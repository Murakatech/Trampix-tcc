<?php

namespace App\Http\Controllers;

use App\Models\Company;
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

    public function show()
    {
        Gate::authorize('isCompany');
        
        $company = auth()->user()->company;
        
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('info', 'Você precisa criar um perfil de empresa primeiro.');
        }
        
        return view('companies.show', compact('company'));
    }

    public function create()
    {
        Gate::authorize('canCreateCompanyProfile');
        
        return view('companies.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('canCreateCompanyProfile');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:companies,cnpj',
            'sector' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'employees_count' => 'nullable|integer|min:1|max:999999',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ]);

        // Criar perfil empresa
        $company = auth()->user()->createProfile('company', $validated);

        return redirect()->route('companies.edit', $company)
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
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('companies', 'cnpj')->ignore($company->id)
            ],
            'sector' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'employees_count' => 'nullable|integer|min:1|max:999999',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
        ]);

        $company->update($validated);

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

    public function vacancies(Company $company)
    {
        Gate::authorize('editCompanyProfile', $company);
        
        $vacancies = $company->vacancies()
            ->with('applications.freelancer.user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('companies.vacancies', compact('company', 'vacancies'));
    }
}
