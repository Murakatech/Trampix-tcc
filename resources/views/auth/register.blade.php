<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="trampix-h1 text-gray-900">Criar Conta</h1>
        <p class="mt-2 text-sm text-gray-600">
            Crie sua conta para acessar a plataforma Trampix
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registerForm" x-data="{ 
        name: '',
        email: '', 
        password: '', 
        password_confirmation: '',
        nameValid: false,
        emailValid: false,
        passwordValid: false,
        hasSpecial: false,
        hasUppercase: false,
        passwordMatch: false,
        showPasswordRequirements: false
    }">
        @csrf

        <!-- Name -->
        <div class="mb-6">
            <x-input-label for="name" :value="__('Nome Completo')" />
            <x-text-input 
                id="name" 
                class="trampix-input block mt-1 w-full" 
                type="text" 
                name="name" 
                x-model="name"
                @input="nameValid = name.trim().length >= 2"
                :value="old('name')" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Seu nome completo" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div x-show="name && !nameValid" class="mt-1 text-sm text-red-600">
                Por favor, insira seu nome completo (mínimo 2 caracteres)
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input 
                id="email" 
                class="trampix-input block mt-1 w-full" 
                type="email" 
                name="email" 
                x-model="email"
                @input="emailValid = $el.validity.valid"
                :value="old('email')" 
                required 
                autocomplete="username"
                placeholder="seu@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div x-show="email && !emailValid" class="mt-1 text-sm text-red-600">
                Por favor, insira um email válido
            </div>
        </div>

        <!-- Password -->
        <div class="mb-6">
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input 
                id="password" 
                class="trampix-input block mt-1 w-full"
                type="password"
                name="password"
                x-model="password"
                @focus="showPasswordRequirements = true"
                @input="passwordValid = password.length >= 8; hasSpecial = /[^A-Za-z0-9]/.test(password); hasUppercase = /[A-Z]/.test(password)"
                required 
                autocomplete="new-password"
                placeholder="Mínimo 8 caracteres" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            
            <!-- Requisitos da senha -->
            <div x-show="showPasswordRequirements" x-transition class="mt-2 text-sm">
                <div class="flex items-center space-x-2" :class="passwordValid ? 'text-green-600' : 'text-gray-500'">
                    <svg class="w-4 h-4" :class="passwordValid ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Mínimo 8 caracteres</span>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />
            <x-text-input 
                id="password_confirmation" 
                class="trampix-input block mt-1 w-full"
                type="password"
                name="password_confirmation" 
                x-model="password_confirmation"
                @input="passwordMatch = password === password_confirmation && password.length > 0"
                required 
                autocomplete="new-password"
                placeholder="Digite a senha novamente" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            
            <div x-show="password_confirmation && !passwordMatch" class="mt-1 text-sm text-red-600">
                As senhas não coincidem
            </div>
            <div x-show="passwordMatch && password_confirmation" class="mt-1 text-sm text-green-600">
                ✓ Senhas coincidem
            </div>

            <!-- Requisitos da senha (abaixo da confirmação) -->
            <div x-show="password || password_confirmation" class="mt-3 text-sm space-y-1">
                <div class="flex items-center space-x-2" :class="passwordValid ? 'text-green-600' : 'text-gray-500'">
                    <svg class="w-4 h-4" :class="passwordValid ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div class="flex items-center space-x-2" :class="hasSpecial ? 'text-green-600' : 'text-gray-500'">
                    <svg class="w-4 h-4" :class="hasSpecial ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pelo menos 1 caractere especial (!@#$...)</span>
                </div>
                <div class="flex items-center space-x-2" :class="hasUppercase ? 'text-green-600' : 'text-gray-500'">
                    <svg class="w-4 h-4" :class="hasUppercase ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pelo menos 1 letra maiúscula (A-Z)</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col space-y-4">
            <button 
                type="submit" 
                class="btn-trampix-primary w-full" 
                id="submitBtn"
                :disabled="!nameValid || !emailValid || !passwordValid || !passwordMatch"
                :class="{ 'opacity-50 cursor-not-allowed': !nameValid || !emailValid || !passwordValid || !passwordMatch }">
                <span id="btnText">{{ __('Criar Conta') }}</span>
                <div id="spinner" class="hidden">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </button>

            <div class="text-center">
                <a class="text-sm text-gray-600 hover:text-gray-900 underline transition-colors duration-200" href="{{ route('login') }}">
                    {{ __('Já tem uma conta? Entrar') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>
</x-guest-layout>
