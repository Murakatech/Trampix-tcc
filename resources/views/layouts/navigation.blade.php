<!-- Header de Navegação Trampix -->
<nav class="trampix-navbar navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center w-100">
            
            <!-- Logo (Esquerda) -->
            <div class="trampix-logo-section">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('storage/img/logo_trampix.png') }}" 
                         alt="Trampix Logo" 
                         class="trampix-logo-img me-2">
                    <span class="trampix-logo-text">Trampix</span>
                </a>
            </div>

            <!-- Toggle button para mobile -->
            <button class="navbar-toggler d-lg-none" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal (Centro e Direita) -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex justify-content-between align-items-center w-100">
                    
                    <!-- Links principais (Centro) -->
                    <div class="trampix-nav-center d-flex">
                        <ul class="navbar-nav d-flex flex-row gap-3">
                            <li class="nav-item">
                                <a class="nav-link trampix-nav-link {{ request()->routeIs('home') ? 'active' : '' }}" 
                                   href="{{ route('home') }}">
                                    <i class="fas fa-search me-1"></i>
                                    Vagas
                                </a>
                            </li>
                            
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link trampix-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                                       href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i>
                                        Dashboard
                                    </a>
                                </li>
                                
                                @can('isFreelancer')
                                    <li class="nav-item">
                                        <a class="nav-link trampix-nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}" 
                                           href="{{ route('applications.index') }}">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Minhas Candidaturas
                                        </a>
                                    </li>
                                @endcan
                                
                                @can('isCompany')
                                    <li class="nav-item">
                                        <a class="nav-link trampix-nav-link {{ request()->routeIs('job_vacancies.index') ? 'active' : '' }}" 
                                           href="{{ route('job_vacancies.index') }}">
                                            <i class="fas fa-briefcase me-1"></i>
                                            Minhas Vagas
                                        </a>
                                    </li>
                                @endcan
                            @endauth
                        </ul>
                    </div>

                    <!-- Menu de usuário (Direita) -->
                    <div class="trampix-nav-right">
                        <ul class="navbar-nav">
                @auth
                    <!-- Menu de Perfil Redesenhado -->
                    <li class="nav-item dropdown" 
                        x-data="{ 
                            open: false,
                            toggle() { this.open = !this.open },
                            close() { this.open = false }
                        }"
                        x-on:click.away="close()"
                        x-on:keydown.escape="close()">
                        
                        <!-- Botão do Perfil -->
                        <button class="trampix-profile-btn d-flex align-items-center gap-2 border-0 bg-transparent p-2 rounded-3"
                                type="button"
                                x-on:click="toggle()"
                                x-bind:aria-expanded="open"
                                aria-haspopup="true"
                                role="button"
                                tabindex="0">
                            
                            @php
                                $activeProfile = null;
                                $profilePhoto = null;
                                $userRole = 'Usuário';
                                
                                if (auth()->user()->freelancer) {
                                    $activeProfile = auth()->user()->freelancer;
                                    $profilePhoto = $activeProfile->profile_photo;
                                    $userRole = 'Freelancer';
                                } elseif (auth()->user()->company) {
                                    $activeProfile = auth()->user()->company;
                                    $profilePhoto = $activeProfile->profile_photo;
                                    $userRole = 'Empresa';
                                }
                            @endphp
                            
                            <!-- Avatar -->
                            <div class="trampix-avatar position-relative">
                                @if($profilePhoto)
                                    <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                         alt="Foto de {{ auth()->user()->name }}" 
                                         class="trampix-avatar-img">
                                @else
                                    <div class="trampix-avatar-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                
                                <!-- Indicador de status online -->
                                <div class="trampix-status-indicator"></div>
                            </div>
                            
                            <!-- Informações do usuário (desktop) -->
                            <div class="trampix-user-info d-none d-lg-block text-start">
                                <div class="trampix-user-name">{{ auth()->user()->name }}</div>
                                <div class="trampix-user-role">{{ $userRole }}</div>
                            </div>
                            
                            <!-- Ícone de seta -->
                            <i class="fas fa-chevron-down trampix-chevron" 
                               x-bind:class="{ 'rotate-180': open }"></i>
                        </button>

                        <!-- Menu Dropdown -->
                        <div class="trampix-dropdown-menu"
                             x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-1 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-1 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             x-cloak>
                            
                            <!-- Header do menu -->
                            <div class="trampix-dropdown-header">
                                <div class="d-flex align-items-center gap-3">
                                    @if($profilePhoto)
                                        <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                             alt="Foto de {{ auth()->user()->name }}" 
                                             class="trampix-dropdown-avatar">
                                    @else
                                        <div class="trampix-dropdown-avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <div class="trampix-dropdown-name">{{ auth()->user()->name }}</div>
                                        <div class="trampix-dropdown-email">{{ auth()->user()->email }}</div>
                                        <div class="trampix-dropdown-role">{{ $userRole }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Divisor -->
                            <div class="trampix-dropdown-divider"></div>
                            
                            <!-- Itens do menu -->
                            <div class="trampix-dropdown-items">
                                <a href="{{ route('profiles.show', auth()->user()) }}" 
                                   class="trampix-dropdown-item"
                                   x-on:click="close()">
                                    <i class="fas fa-eye trampix-item-icon" style="color: var(--trampix-purple);"></i>
                                    <div class="trampix-item-content">
                                        <div class="trampix-item-title">Ver Perfil Profissional</div>
                                        <div class="trampix-item-subtitle">Visualizar perfil público</div>
                                    </div>
                                </a>
                                
                                <a href="{{ route('profile.edit') }}" 
                                   class="trampix-dropdown-item"
                                   x-on:click="close()">
                                    <i class="fas fa-cog trampix-item-icon" style="color: var(--trampix-green);"></i>
                                    <div class="trampix-item-content">
                                        <div class="trampix-item-title">Configurações</div>
                                        <div class="trampix-item-subtitle">Editar perfil e preferências</div>
                                    </div>
                                </a>
                                
                                @can('isFreelancer')
                                    <a href="{{ route('applications.index') }}" 
                                       class="trampix-dropdown-item"
                                       x-on:click="close()">
                                        <i class="fas fa-file-alt trampix-item-icon" style="color: #007bff;"></i>
                                        <div class="trampix-item-content">
                                            <div class="trampix-item-title">Minhas Candidaturas</div>
                                            <div class="trampix-item-subtitle">Acompanhar aplicações</div>
                                        </div>
                                    </a>
                                @endcan
                            </div>
                            
                            <!-- Divisor -->
                            <div class="trampix-dropdown-divider"></div>
                            
                            <!-- Logout -->
                            <div class="trampix-dropdown-footer">
                                <form method="POST" action="{{ route('logout') }}" class="w-100">
                                    @csrf
                                    <button type="submit" 
                                            class="trampix-logout-btn w-100"
                                            x-on:click="close()">
                                        <i class="fas fa-sign-out-alt trampix-item-icon" style="color: var(--trampix-red);"></i>
                                        <div class="trampix-item-content">
                                            <div class="trampix-item-title">Sair</div>
                                            <div class="trampix-item-subtitle">Encerrar sessão</div>
                                        </div>
                                    </button>
                                </form>
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
            </div>
        </div>
    </div>
</nav>

<style>
/* Variáveis CSS do Design System Trampix */
:root {
    --trampix-purple: #8F3FF7;
    --trampix-green: #B9FF66;
    --trampix-black: #191A23;
    --trampix-light-gray: #F3F3F3;
    --trampix-red: #FF4C4C;
    --trampix-dark-gray: #4A4A4A;
}

/* Logo Trampix */
.trampix-logo-section {
    flex-shrink: 0;
}

.trampix-logo-img {
    height: 40px;
    width: auto;
    object-fit: contain;
    max-width: 150px;
}

.trampix-logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--trampix-purple);
}

