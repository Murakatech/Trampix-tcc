<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Esqueceu sua senha?</h1>
        <p class="text-sm text-gray-600">
            Sem problemas. Informe seu endereço de email e enviaremos um link de redefinição de senha que permitirá escolher uma nova.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6" novalidate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" class="text-gray-700 font-medium" />
            <x-text-input 
                id="email" 
                class="block mt-2 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                placeholder="seu@email.com"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-800 transition duration-200">
                ← Voltar ao login
            </a>
            <button type="submit" class="btn-trampix-primary px-6 py-3">
                Enviar Link de Redefinição
            </button>
        </div>
    </form>
</x-guest-layout>
