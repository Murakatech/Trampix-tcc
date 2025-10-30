<!-- Sidebar -->
<aside 
    x-data="{ expanded: false }"
    @mouseenter="expanded = true"
    @mouseleave="expanded = false"
    :class="{
        'w-20': !expanded,
        'w-56': expanded,
        'shadow-lg': expanded,
        'z-20': expanded,
        'z-10': !expanded
    }"
    class="trampix-sidebar fixed left-0 top-0 min-h-screen bg-white border-r border-gray-200 transition-all duration-300 ease-in-out overflow-hidden"
    id="sidebar">

    <!-- Sidebar Header -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('storage/img/logo_trampix.png') }}" alt="Trampix Logo" class="h-8 w-auto object-contain">
            </div>
            <div 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-300 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="overflow-hidden">
                <h2 class="text-purple-600 font-bold text-xl whitespace-nowrap">Trampix</h2>
                <p class="text-gray-500 text-sm whitespace-nowrap">Dashboard</p>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <nav class="py-4">
        <!-- Dashboard Home -->
        @php
            $dashboardRoute = 'dashboard';
            $isActive = request()->routeIs('dashboard');
            
            if (auth()->user()->isFreelancer()) {
                $dashboardRoute = 'freelancer.dashboard';
                $isActive = request()->routeIs('freelancer.dashboard');
            } elseif (auth()->user()->isCompany()) {
                $dashboardRoute = 'company.dashboard';
                $isActive = request()->routeIs('company.dashboard');
            }
        @endphp
        <a href="{{ route($dashboardRoute) }}" 
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ $isActive ? 'bg-purple-100 text-purple-600' : '' }}">
            <i class="fa-solid fa-house text-lg flex-shrink-0"></i>
            <span 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="ml-3 font-medium whitespace-nowrap overflow-hidden">Dashboard</span>
        </a>

        <!-- Buscar Vagas -->
        <a href="{{ route('vagas.index') }}" 
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('vagas.index') ? 'bg-purple-100 text-purple-600' : '' }}">
            <i class="fa-solid fa-magnifying-glass text-lg flex-shrink-0"></i>
            <span 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="ml-3 font-medium whitespace-nowrap overflow-hidden">Buscar Vagas</span>
        </a>

        @can('isAdmin')
            <!-- Admin Section -->
            <div 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Administração</h3>
            </div>
            
            <a href="{{ route('admin.freelancers') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('admin.freelancers') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-id-card text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Freelancers</span>
            </a>

            <a href="{{ route('admin.companies') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('admin.companies') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-building text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Empresas</span>
            </a>

            <a href="{{ route('admin.applications') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('admin.applications') ? 'bg-purple-100 text-purple-600' : '' }}">
                <x-icons.candidaturas class="w-5 h-5 flex-shrink-0" />
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Candidaturas</span>
            </a>

            <a href="{{ route('admin.vagas.create') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('admin.vagas.create') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-plus text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Criar Vaga</span>
            </a>

            <a href="{{ route('admin.vagas.index') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('admin.vagas.index') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-briefcase text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Gerenciar Vagas</span>
            </a>
        @endcan

        @can('isFreelancer')
            <!-- Freelancer Section -->
            <div 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Freelancer</h3>
            </div>
            
            <a href="{{ route('applications.index') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('applications.index') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-file-lines text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Minhas Candidaturas</span>
            </a>

            <a href="{{ route('profile.edit') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-user-edit text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Meu Perfil</span>
            </a>
        @endcan

        @can('isCompany')
            <!-- Company Section -->
            <div 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-100"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="px-4 py-2 mt-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Empresa</h3>
            </div>
            
            <a href="{{ route('vagas.create') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('vagas.create') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-plus text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Criar Vaga</span>
            </a>

            <a href="{{ route('vagas.index') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('vagas.index') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-briefcase text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Minhas Vagas</span>
            </a>

            <a href="{{ route('profile.edit') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('profile.edit') ? 'bg-purple-100 text-purple-600' : '' }}">
                <i class="fa-solid fa-building-user text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Perfil da Empresa</span>
            </a>
        @endcan



        <!-- Development Tools -->
        <div 
            x-show="expanded"
            x-transition:enter="transition-opacity duration-300 ease-in-out delay-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-200 ease-in-out"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="px-4 py-2 mt-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Desenvolvimento</h3>
        </div>
        
        <a href="{{ route('styleguide') }}" 
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('styleguide') ? 'bg-purple-100 text-purple-600' : '' }}">
            <span 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="ml-3 font-medium whitespace-nowrap overflow-hidden">Styleguide</span>
        </a>
    </nav>
</aside>