.navbar-brand {
    margin-right: 0;
    padding: 0.5rem 0;
}

/* Navbar principal */
.trampix-navbar {
    min-height: 70px;
    padding: 0.5rem 0;
}

.trampix-nav-center {
    flex: 1;
    justify-content: center;
    max-width: 600px;
    margin: 0 auto;
}

.trampix-nav-right {
    flex-shrink: 0;
}

.trampix-nav-link {
    color: var(--trampix-black);
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.trampix-nav-link:hover,
.trampix-nav-link.active {
    color: var(--trampix-purple);
    background-color: rgba(143, 63, 247, 0.1);
}

/* Botão Principal do Perfil */
.trampix-profile-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
}

.trampix-profile-btn:hover {
    background-color: rgba(143, 63, 247, 0.05) !important;
    transform: translateY(-1px);
}

.trampix-profile-btn:focus {
    outline: 2px solid var(--trampix-purple);
    outline-offset: 2px;
}

/* Avatar Principal */
.trampix-avatar {
    width: 44px;
    height: 44px;
}

.trampix-avatar-img {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--trampix-purple);
    transition: all 0.3s ease;
}

.trampix-avatar-placeholder {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--trampix-purple), #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    border: 3px solid var(--trampix-purple);
    transition: all 0.3s ease;
}

.trampix-profile-btn:hover .trampix-avatar-img,
.trampix-profile-btn:hover .trampix-avatar-placeholder {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(143, 63, 247, 0.3);
}

