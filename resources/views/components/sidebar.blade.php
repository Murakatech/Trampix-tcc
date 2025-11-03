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
    class="trampix-sidebar fixed left-0 top-0 h-screen bg-white border-r border-gray-200 transition-all duration-300 ease-in-out overflow-hidden flex flex-col"
    id="sidebar">

    <!-- Sidebar Header -->
    <div class="p-4 border-b border-gray-200 flex-shrink-0">
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
    <nav class="py-4 flex-1 overflow-y-auto">
        @php
            $user = auth()->user();
            $isFreelancer = $user->isFreelancer();
            $isCompany = $user->isCompany();
            $isAdmin = $user->isAdmin();
        @endphp

        <!-- Dashboard Home - Sempre presente -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-purple-100 text-purple-600' : '' }}">
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

        @if(!$isCompany)
            <!-- Buscar Vagas - Visível apenas para Freelancers e Admins -->
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
        @endif

        @if($isFreelancer)
            <!-- Opções específicas para Freelancer -->
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

        @elseif($isCompany)
            <!-- Opções específicas para Empresa -->
            <a href="{{ route('company.vagas.index') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('company.vagas.index') ? 'bg-purple-100 text-purple-600' : '' }}">
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

            <a href="{{ route('applications.manage') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 {{ request()->routeIs('applications.manage') ? 'bg-purple-100 text-purple-600' : '' }}">
                <x-icons.candidaturas class="w-5 h-5 flex-shrink-0" />
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Gerenciar Candidaturas</span>
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

        @elseif($isAdmin)
            <!-- Opções específicas para Admin -->
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

            @can('isAdmin')
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
            @endcan

            @can('isCompany')
            <a href="{{ route('company.vagas.index') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duração-200 {{ request()->routeIs('company.vagas.index') ? 'bg-purple-100 text-purple-600' : '' }}">
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
        @endif



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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarNav = sidebar.querySelector('nav');
    
    // Garantir que a sidebar mantenha altura correta
    function adjustSidebarHeight() {
        const viewportHeight = window.innerHeight;
        sidebar.style.height = viewportHeight + 'px';
    }
    
    // Ajustar altura inicial
    adjustSidebarHeight();
    
    // Ajustar altura no resize
    window.addEventListener('resize', adjustSidebarHeight, { passive: true });
    
    // Smooth scroll behavior para links internos da sidebar
    const sidebarLinks = sidebar.querySelectorAll('a[href^="#"], a[href^="/"]');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Adicionar feedback visual suave
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
});
</script>