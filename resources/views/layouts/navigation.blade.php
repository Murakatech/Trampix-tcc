<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('welcome') }}">
            <img src="{{ asset('storage/img/logo.svg') }}" alt="Trampix Logo" class="h-10 object-contain me-2" style="height: 2.5rem;">
            <span>Trampix</span>
        </a>

        <!-- Toggle button para mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu principal -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Links da esquerda -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-search me-1"></i>
                        Vagas
                    </a>
                </li>
                
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    @can('isFreelancer')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}" href="{{ route('applications.index') }}">
                                <i class="fas fa-file-alt me-1"></i>
                                Minhas Candidaturas
                            </a>
                        </li>
                    @endcan
                @endauth
            </ul>

            <!-- Links da direita -->
            <ul class="navbar-nav">
                @auth
                    <!-- Novo cabeçalho com avatar + nome + dropdown -->
                    <li class="nav-item dropdown">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="text-center position-relative dropdown">
                                <button class="btn p-0 border-0 bg-transparent dropdown-toggle" 
                                        type="button" 
                                        id="profileDropdown" 
                                        data-bs-toggle="dropdown" 
                                        aria-expanded="false"
                                        style="box-shadow: none;">
                                    @php
                                        $activeProfile = null;
                                        $profilePhoto = null;
                                        
                                        if (auth()->user()->freelancer) {
                                            $activeProfile = auth()->user()->freelancer;
                                            $profilePhoto = $activeProfile->profile_photo;
                                        } elseif (auth()->user()->company) {
                                            $activeProfile = auth()->user()->company;
                                            $profilePhoto = $activeProfile->profile_photo;
                                        }
                                    @endphp
                                    
                                    @if($profilePhoto)
                                        <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                             alt="Foto de Perfil" 
                                             class="rounded-circle border-2 shadow-sm hover:shadow-md transition-all duration-300"
                                             style="width: 48px; height: 48px; object-fit: cover; border-color: #8b5cf6;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm hover:shadow-md transition-all duration-300"
                                             style="width: 48px; height: 48px; background-color: #8b5cf6; border: 2px solid #8b5cf6;">
                                            <i class="fas fa-user text-white" style="font-size: 18px;"></i>
                                        </div>
                                    @endif
                                    
                                    <p class="text-sm text-gray-700 mt-1 font-semibold mb-0" 
                                       style="font-size: 12px; color: #374151; font-weight: 600; max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ auth()->user()->name }}
                                    </p>
                                </button>

                                <!-- Dropdown Menu -->
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" 
                                    aria-labelledby="profileDropdown"
                                    style="border-radius: 16px; min-width: 180px; margin-top: 8px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 px-3 hover-item" 
                                           href="{{ route('profiles.show', auth()->user()) }}"
                                           style="border-radius: 12px; margin: 4px; transition: all 0.3s ease;">
                                            <i class="fas fa-eye me-2" style="color: #8b5cf6; width: 16px;"></i>
                                            Ver Perfil Profissional
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2 px-3 hover-item" 
                                           href="{{ route('profile.edit') }}"
                                           style="border-radius: 12px; margin: 4px; transition: all 0.3s ease;">
                                            <i class="fas fa-cog me-2" style="color: #10b981; width: 16px;"></i>
                                            Configurações
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-2"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" 
                                                    class="dropdown-item d-flex align-items-center py-2 px-3 hover-item w-100 border-0 bg-transparent text-start"
                                                    style="border-radius: 12px; margin: 4px; transition: all 0.3s ease;">
                                                <i class="fas fa-sign-out-alt me-2" style="color: #ef4444; width: 16px;"></i>
                                                Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                @else
                    <!-- Links para visitantes -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Entrar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>
                            Registrar
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<style>
.profile-photo-nav:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

.profile-nav-link:hover .text-dark {
    color: #0d6efd !important;
}

.profile-nav-link {
    transition: all 0.3s ease;
}

.profile-nav-link:hover {
    transform: translateY(-2px);
}

.hover-item:hover {
    background-color: #f8fafc !important;
    color: #8b5cf6 !important;
    transform: translateX(2px);
}

.dropdown-toggle::after {
    display: none !important;
}

.dropdown-menu {
    animation: fadeInDown 0.3s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Garantir visibilidade do botão de engrenagem */
    .navbar .dropdown-toggle {
        visibility: visible !important;
        opacity: 1 !important;
        display: flex !important;
        cursor: pointer;
    }
    
    .navbar .dropdown-toggle.btn {
        padding: 0.5rem 0.75rem;
        color: rgba(0,0,0,.55);
    }
    
    .navbar .dropdown-toggle.btn:hover {
        color: rgba(0,0,0,.7);
    }
    
    .navbar .dropdown-toggle .fas.fa-cog {
        font-size: 1.2rem;
        color: #6c757d;
        transition: color 0.3s ease;
    }
    
    .navbar .dropdown-toggle:hover .fas.fa-cog {
        color: #007bff;
    }

/* Melhorar visibilidade do dropdown */
    .dropdown-menu {
        border: 1px solid #dee2e6;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        min-width: 200px;
        z-index: 1050;
    }
    
    /* Animação do ícone de seta */
    .rotate-180 {
        transform: rotate(180deg);
    }
    
    .fas.fa-chevron-down {
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }
    
    /* Posicionamento relativo para o dropdown */
    .nav-item {
        position: relative;
    }
</style>
