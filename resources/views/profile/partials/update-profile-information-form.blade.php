<div>
    <p class="text-muted mb-3">
        Atualize as informações do seu perfil e endereço de email.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.account.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label">Nome Completo</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <small class="form-text text-muted">Este é seu nome completo usado na conta.</small>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>



        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <div class="alert alert-warning">
                        <small>
                            Seu endereço de email não foi verificado.
                            <button form="send-verification" class="btn btn-link btn-sm p-0 text-decoration-underline">
                                Clique aqui para reenviar o email de verificação.
                            </button>
                        </small>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success">
                            <small>Um novo link de verificação foi enviado para seu email.</small>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Salvar</button>

            @if (session('status') === 'profile-updated')
                <small class="text-success">Salvo.</small>
            @endif
        </div>
    </form>
</div>
