<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Application;
use App\Models\Segment;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
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
            // Perfil próprio: permitir alternância via querystring e persistir na sessão
            $requestedRole = request()->query('role');
            if (in_array($requestedRole, ['freelancer', 'company'])) {
                if ($requestedRole === 'company' && $company) {
                    session(['active_role' => 'company']);
                    $activeRole = 'company';
                } elseif ($requestedRole === 'freelancer' && $freelancer) {
                    session(['active_role' => 'freelancer']);
                    $activeRole = 'freelancer';
                }
            }

            // Caso não tenha sido solicitado troca, usar valor atual da sessão
            $activeRole = $activeRole ?? session('active_role');
        } else {
            // Para visualização externa, priorizar empresa se disponível
            if ($company) {
                $activeRole = 'company';
            } elseif ($freelancer) {
                $activeRole = 'freelancer';
            }
        }

        // Métricas de avaliação pública (média) para exibição no perfil
        $companyPublicRatingAvg = null;
        $companyPublicRatingCount = 0;
        $freelancerPublicRatingAvg = null;
        $freelancerPublicRatingCount = 0;

        if ($company) {
            // Média das avaliações feitas PELOS freelancers sobre a empresa
            $companyRatingsQuery = Application::query()
                ->whereHas('jobVacancy', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })
                ->where('status', 'ended')
                ->whereNotNull('freelancer_rating_avg');

            $companyPublicRatingCount = (int) $companyRatingsQuery->count();
            if ($companyPublicRatingCount > 0) {
                $companyPublicRatingAvg = round((float) $companyRatingsQuery->avg('freelancer_rating_avg'), 1);
            }
        }

        if ($freelancer) {
            // Média das avaliações feitas PELAS empresas sobre o freelancer
            $freelancerRatingsQuery = Application::query()
                ->where('freelancer_id', $freelancer->id)
                ->where('status', 'ended')
                ->whereNotNull('company_rating_avg');

            $freelancerPublicRatingCount = (int) $freelancerRatingsQuery->count();
            if ($freelancerPublicRatingCount > 0) {
                $freelancerPublicRatingAvg = round((float) $freelancerRatingsQuery->avg('company_rating_avg'), 1);
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
            // Avaliações públicas
            'companyPublicRatingAvg' => $companyPublicRatingAvg,
            'companyPublicRatingCount' => $companyPublicRatingCount,
            'freelancerPublicRatingAvg' => $freelancerPublicRatingAvg,
            'freelancerPublicRatingCount' => $freelancerPublicRatingCount,
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
        // Direcionar para a tela unificada de seleção/criação de perfil
        return redirect()->route('profile.selection');
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
        // Direcionar para a tela unificada de seleção/criação de perfil
        return redirect()->route('profile.selection');
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
        $hasFreelancer = (bool) $user->freelancer;
        $hasCompany = (bool) $user->company;

        // Se não há active_role definido, decidir comportamento conforme perfis existentes
        if (!$activeRole) {
            // Sem perfis: encaminhar para seleção/criação de perfil
            if (!$hasFreelancer && !$hasCompany) {
                return redirect()->route('profile.selection');
            }

            // Apenas um perfil: definir automaticamente e seguir
            if ($hasFreelancer && !$hasCompany) {
                session(['active_role' => 'freelancer']);
                $activeRole = 'freelancer';
            } elseif ($hasCompany && !$hasFreelancer) {
                session(['active_role' => 'company']);
                $activeRole = 'company';
            } else {
                // Ambos os perfis: exibir tela de seleção
                return redirect()->route('select-role.show');
            }
        }
        
        // Carregar ambos os perfis para exibição correta na view
        $freelancer = $user->freelancer;
        $company = $user->company;
        
        // Carregar categorias e segmentos se o freelancer existir
        if ($freelancer) {
            $freelancer->load(['serviceCategories', 'segments']);
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
        
        // Carregar lista de segmentos para seleção (apenas uma vez)
        $segments = Segment::where('active', true)->orderBy('name')->get();

        return view('profile.edit', [
            'user' => $user,
            'activeRole' => $activeRole,
            'profile' => $profile,
            'freelancer' => $freelancer,
            'company' => $company,
            'hasFreelancer' => $user->isFreelancer(),
            'hasCompany' => $user->isCompany(),
            'segments' => $segments,
        ]);
    }

    /**
     * Update the user's account information.
     */
    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Bloquear edição da conta para administradores
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('error', 'A conta do administrador não pode ser editada.');
        }
        $validated = $request->validate(\App\Http\Requests\AccountUpdateRequest::rulesFor($user->id));

        try {
            $user->fill($validated);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();
        } catch (\Throwable $e) {
            // Não capturar erros de validação novamente
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                throw $e;
            }
            Log::error('Erro ao atualizar conta do usuário', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return Redirect::route('profile.edit')->with('error', 'Não foi possível atualizar a conta no momento. Tente novamente mais tarde.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile information based on active role.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $section = $request->input('section');
        $activeRole = session('active_role');
        
        // Verificar se é criação de novo perfil via modal
        if ($request->has('create_company_profile')) {
            return $this->createCompanyProfile($request);
        }
        
        if ($request->has('create_freelancer_profile')) {
            return $this->createFreelancerProfile($request);
        }
        
        // Se é uma atualização de informações básicas da conta
        // Caso section seja 'account' OU (section ausente e vierem campos de conta)
        if ($section === 'account' || (!$section && $request->hasAny(['name', 'email']))) {
            // Bloquear edição da conta para administradores
            if ($user->isAdmin()) {
                return Redirect::route('admin.dashboard')->with('error', 'A conta do administrador não pode ser editada.');
            }
            $validated = $request->validate(\App\Http\Requests\AccountUpdateRequest::rulesFor($user->id));

            $user->fill($validated);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }
        
        // Se a section é especificada, usar ela ao invés do activeRole
        $targetRole = $section ?: $activeRole;
        
        // Fallback robusto: deduzir pelo que o usuário possui caso a sessão/section esteja ausente
        if (!$targetRole) {
            if ($user->freelancer) {
                $targetRole = 'freelancer';
            } elseif ($user->company) {
                $targetRole = 'company';
            }
        }
        
        if (!$targetRole) {
            return redirect()->route('dashboard');
        }

        if ($targetRole === 'freelancer') {
            // Autorizações: criar/editar
            $freelancer = $user->freelancer;
            if ($freelancer) {
                // Usar Policy padrão para atualização
                $this->authorize('update', $freelancer);
            } else {
                $this->authorize('canCreateFreelancerProfile');
            }

            $validated = $request->validate(\App\Http\Requests\FreelancerUpdateRequest::rulesFor($freelancer?->id));

            // Sanitizar WhatsApp se enviado: manter somente números e limitar tamanho
            if ($request->filled('whatsapp')) {
                $rawWhatsapp = preg_replace('/\D+/', '', $request->input('whatsapp'));
                $validated['whatsapp'] = substr($rawWhatsapp, 0, 14);
            }

            // Upload/remoção de CV
            if ($freelancer) {
                if ($request->boolean('remove_cv') && $freelancer->cv_url) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_url);
                    $validated['cv_url'] = null;
                } elseif ($request->hasFile('cv')) {
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

            try {
                if ($freelancer) {
                    // Garantir atualização explícita do display_name
                    $freelancer->fill($validated);
                    if ($request->has('display_name')) {
                        $freelancer->display_name = $request->input('display_name');
                    }
                    $freelancer->save();
                } else {
                    $freelancer = $user->createProfile('freelancer', $validated);
                }
            } catch (\Throwable $e) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    throw $e;
                }
                Log::error('Erro ao atualizar perfil freelancer', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                return Redirect::route('profile.edit')->with('error', 'Não foi possível atualizar o perfil de freelancer. Tente novamente mais tarde.');
            }

            // Atualizar área de atuação (ActivityArea) para freelancer
            // Atenção: não limpar automaticamente se o campo não vier na requisição
            if ($request->filled('activity_area_id')) {
                $areaId = (int) $request->input('activity_area_id');
                $area = \App\Models\ActivityArea::where('id', $areaId)
                    ->where('type', 'freelancer')
                    ->first();
                $freelancer->activity_area_id = $area?->id; // só define se for válida
                $freelancer->save();
            } elseif ($request->boolean('clear_activity_area')) {
                // Limpar explicitamente se solicitado
                $freelancer->activity_area_id = null;
                $freelancer->save();
            }

            // Removido: processamento de Categorias de Serviços do freelancer

            // Processar segmentos do freelancer (múltipla seleção)
            if ($request->has('segments')) {
                $segmentIds = $request->input('segments', []);
                $validSegmentIds = Segment::whereIn('id', $segmentIds)
                    ->pluck('id')
                    ->toArray();
                $freelancer->segments()->sync($validSegmentIds);
            } elseif ($request->boolean('clear_segments')) {
                // Remover segmentos apenas se explicitamente solicitado
                $freelancer->segments()->detach();
            }

            // Removido: segmento principal único do freelancer (segment_id)

            // Garantir que o papel ativo permaneça como freelancer após salvar
            session(['active_role' => 'freelancer']);

            return Redirect::route('profile.edit')->with('success', 'Perfil de freelancer atualizado com sucesso!');
        }

        if ($targetRole === 'company') {
            $company = $user->company;
            if ($company) {
                // Usar Policy padrão para atualização
                $this->authorize('update', $company);
            } else {
                $this->authorize('canCreateCompanyProfile');
            }

            // Preparar dados para validação, tratando URLs inválidas
            $requestData = $request->all();
            
            // Mapear name para display_name se name foi enviado
            if (isset($requestData['name']) && !isset($requestData['display_name'])) {
                $requestData['display_name'] = $requestData['name'];
            }
            
            // Não remover website inválido - deixar a validação capturar o erro
            // if (isset($requestData['website']) && !empty($requestData['website'])) {
            //     if (!filter_var($requestData['website'], FILTER_VALIDATE_URL)) {
            //         $requestData['website'] = null;
            //     }
            // }
            
            // Criar um novo request com os dados tratados
            $request->merge($requestData);

            try {
                $validated = $request->validate(\App\Http\Requests\CompanyUpdateRequest::rulesFor($company?->id));
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Mapear erros de display_name para name
                $errors = $e->errors();
                if (isset($errors['display_name'])) {
                    $errors['name'] = $errors['display_name'];
                    unset($errors['display_name']);
                }
                
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }

            // Mapear display_name para name
            if (isset($validated['display_name'])) {
                $validated['name'] = $validated['display_name'];
            }

            try {
                if ($company) {
                    $company->update($validated);
                } else {
                    $company = $user->createProfile('company', $validated);
                }
            } catch (\Throwable $e) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    throw $e;
                }
                Log::error('Erro ao atualizar perfil empresa', ['user_id' => $user->id, 'error' => $e->getMessage()]);
                return Redirect::route('profile.edit')->with('error', 'Não foi possível atualizar o perfil de empresa. Tente novamente mais tarde.');
            }

            // Processar segmentos da empresa (múltipla seleção)
            if ($request->has('segments')) {
                $segmentIds = $request->input('segments', []);
                $validSegmentIds = Segment::whereIn('id', $segmentIds)
                    ->pluck('id')
                    ->toArray();
                $company->segments()->sync($validSegmentIds);
            } else {
                // Se nenhum segmento foi selecionado, remover todos
                $company->segments()->detach();
            }

            // Removido: processamento de Categorias de Serviços da empresa

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
            'cnpj' => 'nullable|string|size:18', // Com máscara: 00.000.000/0000-00
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
            'cnpj' => $validated['cnpj'] ?? null,
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
            'whatsapp' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'cv' => 'nullable|file|mimes:pdf|max:10240', // 10MB
        ]);
        
        // Processar uploads
        $profileData = [
            'bio' => $validated['bio'],
            'title' => $validated['title'],
            'skills' => $validated['skills'],
        ];

        // Sanitizar WhatsApp: manter somente números e limitar tamanho
        if ($request->filled('whatsapp')) {
            $rawWhatsapp = preg_replace('/\D+/', '', $request->input('whatsapp'));
            $profileData['whatsapp'] = substr($rawWhatsapp, 0, 14);
        }
        
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
        // Mapear company_name para o campo name exigido pelo modelo/tabela
        $validated['name'] = $validated['company_name'];
        
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
            // permitir seleção inicial de segmentos
            'segments' => 'nullable|array|max:10',
            'segments.*' => 'exists:segments,id',
        ]);
        
        // Criar perfil freelancer
        $freelancer = $user->createProfile('freelancer', $validated);

        // Sincronizar segmentos se fornecidos
        if ($request->has('segments')) {
            $segmentIds = $request->input('segments', []);
            $validSegmentIds = Segment::whereIn('id', $segmentIds)->pluck('id')->toArray();
            $freelancer->segments()->sync($validSegmentIds);
        }
        
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
            if ($freelancer->cv_url) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_url);
            }

            // Salvar novo currículo
            $cvPath = $request->file('cv')->store('cvs', 'public');
            
            // Atualizar no banco de dados (usar coluna cv_url)
            $freelancer->update(['cv_url' => $cvPath]);

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
            if (!$freelancer->cv_url) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum currículo encontrado.'
                ], 404);
            }

            // Remover arquivo do storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($freelancer->cv_url);
            
            // Remover referência do banco
            $freelancer->update(['cv_url' => null]);

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
