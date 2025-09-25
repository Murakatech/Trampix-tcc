<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Freelancer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:freelancer,company'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->user_type,
        ]);

        // Criar perfil automaticamente baseado no tipo
        if ($request->user_type === 'company') {
            Company::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'cnpj' => '',
                'sector' => '',
                'location' => '',
                'description' => '',
            ]);
        } elseif ($request->user_type === 'freelancer') {
            Freelancer::create([
                'user_id' => $user->id,
                'bio' => '',
                'portfolio_url' => '',
                'cv_url' => '',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
