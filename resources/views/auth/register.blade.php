<x-guest-layout>
    <div class="space-y-6">
        <header class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">Crie sua conta</h1>
            <p class="mt-1 text-sm text-gray-600">Cadastre-se para aproveitar todas as funcionalidades</p>
        </header>

        <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" value="Nome" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- User Type -->
            <div>
                <x-input-label for="user_type" value="Tipo de Usuário" />
                <select id="user_type" name="user_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Selecione o tipo de usuário</option>
                    <option value="freelancer" {{ old('user_type') == 'freelancer' ? 'selected' : '' }}>
                        🧑‍💻 Freelancer - Busco trabalhos
                    </option>
                    <option value="company" {{ old('user_type') == 'company' ? 'selected' : '' }}>
                        🏢 Empresa - Publico vagas
                    </option>
                </select>
                <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" value="Senha" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                <x-password-requirements />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" value="Confirmar Senha" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('login') }}" class="btn-trampix-secondary btn-glow px-4 py-2">Entrar</a>
                <x-primary-button class="btn-trampix-primary btn-glow ms-3 px-4 py-2">
                    Cadastrar
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        // Spinner: rotate brand icon while submitting
        document.getElementById('register-form')?.addEventListener('submit', function () {
            const brand = document.getElementById('brand-icon');
            if (brand) brand.classList.add('icon-spin-slow');
        });
    </script>
</x-guest-layout>
