<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
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
        
        // Carregar perfis ativos
        $freelancerProfile = $user->freelancer();
        $companyProfile = $user->company();
        
        return view('profile.edit', [
            'user' => $user,
            'freelancerProfile' => $freelancerProfile,
            'companyProfile' => $companyProfile,
            'canCreateFreelancer' => !$freelancerProfile && auth()->user()->can('canCreateFreelancerProfile'),
            'canCreateCompany' => !$companyProfile && auth()->user()->can('canCreateCompanyProfile'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
