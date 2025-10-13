<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6">
        <header class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h1>
            <p class="mt-1 text-sm text-gray-600">Entre para continuar sua jornada no Trampix</p>
        </header>

        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <button type="button" id="forgot-trigger" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        {{ __('Forgot your password?') }}
                    </button>
                @endif
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('register') }}" class="btn-trampix-secondary btn-glow px-4 py-2">Inscrever-se</a>
                <x-primary-button class="btn-trampix-primary btn-glow ms-3 px-4 py-2">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Modal: Forgot Password (slide) -->
    <div id="forgot-modal" class="hidden">
        <div class="modal-backdrop opacity-0 pointer-events-none" aria-hidden="true">
            <div class="modal-panel translate-y-6 opacity-0">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Esqueceu sua senha?</h2>
                    <p class="mt-1 text-sm text-gray-600">Vamos te redirecionar para a página de recuperação.</p>
                    <div class="mt-4 flex items-center justify-end gap-2">
                        <button type="button" id="forgot-close" class="btn-trampix-secondary px-3 py-2">Fechar</button>
                        <a href="{{ route('password.request') }}" class="btn-trampix-primary btn-glow px-4 py-2">Recuperar senha</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Spinner: rotate brand icon while submitting
        document.getElementById('login-form')?.addEventListener('submit', function () {
            const brand = document.getElementById('brand-icon');
            if (brand) brand.classList.add('icon-spin-slow');
        });

        // Modal slide open/close
        const trigger = document.getElementById('forgot-trigger');
        const modal = document.getElementById('forgot-modal');
        const backdrop = modal?.querySelector('.modal-backdrop');
        const panel = modal?.querySelector('.modal-panel');
        const closeBtn = document.getElementById('forgot-close');

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            backdrop.classList.remove('pointer-events-none');
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('opacity-0');
            panel.classList.add('slide-in');
        }

        function closeModal() {
            if (!modal) return;
            panel.classList.remove('slide-in');
            panel.classList.add('slide-out');
            backdrop.classList.add('opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); }, 180);
        }

        trigger?.addEventListener('click', openModal);
        closeBtn?.addEventListener('click', closeModal);
        backdrop?.addEventListener('click', (e) => { if (e.target === backdrop) closeModal(); });
    </script>
</x-guest-layout>
