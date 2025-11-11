@props(['hoverCls' => 'hover:bg-gray-100'])
@php
    $hoverCls = $hoverCls ?? 'hover:bg-gray-100';
@endphp

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
            @php
                $sidebarRole = session('active_role')
                    ?? (Auth::user()->isCompany() ? 'company' : (Auth::user()->isFreelancer() ? 'freelancer' : null));
            @endphp
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 {{ $sidebarRole === 'company' ? '' : 'bg-purple-600' }}" style="{{ $sidebarRole === 'company' ? 'background-color: var(--trampix-green);' : '' }}">
                @php
                    $logoPath = 'img/logo_trampix.png';
                    $logoExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath);
                @endphp
                @if($logoExists)
                    <img src="{{ asset('storage/' . $logoPath) }}" alt="Trampix Logo" class="h-8 w-auto object-contain">
                @else
                    <span class="text-white font-bold text-lg">T</span>
                @endif
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
                <h2 class="{{ $sidebarRole === 'company' ? 'text-green-600' : 'text-purple-600' }} font-bold text-xl whitespace-nowrap">Trampix</h2>
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
            // Determinar o papel ativo: prioriza sessão; se não houver, escolhe conforme perfis existentes
            $activeRole = session('active_role')
                ?? ($isAdmin ? 'admin' : ($isCompany ? 'company' : ($isFreelancer ? 'freelancer' : null)));
        @endphp
        @php
            // Hover classes dinâmicas por papel ativo (empresa: verde, outros: roxo)
            $hoverCls = ($activeRole === 'company')
                ? 'hover:bg-green-50 hover:text-green-600'
                : 'hover:bg-purple-50 hover:text-purple-600';
        @endphp

        <!-- Dashboard Home - oculto para admin -->
        @if($activeRole !== 'admin')
        <a href="{{ route('dashboard') }}" 
           data-menu-item="dashboard"
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('dashboard') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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

        <!-- Conectar - novo item abaixo de Dashboard -->
        <a href="{{ route('connect.index') }}"
           data-menu-item="connect"
           class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('connect.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
            <i class="fa-solid fa-share-nodes text-lg flex-shrink-0"></i>
            <span
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="ml-3 font-medium whitespace-nowrap overflow-hidden">Conectar</span>
        </a>
        @endif

        @if($activeRole === 'freelancer')
            <!-- Buscar Vagas - Visível para papel ativo Freelancer -->
            <a href="{{ route('vagas.index') }}" 
               data-menu-item="search-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('vagas.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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

        @if($activeRole === 'freelancer')
            <!-- Opções específicas para Freelancer -->
            <a href="{{ route('applications.index') }}" 
               data-menu-item="my-applications"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('applications.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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

            <!-- Trabalhos finalizados (Freelancer) -->
            <a href="{{ route('finished.index') }}" 
               data-menu-item="finished-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('finished.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-check-double text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Trabalhos Finalizados</span>
            </a>

            <a href="{{ route('profiles.show', auth()->user()) }}" 
               data-menu-item="my-profile"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200">
                <i class="fa-solid fa-user text-lg flex-shrink-0"></i>
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

        @elseif($activeRole === 'company')
            <!-- Opções específicas para Empresa -->
            <a href="{{ route('company.vagas.index') }}" 
               data-menu-item="my-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('company.vagas.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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
               data-menu-item="manage-applications"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('applications.manage') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-file-lines text-lg flex-shrink-0"></i>
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

            <!-- Trabalhos finalizados (Empresa) -->
            <a href="{{ route('finished.index') }}" 
               data-menu-item="finished-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('finished.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-check-double text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Trabalhos Finalizados</span>
            </a>

            <a href="{{ route('profiles.show', auth()->user()) }}" 
               data-menu-item="company-profile"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duração-200">
                <i class="fa-solid fa-building text-lg flex-shrink-0"></i>
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

        @elseif($activeRole === 'admin')
            <!-- Opções específicas para Admin -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-gauge text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duração-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duração-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Dashboard</span>
            </a>
            <a href="{{ route('admin.freelancers') }}" 
               data-menu-item="manage-freelancers"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duração-200 {{ request()->routeIs('admin.freelancers') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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
               data-menu-item="manage-companies"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('admin.companies') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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
               data-menu-item="manage-applications"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('admin.applications') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-file-lines text-lg flex-shrink-0"></i>
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

            <!-- Categorias (Admin) -->
            <a href="{{ route('admin.categories.index') }}" 
               data-menu-item="manage-categories"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('admin.categories.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-tags text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Categorias</span>
            </a>

            <!-- Todas as Vagas (lista pública, visível no admin) -->
            <a href="{{ route('vagas.index') }}" 
               data-menu-item="search-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('vagas.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
                <i class="fa-solid fa-briefcase text-lg flex-shrink-0"></i>
                <span 
                    x-show="expanded"
                    x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-200 ease-in-out"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="ml-3 font-medium whitespace-nowrap overflow-hidden">Todas as Vagas</span>
            </a>

            @can('isAdmin')
            <a href="{{ route('admin.vagas.create') }}" 
               data-menu-item="create-job"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duration-200 {{ request()->routeIs('admin.vagas.create') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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
               data-menu-item="manage-jobs"
               class="flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 {{ $hoverCls }} transition-colors duração-200 {{ request()->routeIs('company.vagas.index') ? ($activeRole === 'company' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') : '' }}">
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



        <!-- Development Tools removidos -->
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