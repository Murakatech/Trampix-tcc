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

        // Carregar vagas recentes da empresa (se houver)
        if ($company) {
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
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Carregar perfis (hasOne)
        $freelancer = $user->freelancer;
        $company = $user->company;
        
        return view('profile.edit', [
            'user' => $user,
            'freelancer' => $freelancer,
            'company' => $company,
            'canCreateFreelancer' => !$freelancer && auth()->user()->can('canCreateFreelancerProfile'),
            'canCreateCompany' => !$company && auth()->user()->can('canCreateCompanyProfile'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $section = $request->input('section', 'account');

        if ($section === 'account') {
            $validated = $request->validate(\App\Http\Requests\AccountUpdateRequest::rulesFor($user->id));

            $user->fill($validated);
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->save();

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }

        if ($section === 'freelancer') {
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

            return Redirect::route('profile.edit')->with('success', 'Perfil de freelancer atualizado com sucesso!');
        }

        if ($section === 'company') {
            $company = $user->company;
            if ($company) {
                // Usar Policy padrão para atualização
                $this->authorize('update', $company);
            } else {
                $this->authorize('canCreateCompanyProfile');
            }

            $validated = $request->validate(\App\Http\Requests\CompanyUpdateRequest::rulesFor($company?->id));

            if ($company) {
                $company->update($validated);
            } else {
                $company = $user->createProfile('company', $validated);
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
}
