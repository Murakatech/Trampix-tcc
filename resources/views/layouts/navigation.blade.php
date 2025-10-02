<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="fas fa-briefcase me-2 text-primary"></i>
            Trampix
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
                    <!-- Dropdown do usuário -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            @php
                                $profilePhoto = null;
                                if (auth()->user()->freelancer && auth()->user()->freelancer->profile_photo) {
                                    $profilePhoto = auth()->user()->freelancer->profile_photo;
                                } elseif (auth()->user()->company && auth()->user()->company->profile_photo) {
                                    $profilePhoto = auth()->user()->company->profile_photo;
                                }
                            @endphp
                            
                            @if($profilePhoto)
                                <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                     alt="Foto de {{ auth()->user()->name }}" 
                                     class="rounded-circle me-2" 
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                     style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-white" style="font-size: 14px;"></i>
                                </div>
                            @endif
                            
                            <span>{{ auth()->user()->name }}</span>
                            
                            @if(auth()->user()->role === 'admin')
                                <span class="badge bg-danger ms-2">Administrador</span>
                            @elseif(auth()->user()->role === 'company')
                                <span class="badge bg-primary ms-2">Empresa</span>
                            @elseif(auth()->user()->role === 'freelancer')
                                <span class="badge bg-success ms-2">Freelancer</span>
                            @endif
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profiles.show', auth()->user()) }}">
                                    <i class="fas fa-id-badge me-2"></i>
                                    Ver Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2"></i>
                                    Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
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