/* Indicador de Status Online */
.trampix-status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background-color: var(--trampix-green);
    border: 2px solid white;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(185, 255, 102, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(185, 255, 102, 0); }
    100% { box-shadow: 0 0 0 0 rgba(185, 255, 102, 0); }
}

/* Informações do Usuário */
.trampix-user-info {
    max-width: 140px;
}

.trampix-user-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--trampix-black);
    line-height: 1.2;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.trampix-user-role {
    font-size: 12px;
    color: var(--trampix-dark-gray);
    line-height: 1.2;
}

/* Ícone Chevron */
.trampix-chevron {
    font-size: 12px;
    color: var(--trampix-dark-gray);
    transition: all 0.3s ease;
}

.rotate-180 {
    transform: rotate(180deg);
}

/* Menu Dropdown */
.trampix-dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1050;
    min-width: 320px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(143, 63, 247, 0.1);
    margin-top: 8px;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

/* Header do Dropdown */
.trampix-dropdown-header {
    padding: 20px;
    background: linear-gradient(135deg, rgba(143, 63, 247, 0.05), rgba(185, 255, 102, 0.05));
    border-bottom: 1px solid rgba(143, 63, 247, 0.1);
}

.trampix-dropdown-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--trampix-purple);
}

.trampix-dropdown-avatar-placeholder {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--trampix-purple), #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    border: 2px solid var(--trampix-purple);
}

.trampix-dropdown-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--trampix-black);
    line-height: 1.2;
    margin-bottom: 2px;
}

.trampix-dropdown-email {
    font-size: 13px;
    color: var(--trampix-dark-gray);
    line-height: 1.2;
    margin-bottom: 4px;
}

.trampix-dropdown-role {
    font-size: 12px;
    color: var(--trampix-purple);
    font-weight: 600;
    background-color: rgba(143, 63, 247, 0.1);
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-block;
}

/* Divisor */
.trampix-dropdown-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(143, 63, 247, 0.2), transparent);
    margin: 0 20px;
}

/* Itens do Menu */
.trampix-dropdown-items {
    padding: 12px 8px;
}

.trampix-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    text-decoration: none;
    color: var(--trampix-black);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 4px;
    position: relative;
    overflow: hidden;
}

.trampix-dropdown-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(143, 63, 247, 0.05), transparent);
    transition: left 0.5s ease;
}

.trampix-dropdown-item:hover::before {
    left: 100%;
}

.trampix-dropdown-item:hover {
    background-color: rgba(143, 63, 247, 0.05);
    transform: translateX(4px);
    color: var(--trampix-purple);
    text-decoration: none;
}

.trampix-item-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s ease;
}

.trampix-dropdown-item:hover .trampix-item-icon {
    transform: scale(1.1);
}

.trampix-item-content {
    flex: 1;
}

.trampix-item-title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.2;
    margin-bottom: 2px;
}

.trampix-item-subtitle {
    font-size: 12px;
    color: var(--trampix-dark-gray);
    line-height: 1.2;
}

.trampix-dropdown-item:hover .trampix-item-subtitle {
    color: rgba(143, 63, 247, 0.7);
}

/* Footer do Dropdown */
.trampix-dropdown-footer {
    padding: 8px;
    background-color: rgba(255, 76, 76, 0.02);
}

.trampix-logout-btn {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    border: none;
    background: transparent;
    color: var(--trampix-red);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    text-align: left;
    position: relative;
    overflow: hidden;
}

.trampix-logout-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 76, 76, 0.05), transparent);
    transition: left 0.5s ease;
}

.trampix-logout-btn:hover::before {
    left: 100%;
}

.trampix-logout-btn:hover {
    background-color: rgba(255, 76, 76, 0.05);
    transform: translateX(4px);
    color: var(--trampix-red);
}

.trampix-logout-btn:hover .trampix-item-icon {
    transform: scale(1.1);
}

/* Responsividade */
@media (max-width: 991.98px) {
    .trampix-dropdown-menu {
        min-width: 280px;
        right: -20px;
    }
    
    .trampix-dropdown-header {
        padding: 16px;
    }
    
    .trampix-dropdown-name {
        font-size: 15px;
    }
}

@media (max-width: 575.98px) {
    .trampix-dropdown-menu {
        min-width: 260px;
        right: -40px;
    }
    
    .trampix-user-info {
        display: none !important;
    }
}

/* Acessibilidade */
.trampix-dropdown-item:focus,
.trampix-logout-btn:focus {
    outline: 2px solid var(--trampix-purple);
    outline-offset: 2px;
}

/* Animações de entrada */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Alpine.js cloak */
[x-cloak] {
    display: none !important;
}

/* Posicionamento relativo para o dropdown */
.nav-item.dropdown {
    position: relative;
}
</style>
