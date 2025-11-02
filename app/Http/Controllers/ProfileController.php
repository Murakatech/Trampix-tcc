<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Exibe a tela unificada de perfil para qualquer usuário
     */
    public function show(User $user): View
    {
        $freelancer = $user->freelancer;
        $company = $user->company;

        // Determinar perfil ativo baseado na sessão (para o próprio usuário) ou disponibilidade
        $activeRole = null;
        if (auth()->check() && auth()->id() === $user->id) {
            $activeRole = session('active_role');
        } else {
            // Para visualização externa, priorizar empresa se disponível
            if ($company) {
                $activeRole = 'company';
            } elseif ($freelancer) {
                $activeRole = 'freelancer';
            }
        }

        // Carregar vagas recentes apenas se o perfil ativo for empresa
        if ($activeRole === 'company' && $company) {
            $company->load(['vacancies' => function($query) {
                $query->where('status', 'active')->latest()->take(5);
            }]);
        }

        return view('profile.show', [
            'user' => $user,
            'freelancer' => $freelancer,
            'company' => $company,
        ]);
    }

    /**
     * Display the freelancer profile creation form.
     */
    public function showCreateFreelancer(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil freelancer
        if ($user->freelancer) {
            return redirect()->route('dashboard')->with('error', 'Você já possui um perfil de freelancer.');
        }
        
        return view('freelancers.create');
    }

    /**
     * Display the company profile creation form.
     */
    public function showCreateCompany(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil empresa
        if ($user->company) {
            return redirect()->route('dashboard')->with('error', 'Você já possui um perfil de empresa.');
        }
        
        return view('companies.create');
    }

    /**
     * Display the account settings form.
     */
    public function account(Request $request): View
    {
        return view('profile.account', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile form based on active role.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $activeRole = session('active_role');
        
        // Se não há active_role definido, redirecionar para dashboard
        if (!$activeRole) {
            return redirect()->route('dashboard');
        }
        
        // Carregar ambos os perfis para exibição correta na view
        $freelancer = $user->freelancer;
        $company = $user->company;
        
        // Carregar categorias se o freelancer existir
        if ($freelancer) {
            $freelancer->load('serviceCategories');
        }
        
        // Carregar categorias se a empresa existir
        if ($company) {
            $company->load('serviceCategories');
        }
        
        // Determinar o perfil ativo
        $profile = null;
        if ($activeRole === 'freelancer') {
            $profile = $freelancer;
        } elseif ($activeRole === 'company') {
            $profile = $company;
        }
        
        return view('profile.edit', [
            'user' => $user,
            'activeRole' => $activeRole,
            'profile' => $profile,
            'freelancer' => $freelancer,
            'company' => $company,
            'hasFreelancer' => $user->isFreelancer(),
            'hasCompany' => $user->isCompany(),
        ]);
    }

    /**
     * Update the user's account information.
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate(\App\Http\Requests\AccountUpdateRequest::rulesFor($user->id));

        $user->fill($validated);
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile information based on active role.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $activeRole = session('active_role');
        
        // Verificar se é criação de novo perfil via modal
        if ($request->has('create_company_profile')) {
            return $this->createCompanyProfile($request);
        }
        
        if ($request->has('create_freelancer_profile')) {
            return $this->createFreelancerProfile($request);
        }
        
        if (!$activeRole) {
            return redirect()->route('dashboard');
        }

        if ($activeRole === 'freelancer') {
            // Autorizações: criar/editar
            $freelancer = $user->freelancer;
            if ($freelancer) {
                // Usar Policy padrão para atualização
                $this->authorize('update', $freelancer);
            } else {
                $this->authorize('canCreateFreelancerProfile');
            }

            $validated = $request->validate(\App\Http\Requests\FreelancerUpdateRequest::rulesFor($freelancer?->id));

            // Upload/remoção de CV
            if ($freelancer) {
                if ($request->boolean('remove_cv') && $freelancer->cv_url) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_url);
                    $validated['cv_url'] = null;
                }
                if ($request->hasFile('cv')) {
                    if ($freelancer->cv_url) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_url);
                    }
                    $cvPath = $request->file('cv')->store('cvs', 'public');
                    $validated['cv_url'] = $cvPath;
                }
            } else {
                if ($request->hasFile('cv')) {
                    $cvPath = $request->file('cv')->store('cvs', 'public');
                    $validated['cv_url'] = $cvPath;
                }
            }

            if ($freelancer) {
                $freelancer->update($validated);
            } else {
                $freelancer = $user->createProfile('freelancer', $validated);
            }

            // Processar categorias de serviços
            if ($request->has('service_categories')) {
                $categoryIds = $request->input('service_categories', []);
                // Validar que as categorias existem e estão ativas
                $validCategoryIds = \App\Models\ServiceCategory::whereIn('id', $categoryIds)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->toArray();
                
                $freelancer->serviceCategories()->sync($validCategoryIds);
            } else {
                // Se nenhuma categoria foi selecionada, remover todas
                $freelancer->serviceCategories()->detach();
            }

            return Redirect::route('profile.edit')->with('success', 'Perfil de freelancer atualizado com sucesso!');
        }

        if ($activeRole === 'company') {
            $company = $user->company;
            if ($company) {
                // Usar Policy padrão para atualização
                $this->authorize('update', $company);
            } else {
                $this->authorize('canCreateCompanyProfile');
            }

            // Preparar dados para validação, tratando URLs inválidas
            $requestData = $request->all();
            
            // Se o website existe mas é inválido, remover para evitar erro de validação
            if (isset($requestData['website']) && !empty($requestData['website'])) {
                if (!filter_var($requestData['website'], FILTER_VALIDATE_URL)) {
                    $requestData['website'] = null;
                }
            }
            
            // Criar um novo request com os dados tratados
            $request->merge($requestData);

            $validated = $request->validate(\App\Http\Requests\CompanyUpdateRequest::rulesFor($company?->id));

            // Mapear display_name para name
            if (isset($validated['display_name'])) {
                $validated['name'] = $validated['display_name'];
            }

            if ($company) {
                $company->update($validated);
            } else {
                $company = $user->createProfile('company', $validated);
            }

            // Sincronizar categorias de serviço se fornecidas
            if ($request->has('service_categories')) {
                $categoryIds = $request->input('service_categories', []);
                $validCategories = \App\Models\ServiceCategory::whereIn('id', $categoryIds)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->toArray();
                
                $company->serviceCategories()->sync($validCategories);
            } else {
                // Se nenhuma categoria foi selecionada, remover todas
                $company->serviceCategories()->detach();
            }

            return Redirect::route('profile.edit')->with('success', 'Perfil de empresa atualizado com sucesso!');
        }

        // Fallback
        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Create a new company profile from profile selection.
     */
    public function createCompany(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil empresa
        if ($user->company) {
            return redirect()->route('dashboard')->with('error', 'Você já possui um perfil de empresa.');
        }
        
        $validated = $request->validate([
            'company_name' => 'required|string|min:3|max:255',
            'cnpj' => 'required|string|size:18', // Com máscara: 00.000.000/0000-00
            'description' => 'required|string|min:30|max:1000',
            'sector' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:5120', // 5MB
        ]);
        
        // Processar upload do logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company_logos', 'public');
            $validated['logo'] = $logoPath;
        }
        
        // Atualizar nome do usuário
        $user->update([
            'name' => $validated['company_name'],
            'role' => 'company'
        ]);
        
        // Criar perfil empresa
        $company = $user->createProfile('company', [
            'name' => $validated['company_name'],
            'cnpj' => $validated['cnpj'],
            'description' => $validated['description'],
            'sector' => $validated['sector'],
            'website' => $validated['website'] ?? null,
            'logo' => $validated['logo'] ?? null,
        ]);
        
        // Definir empresa como perfil ativo
        session(['active_role' => 'company']);
        
        return redirect()->route('dashboard')->with('success', 'Perfil de empresa criado com sucesso!');
    }

    /**
     * Create a new freelancer profile from profile selection.
     */
    public function createFreelancer(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil freelancer
        if ($user->freelancer) {
            return redirect()->route('dashboard')->with('error', 'Você já possui um perfil de freelancer.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'title' => 'required|string|max:255',
            'bio' => 'required|string|min:50|max:1000',
            'skills' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'cv' => 'nullable|file|mimes:pdf|max:10240', // 10MB
        ]);
        
        // Processar uploads
        $profileData = [
            'bio' => $validated['bio'],
            'title' => $validated['title'],
            'skills' => $validated['skills'],
        ];
        
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
            $profileData['profile_photo'] = $photoPath;
        }
        
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
            $profileData['cv_url'] = $cvPath;
        }
        
        // Atualizar nome do usuário
        $user->update([
            'name' => $validated['name'],
            'role' => 'freelancer'
        ]);
        
        // Criar perfil freelancer
        $freelancer = $user->createProfile('freelancer', $profileData);
        
        // Definir freelancer como perfil ativo
        session(['active_role' => 'freelancer']);
        
        return redirect()->route('dashboard')->with('success', 'Perfil de freelancer criado com sucesso!');
    }

    /**
     * Create a new company profile for the user.
     */
    private function createCompanyProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil empresa
        if ($user->company) {
            return redirect()->route('profile.edit')->with('error', 'Você já possui um perfil de empresa.');
        }
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
        ]);
        
        // Criar perfil empresa
        $company = $user->createProfile('company', $validated);
        
        // Definir empresa como perfil ativo
        session(['active_role' => 'company']);
        
        return redirect()->route('profile.edit')->with('success', 'Perfil de empresa criado com sucesso!');
    }

    /**
     * Create a new freelancer profile for the user.
     */
    private function createFreelancerProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Verificar se já tem perfil freelancer
        if ($user->freelancer) {
            return redirect()->route('profile.edit')->with('error', 'Você já possui um perfil de freelancer.');
        }
        
        $validated = $request->validate([
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'hourly_rate' => 'nullable|numeric|min:0|max:9999.99',
        ]);
        
        // Criar perfil freelancer
        $freelancer = $user->createProfile('freelancer', $validated);
        
        // Definir freelancer como perfil ativo
        session(['active_role' => 'freelancer']);
        
        return redirect()->route('profile.edit')->with('success', 'Perfil de freelancer criado com sucesso!');
    }

    /**
     * Update profile image (freelancer photo or company logo).
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profileType = $request->input('profile_type');
        
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'profile_type' => 'required|in:freelancer,company'
        ]);
        
        if ($profileType === 'freelancer') {
            $freelancer = $user->freelancer;
            if (!$freelancer) {
                return redirect()->route('profile.edit')->with('error', 'Perfil de freelancer não encontrado.');
            }
            
            // Remove imagem anterior se existir
            if ($freelancer->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->profile_photo);
            }
            
            // Upload nova imagem
            $imagePath = $request->file('profile_image')->store('profile_photos', 'public');
            $freelancer->update(['profile_photo' => $imagePath]);
            
            return redirect()->route('profile.edit')->with('success', 'Foto de perfil atualizada com sucesso!');
        }
        
        if ($profileType === 'company') {
            $company = $user->company;
            if (!$company) {
                return redirect()->route('profile.edit')->with('error', 'Perfil de empresa não encontrado.');
            }
            
            // Remove logo anterior se existir
            if ($company->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($company->profile_photo);
            }
            
            // Upload novo logo
            $logoPath = $request->file('profile_image')->store('profile_photos', 'public');
            $company->update(['profile_photo' => $logoPath]);
            
            return redirect()->route('profile.edit')->with('success', 'Logo da empresa atualizado com sucesso!');
        }
        
        return redirect()->route('profile.edit')->with('error', 'Tipo de perfil inválido.');
    }

    /**
     * Delete profile image (freelancer photo or company logo).
     */
    public function deleteImage(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profileType = $request->input('profile_type');
        
        $request->validate([
            'profile_type' => 'required|in:freelancer,company'
        ]);
        
        if ($profileType === 'freelancer') {
            $freelancer = $user->freelancer;
            if (!$freelancer || !$freelancer->profile_photo) {
                return redirect()->route('profile.edit')->with('error', 'Nenhuma foto de perfil encontrada.');
            }
            
            // Remove arquivo do storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->profile_photo);
            
            // Remove referência do banco
            $freelancer->update(['profile_photo' => null]);
            
            return redirect()->route('profile.edit')->with('success', 'Foto de perfil removida com sucesso!');
        }
        
        if ($profileType === 'company') {
            $company = $user->company;
            if (!$company || !$company->profile_photo) {
                return redirect()->route('profile.edit')->with('error', 'Nenhum logo encontrado.');
            }
            
            // Remove arquivo do storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($company->profile_photo);
            
            // Remove referência do banco
            $company->update(['profile_photo' => null]);
            
            return redirect()->route('profile.edit')->with('success', 'Logo da empresa removido com sucesso!');
        }
        
        return redirect()->route('profile.edit')->with('error', 'Tipo de perfil inválido.');
    }

    /**
     * Upload de currículo para freelancer
     */
    public function uploadCv(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se o usuário tem perfil de freelancer
        if (!$user->freelancer) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas freelancers podem fazer upload de currículo.'
            ], 403);
        }

        // Validar o arquivo
        $request->validate([
            'cv' => 'required|mimes:pdf,doc,docx|max:10240', // 10MB máximo
        ]);

        $freelancer = $user->freelancer;

        try {
            // Remover currículo anterior se existir
            if ($freelancer->cv_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_path);
            }

            // Salvar novo currículo
            $cvPath = $request->file('cv')->store('cvs', 'public');
            
            // Atualizar no banco de dados
            $freelancer->update(['cv_path' => $cvPath]);

            return response()->json([
                'success' => true,
                'message' => 'Currículo enviado com sucesso!',
                'cv_path' => $cvPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload do currículo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar currículo do freelancer
     */
    public function deleteCv(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se o usuário tem perfil de freelancer
        if (!$user->freelancer) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas freelancers podem remover currículo.'
            ], 403);
        }

        $freelancer = $user->freelancer;

        try {
            // Verificar se existe currículo
            if (!$freelancer->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum currículo encontrado.'
                ], 404);
            }

            // Remover arquivo do storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_path);
            
            // Remover referência do banco
            $freelancer->update(['cv_path' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Currículo removido com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover currículo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Switch between user roles (freelancer/company)
     */
    public function switchRole(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:freelancer,company'
        ]);

        $user = auth()->user();
        $targetRole = $request->input('role');

        // Verificar se o usuário tem o perfil solicitado
        if ($targetRole === 'freelancer' && !$user->freelancer) {
            return redirect()->back()->with('error', 'Você não possui um perfil de freelancer.');
        }

        if ($targetRole === 'company' && !$user->company) {
            return redirect()->back()->with('error', 'Você não possui um perfil de empresa.');
        }

        // Atualizar a sessão com o novo papel ativo
        session(['active_role' => $targetRole]);

        return redirect()->back()->with('success', 'Perfil alterado com sucesso!');
    }

    /**
     * Delete the user's freelancer profile.
     */
    public function destroyFreelancerProfile(Request $request): RedirectResponse
    {
        $request->validateWithBag('freelancerDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Verificar se o usuário tem perfil de freelancer
        if (!$user->freelancer) {
            return redirect()->back()->with('error', 'Você não possui um perfil de freelancer.');
        }

        $freelancer = $user->freelancer;

        // Verificar se há candidaturas pendentes
        if ($freelancer->applications()->whereIn('status', ['pending', 'reviewing'])->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir o perfil. Há candidaturas pendentes.');
        }

        // Remover CV do storage se existir
        if ($freelancer->cv_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_path);
        }

        // Remover foto de perfil se existir
        if ($freelancer->profile_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->profile_photo);
        }

        // Excluir o perfil
        $freelancer->delete();

        // Se o perfil ativo era freelancer, limpar a sessão
        if (session('active_role') === 'freelancer') {
            session()->forget('active_role');
        }

        return redirect()->route('profile.account')->with('success', 'Perfil de freelancer excluído com sucesso!');
    }

    /**
     * Delete the user's company profile.
     */
    public function destroyCompanyProfile(Request $request): RedirectResponse
    {
        $request->validateWithBag('companyDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Verificar se o usuário tem perfil de empresa
        if (!$user->company) {
            return redirect()->back()->with('error', 'Você não possui um perfil de empresa.');
        }

        $company = $user->company;

        // Verificar se há vagas ativas
        if ($company->vacancies()->where('status', 'active')->exists()) {
            return redirect()->back()->with('error', 'Não é possível excluir o perfil. Há vagas ativas vinculadas a esta empresa.');
        }

        // Verificar se há candidaturas pendentes em vagas da empresa
        $pendingApplications = \App\Models\Application::whereHas('vacancy', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->whereIn('status', ['pending', 'reviewing'])->exists();

        if ($pendingApplications) {
            return redirect()->back()->with('error', 'Não é possível excluir o perfil. Há candidaturas pendentes em suas vagas.');
        }

        // Remover foto de perfil se existir
        if ($company->profile_photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($company->profile_photo);
        }

        // Excluir o perfil
        $company->delete();

        // Se o perfil ativo era empresa, limpar a sessão
        if (session('active_role') === 'company') {
            session()->forget('active_role');
        }

        return redirect()->route('profile.account')->with('success', 'Perfil de empresa excluído com sucesso!');
    }

    /**
     * Retorna dados do perfil freelancer em formato JSON
     */
    public function showFreelancerProfile(Request $request)
    {
        $user = auth()->user();
        
        // Verificar se o usuário está autenticado
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        // Verificar se o usuário tem perfil de freelancer
        if (!$user->freelancer) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de freelancer não encontrado.'
            ], 404);
        }

        $freelancer = $user->freelancer;

        // Carregar estatísticas de candidaturas
        $applications = \App\Models\Application::where('freelancer_id', $freelancer->id);
        $applicationsStats = [
            'total' => $applications->count(),
            'pending' => $applications->where('status', 'pending')->count(),
            'accepted' => $applications->where('status', 'accepted')->count(),
            'rejected' => $applications->where('status', 'rejected')->count(),
        ];

        // Dados do perfil freelancer
        $profileData = [
            'success' => true,
            'data' => [
                'id' => $freelancer->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'profile' => [
                    'bio' => $freelancer->bio,
                    'skills' => $freelancer->skills,
                    'experience' => $freelancer->experience,
                    'hourly_rate' => $freelancer->hourly_rate,
                    'availability' => $freelancer->availability,
                    'profile_photo' => $freelancer->profile_photo ? asset('storage/' . $freelancer->profile_photo) : null,
                    'cv_path' => $freelancer->cv_path ? asset('storage/' . $freelancer->cv_path) : null,
                    'portfolio_url' => $freelancer->portfolio_url,
                    'linkedin_url' => $freelancer->linkedin_url,
                    'github_url' => $freelancer->github_url,
                ],
                'statistics' => $applicationsStats,
                'created_at' => $freelancer->created_at,
                'updated_at' => $freelancer->updated_at,
            ]
        ];

        return response()->json($profileData, 200);
    }
}
