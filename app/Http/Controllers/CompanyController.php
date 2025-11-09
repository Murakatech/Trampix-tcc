<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Segment;
use App\Models\ActivityArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {
        Gate::authorize('isAdmin');
        
        $companies = Company::with('user')
            ->where('is_active', true)
            ->paginate(15);
            
        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        // Verificar se a empresa está ativa
        if (!$company->is_active) {
            abort(404, 'Empresa não encontrada.');
        }
        
        // Carregar relacionamentos necessários
        $company->load(['user', 'vacancies' => function($query) {
            $query->where('status', 'active')->latest()->take(5);
        }]);
        
        // Usar a tela unificada de perfil
        return view('profile.show', [
            'user' => $company->user,
            'company' => $company,
            'freelancer' => null,
        ]);
    }

    public function create()
    {
        Gate::authorize('canCreateCompanyProfile');
        // Redirecionar para a tela unificada de seleção/criação de perfil
        return redirect()->route('profile.selection');
    }

    public function store(Request $request)
    {
        Gate::authorize('canCreateCompanyProfile');
        
        $validated = $request->validate([
            'display_name' => 'required|string|min:2|max:255',
            'cnpj' => 'required|string|max:18|unique:companies,cnpj',
            'sector' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company_size' => ['nullable','string', 'in:1-10,11-50,51-200,201-500,500+'],
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'service_categories' => 'nullable|array',
            'service_categories.*' => 'exists:service_categories,id',
            'activity_area_id' => 'nullable|integer',
            // Permitir seleção de segmentos (máximo 3)
            'segments' => 'nullable|array|max:3',
            'segments.*' => 'exists:segments,id',
        ]);

        // Mapear display_name para name
        $validated['name'] = $validated['display_name'];

        // Validar área de atuação (type = company)
        if ($request->filled('activity_area_id')) {
            $areaId = (int) $request->input('activity_area_id');
            $area = \App\Models\ActivityArea::where('id', $areaId)
                ->where('type', 'company')
                ->first();
            $validated['activity_area_id'] = $area?->id;
        }

        // Criar perfil empresa
        $company = auth()->user()->createProfile('company', $validated);

        // Sincronizar categorias de serviço se fornecidas
        if (isset($validated['service_categories'])) {
            $validCategories = \App\Models\ServiceCategory::whereIn('id', $validated['service_categories'])
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            
            $company->serviceCategories()->sync($validCategories);
        }

        // Sincronizar segmentos se fornecidos
        if ($request->has('segments')) {
            $segmentIds = $request->input('segments', []);
            $validSegmentIds = Segment::whereIn('id', $segmentIds)
                ->pluck('id')
                ->toArray();
            $company->segments()->sync($validSegmentIds);
        }

        // Definir empresa como perfil ativo
        session(['active_role' => 'company']);

        // Redirecionar para o perfil público do próprio usuário, já com a visualização da nova empresa
        return redirect()->route('profiles.show', ['user' => auth()->id(), 'role' => 'company'])
            ->with('success', 'Perfil de empresa criado com sucesso!');
    }

    public function edit(Company $company)
    {
        Gate::authorize('editCompanyProfile', $company);
        
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        Gate::authorize('editCompanyProfile', $company);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // No update, don't force CNPJ if it's not being changed. Allow partial PATCH updates.
            'cnpj' => [
                'sometimes',
                'string',
                'max:18',
                Rule::unique('companies', 'cnpj')->ignore($company->id)
            ],
            'sector' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company_size' => ['nullable','string', 'in:1-10,11-50,51-200,201-500,500+'],
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'service_categories' => 'nullable|array',
            'service_categories.*' => 'exists:service_categories,id',
            'activity_area_id' => 'nullable|integer',
        ]);

        // Validar área de atuação (type = company)
        if ($request->filled('activity_area_id')) {
            $areaId = (int) $request->input('activity_area_id');
            $area = \App\Models\ActivityArea::where('id', $areaId)
                ->where('type', 'company')
                ->first();
            $validated['activity_area_id'] = $area?->id;
        } else {
            $validated['activity_area_id'] = null;
        }

        $company->update($validated);

        // Sincronizar categorias de serviço se fornecidas
        if (isset($validated['service_categories'])) {
            $validCategories = \App\Models\ServiceCategory::whereIn('id', $validated['service_categories'])
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            
            $company->serviceCategories()->sync($validCategories);
        } else {
            // Se não foram fornecidas categorias, remover todas
            $company->serviceCategories()->detach();
        }

        return redirect()->route('companies.edit', $company)
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(Company $company)
    {
        Gate::authorize('editCompanyProfile', $company);
        
        // Verificar se há vagas ativas
        if ($company->vacancies()->where('status', 'active')->exists()) {
            return redirect()->back()
                ->with('error', 'Não é possível desativar o perfil. Há vagas ativas vinculadas a esta empresa.');
        }

        // Soft delete - marcar como inativo
        $company->update(['is_active' => false]);

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil de empresa desativado com sucesso!');
    }

    // Método legada removido: vagas por empresa
}
