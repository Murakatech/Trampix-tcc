<div>
    <p class="text-gray-600 mb-4">
        Atualize as informações da sua conta e endereço de email.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.account.update') }}">
        @csrf
        @method('patch')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
            <input id="name" name="name" type="text" class="trampix-input @error('name') border-red-500 @enderror" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input id="email" name="email" type="email" class="trampix-input @error('email') border-red-500 @enderror" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <p class="text-sm text-yellow-800">
                            Seu endereço de email não foi verificado.
                            <button form="send-verification" class="text-yellow-600 underline hover:text-yellow-800 transition-colors duration-200">
                                Clique aqui para reenviar o email de verificação.
                            </button>
                        </p>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2 bg-green-50 border border-green-200 rounded-md p-3">
                            <p class="text-sm text-green-800">Um novo link de verificação foi enviado para seu email.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn-trampix-primary">Salvar</button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600">Salvo.</p>
            @endif
        </div>
    </form>
</div>