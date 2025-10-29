<!-- Sidebar -->
<aside class="trampix-sidebar w-64 min-h-screen" id="sidebar">
    <!-- Sidebar Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-briefcase text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-purple-600 font-bold text-xl">Trampix</h2>
                <p class="text-gray-500 text-sm">Dashboard</p>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <nav class="py-4">
        <!-- Dashboard Home -->
        <a href="{{ route('dashboard') }}" 
           class="sidebar-card {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <div class="sidebar-icon">
                <i class="fas fa-home"></i>
            </div>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <!-- Buscar Vagas -->
        <a href="{{ route('vagas.index') }}" 
           class="sidebar-card {{ request()->routeIs('vagas.index') ? 'active' : '' }}">
            <div class="sidebar-icon">
                <i class="fas fa-search"></i>
            </div>
            <span class="sidebar-text">Buscar Vagas</span>
        </a>

        @can('isAdmin')
            <!-- Admin Section -->
            <div class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Administração</h3>
            </div>
            
            <a href="{{ route('admin.freelancers') }}" 
               class="sidebar-card {{ request()->routeIs('admin.freelancers') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-users"></i>
                </div>
                <span class="sidebar-text">Freelancers</span>
            </a>

            <a href="{{ route('admin.companies') }}" 
               class="sidebar-card {{ request()->routeIs('admin.companies') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-building"></i>
                </div>
                <span class="sidebar-text">Empresas</span>
            </a>

            <a href="{{ route('admin.applications') }}" 
               class="sidebar-card {{ request()->routeIs('admin.applications') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <span class="sidebar-text">Candidaturas</span>
            </a>
        @endcan

        @can('isCompany')
            <!-- Company Section -->
            <div class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Empresa</h3>
            </div>
            
            <a href="{{ route('vagas.create') }}" 
               class="sidebar-card {{ request()->routeIs('vagas.create') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <span class="sidebar-text">Nova Vaga</span>
            </a>

            <a href="{{ route('companies.vacancies', auth()->user()->company) }}" 
               class="sidebar-card {{ request()->routeIs('companies.vacancies') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-list"></i>
                </div>
                <span class="sidebar-text">Minhas Vagas</span>
            </a>
        @endcan

        @can('isFreelancer')
            <!-- Freelancer Section -->
            <div class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Freelancer</h3>
            </div>
            
            <a href="{{ route('applications.index') }}" 
               class="sidebar-card {{ request()->routeIs('applications.index') ? 'active' : '' }}">
                <div class="sidebar-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="sidebar-text">Candidaturas</span>
            </a>
        @endcan

        <!-- Profile Section -->
        <div class="px-4 py-2 mt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perfil</h3>
        </div>
        
        <a href="{{ route('profiles.show', auth()->user()) }}" 
           class="sidebar-card {{ request()->routeIs('profiles.show') ? 'active' : '' }}">
            <div class="sidebar-icon">
                <i class="fas fa-user"></i>
            </div>
            <span class="sidebar-text">Ver Perfil</span>
        </a>

        <a href="{{ route('profile.edit') }}" 
           class="sidebar-card {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <div class="sidebar-icon">
                <i class="fas fa-edit"></i>
            </div>
            <span class="sidebar-text">Editar Perfil</span>
        </a>

        <!-- Development Tools -->
        <div class="px-4 py-2 mt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Desenvolvimento</h3>
        </div>
        
        <a href="{{ route('styleguide') }}" 
           class="sidebar-card {{ request()->routeIs('styleguide') ? 'active' : '' }}">
            <div class="sidebar-icon">
                <i class="fas fa-palette"></i>
            </div>
            <span class="sidebar-text">Styleguide</span>
        </a>
    </nav>
</aside>