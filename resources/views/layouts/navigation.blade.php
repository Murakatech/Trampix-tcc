<!-- Header de Navegação Trampix -->
<nav class="trampix-navbar navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center w-100">
            


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
                                <a class="nav-link trampix-nav-link {{ request()->routeIs('vagas.index') ? 'active' : '' }}" 
                                   href="{{ route('vagas.index') }}">
                                    Vagas
                                </a>
                            </li>
                            
                            @auth
                                <li class="nav-item">
                                    <a class="nav-link trampix-nav-link {{ request()->routeIs('home', 'dashboard', 'freelancer.dashboard', 'company.dashboard') ? 'active' : '' }}" 
                                       href="{{ route('home') }}">
                                        Início
                                    </a>
                                </li>
                                
                                @can('isFreelancer')
                                    <li class="nav-item">
                                        <a class="nav-link trampix-nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}" 
                                           href="{{ route('applications.index') }}">
                                            Minhas Candidaturas
                                        </a>
                                    </li>
                                @endcan
                                
                                @can('isCompany')
                                    <li class="nav-item">
                                        <a class="nav-link trampix-nav-link {{ request()->routeIs('vagas.index') ? 'active' : '' }}" 
                                           href="{{ route('vagas.index') }}">
                                            Minhas Vagas
                                        </a>
                                    </li>
                                @endcan
                            @endauth
                        </ul>
                    </div>

                    <!-- Toggle do Modo Escuro e Perfil (Direita) -->
                    <div class="trampix-nav-right d-flex align-items-center gap-3">

                        
                        @auth
                            <!-- Perfil Dinâmico -->
                            <div class="nav-item dropdown position-relative" x-data="{ open: false }">
                                <button 
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="trampix-profile-btn d-flex align-items-center gap-2 p-2 border-0 bg-transparent"
                                    type="button">
                                    
                                    <!-- Avatar -->
                                    <div class="trampix-avatar position-relative">
                                        @php
                                            $photoPath = auth()->user()->profile_photo_path;
                                            $hasPhoto = $photoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                                        @endphp
                                        @if($hasPhoto)
                                            <img src="{{ auth()->user()->profile_photo_url }}" 
                                                 alt="Foto de perfil de {{ auth()->user()->display_name }}"
                                                 class="trampix-avatar-img">
                                        @else
                                            <div class="trampix-avatar-placeholder">
                                                {{ auth()->user()->initials }}
                                            </div>
                                        @endif
                                        
                                        <!-- Indicador de Status Online -->
                                        <div class="trampix-status-indicator bg-success"></div>
                                    </div>
                                    
                                    <!-- Informações do Usuário (Desktop) -->
                                    <div class="trampix-user-info d-none d-md-block text-start">
                                        <div class="trampix-user-name">{{ auth()->user()->display_name }}</div>
                                        <div class="trampix-user-role">{{ ucfirst(session('active_role')) }}</div>
                                    </div>
                                    
                                    <!-- Chevron -->
                                    <i class="fas fa-chevron-down trampix-chevron" 
                                       :class="{ 'rotate-180': open }"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     x-cloak
                                     class="trampix-dropdown-menu">
                                    
                                    <!-- Header do Dropdown -->
                                    <div class="trampix-dropdown-header">
                                        <div class="d-flex align-items-center gap-3">
                                            @php
                                                $photoPath = auth()->user()->profile_photo_path;
                                                $hasPhoto = $photoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath);
                                            @endphp
                                            @if($hasPhoto)
                                                <img src="{{ auth()->user()->profile_photo_url }}" 
                                                     alt="Foto de perfil"
                                                     class="trampix-dropdown-avatar">
                                            @else
                                                <div class="trampix-dropdown-avatar-placeholder">
                                                    {{ auth()->user()->initials }}
                                                </div>
                                            @endif
                                            
                                            <div class="flex-grow-1">
                                                <div class="trampix-dropdown-name">{{ auth()->user()->display_name }}</div>
                                                <div class="trampix-dropdown-email">{{ auth()->user()->email }}</div>
                                                <span class="trampix-dropdown-role">{{ ucfirst(session('active_role')) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Divisor -->
                                    <div class="trampix-dropdown-divider"></div>
                                    
                                    <!-- Itens do Menu -->
                                    <div class="trampix-dropdown-items">
                                        @unless(auth()->user()->isAdmin())
                                            <a href="{{ route('profile.edit') }}" class="trampix-dropdown-item">
                                                <i class="fas fa-user-edit"></i>
                                                <span>Editar Perfil</span>
                                            </a>
                                        @endunless
        
                                        <a href="{{ route('profile.account') }}" class="trampix-dropdown-item">
                                            <i class="fas fa-cog"></i>
                                            <span>Configurações</span>
                                        </a>
                                        
                                        @if(auth()->user()->hasMultipleRoles() && !auth()->user()->isAdmin())
                                            <div class="trampix-dropdown-divider"></div>
                                            <div class="px-3 py-2">
                                                <small class="text-muted fw-bold">TROCAR PERFIL</small>
                                            </div>
                                            
                                            @if(!auth()->user()->isAdmin() && auth()->user()->freelancer && auth()->user()->active_role !== 'freelancer')
                                                <form method="POST" action="{{ route('profile.switch-role') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="role" value="freelancer">
                                                    <button type="submit" class="trampix-dropdown-item w-100 border-0 bg-transparent">
                                                        <i class="fas fa-user"></i>
                                                        <span>Freelancer</span>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if(!auth()->user()->isAdmin() && auth()->user()->company && auth()->user()->active_role !== 'company')
                                                <form method="POST" action="{{ route('profile.switch-role') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="role" value="company">
                                                    <button type="submit" class="trampix-dropdown-item w-100 border-0 bg-transparent">
                                                        <i class="fas fa-building"></i>
                                                        <span>Empresa</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        
                                        <div class="trampix-dropdown-divider"></div>
                                        
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="trampix-dropdown-item w-100 border-0 bg-transparent text-danger">
                                                <i class="fas fa-sign-out-alt"></i>
                                                <span>Sair</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Botões de Login/Registro para usuários não autenticados -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Entrar</a>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Cadastrar</a>
                            </div>
                        @endauth
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

/* Garantir que a navbar fique colada no topo sem espaçamento extra */
html, body {
    margin: 0 !important;
    padding: 0 !important;
}
.trampix-navbar {
    position: sticky;
    top: 0;
    z-index: 1050;
    margin-top: 0 !important;
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
    /* Permite que o centro da navbar se ajuste e não force overflow */
    flex: 1 1 auto;
    min-width: 0;
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

/* Evita que itens da navbar "vazem" para fora em telas médias/grandes */
@media (min-width: 992px) {
    .trampix-nav-center .navbar-nav {
        flex-wrap: wrap; /* permite quebrar linha quando há muitos itens */
        gap: 0.75rem; /* espaço mais compacto entre itens */
    }
    /* reduz um pouco o padding dos links para caber mais itens */
    .trampix-nav-link {
        padding: 0.4rem 0.75rem;
    }
}

@media (min-width: 992px) and (max-width: 1200px) {
    .trampix-nav-center {
        max-width: 100%; /* libera mais largura para os itens em telas menores */
    }
    .trampix-nav-center .navbar-nav {
        gap: 0.5rem !important;
    }
    .trampix-nav-link {
        padding: 0.35rem 0.6rem;
        font-size: 0.95rem;
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
