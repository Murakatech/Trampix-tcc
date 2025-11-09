<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class FreelancerController extends Controller
{
    public function index()
    {
        Gate::authorize('isAdmin');
        
        $freelancers = Freelancer::with('user')
            ->where('is_active', true)
            ->paginate(15);
            
        return view('freelancers.index', compact('freelancers'));
    }

    public function show(Freelancer $freelancer)
    {
        // Permitir visualização para:
        // 1. O próprio freelancer
        // 2. Administradores
        // 3. Empresas (para visualizar candidatos)
        if (auth()->user()->isAdmin() || 
            (auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id) ||
            auth()->user()->isCompany()) {
            return view('profile.show', [
                'user' => $freelancer->user,
                'freelancer' => $freelancer,
                'company' => null,
            ]);
        }

        abort(403, 'Acesso negado.');
    }

    public function showOwn()
    {
        Gate::authorize('isFreelancer');
        
        $freelancer = auth()->user()->freelancer;
        
        if (!$freelancer) {
            return redirect()->route('profile.selection')
                ->with('info', 'Você precisa criar seu perfil na tela de seleção de perfil.');
        }
        
        return view('profile.show', [
            'user' => $freelancer->user,
            'freelancer' => $freelancer,
            'company' => null,
        ]);
    }

    public function create()
    {
        Gate::authorize('canCreateFreelancerProfile');
        // Redirecionar para a tela unificada de seleção/criação de perfil
        return redirect()->route('profile.selection');
    }

    public function store(Request $request)
    {
        Gate::authorize('canCreateFreelancerProfile');
        
        $validated = $request->validate([
            'display_name' => 'required|string|min:2|max:255',
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'whatsapp' => 'required|string',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'activity_area_id' => 'nullable|integer',
            'service_categories' => 'nullable|array|max:5',
            'service_categories.*' => 'exists:service_categories,id',
        ]);

        // Upload do CV se fornecido
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $validated['cv_url'] = $cvPath;
        }

        // Mapear área de atuação válida (type = freelancer)
        if ($request->filled('activity_area_id')) {
            $areaId = (int) $request->input('activity_area_id');
            $area = \App\Models\ActivityArea::where('id', $areaId)
                ->where('type', 'freelancer')
                ->first();
            $validated['activity_area_id'] = $area?->id; // define somente se existir
        }

        // Sanitizar WhatsApp: manter somente números
        if ($request->filled('whatsapp')) {
            $raw = preg_replace('/\D+/', '', $request->input('whatsapp'));
            // limitar tamanho razoável (DDI 55 + DDD 2 + número 8-9)
            $raw = substr($raw, 0, 14);
            $validated['whatsapp'] = $raw;
        }

        // Criar perfil freelancer
        $freelancer = auth()->user()->createProfile('freelancer', $validated);

        // Sincronizar categorias de serviço se fornecidas
        if (isset($validated['service_categories'])) {
            $validCategories = \App\Models\ServiceCategory::whereIn('id', $validated['service_categories'])
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            $freelancer->serviceCategories()->sync($validCategories);
        }

        // Definir freelancer como perfil ativo
        session(['active_role' => 'freelancer']);

        // Após criar, enviar usuário para a tela de seleção/criação de perfil
        return redirect()->route('profile.selection')
            ->with('success', 'Perfil de freelancer criado com sucesso!');
    }

    public function edit(Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        return view('freelancers.edit', compact('freelancer'));
    }

    public function update(Request $request, Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'remove_cv' => 'nullable|boolean',
            'activity_area_id' => 'nullable|integer',
        ]);

        // Remover CV se solicitado
        if ($request->boolean('remove_cv') && $freelancer->cv_url) {
            Storage::disk('public')->delete($freelancer->cv_url);
            $validated['cv_url'] = null;
        }

        // Upload do novo CV se fornecido
        if ($request->hasFile('cv')) {
            // Remover CV antigo
            if ($freelancer->cv_url) {
                Storage::disk('public')->delete($freelancer->cv_url);
            }
            
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $validated['cv_url'] = $cvPath;
        }

        // Mapear área de atuação válida (type = freelancer)
        if ($request->filled('activity_area_id')) {
            $areaId = (int) $request->input('activity_area_id');
            $area = \App\Models\ActivityArea::where('id', $areaId)
                ->where('type', 'freelancer')
                ->first();
            $validated['activity_area_id'] = $area?->id; // define somente se existir
        } else {
            // permitir limpar o campo
            $validated['activity_area_id'] = null;
        }

        // Sanitizar WhatsApp em atualizações: manter somente números e limitar tamanho
        if ($request->filled('whatsapp')) {
            $raw = preg_replace('/\D+/', '', $request->input('whatsapp'));
            $raw = substr($raw, 0, 14);
            $validated['whatsapp'] = $raw;
        }

        $freelancer->update($validated);

        return redirect()->route('freelancers.edit', $freelancer)
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(Freelancer $freelancer)
    {
        Gate::authorize('editFreelancerProfile', $freelancer);
        
        // Remover CV do storage
        if ($freelancer->cv_url) {
            Storage::disk('public')->delete($freelancer->cv_url);
        }

        // Soft delete - marcar como inativo
        $freelancer->update(['is_active' => false]);

        return redirect()->route('profile.edit')
            ->with('success', 'Perfil de freelancer desativado com sucesso!');
    }

    public function downloadCv(Freelancer $freelancer)
    {
        // Autorizações consistentes com a visualização do perfil:
        // 1. Administradores
        // 2. O próprio freelancer dono do CV
        // 3. Usuários empresa (podem acessar CVs de candidatos)
        if (!(auth()->user()->isAdmin() ||
            (auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id) ||
            auth()->user()->isCompany())) {
            abort(403, 'Acesso negado.');
        }

        if (!$freelancer->cv_url || !Storage::disk('public')->exists($freelancer->cv_url)) {
            abort(404, 'CV não encontrado');
        }

        return Storage::disk('public')->download($freelancer->cv_url, 'CV_' . $freelancer->user->name . '.pdf');
    }
}
