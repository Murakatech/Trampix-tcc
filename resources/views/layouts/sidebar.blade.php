<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 z-40 h-screen w-64 bg-white border-r shadow-sm transition-transform duration-300
             -translate-x-full lg:translate-x-0" 
       :class="{
         'translate-x-0': open,
         'lg:-translate-x-full': collapsed
       }" 
       x-cloak>
    
    <!-- Topo com card do usuário único -->
    <div class="p-4 border-b">
        <div class="flex items-center justify-between">
            <a href="{{ route('profiles.show', Auth::user()) }}" class="flex items-center gap-3 hover:opacity-90 transition-opacity">
                <img class="h-10 w-10 rounded-full object-cover trampix-avatar-img" 
                     src="{{ Auth::user()->profile_photo_url ?? asset('images/avatar-placeholder.png') }}" 
                     alt="Avatar">
                <div class="leading-tight">
                    @php
                        $activeRole = session('active_role');
                        $displayName = Auth::user()->name;
                        
                        if ($activeRole === 'freelancer' && Auth::user()->freelancer) {
                            $displayName = Auth::user()->freelancer->display_name ?? Auth::user()->name;
                        } elseif ($activeRole === 'company' && Auth::user()->company) {
                            $displayName = Auth::user()->company->display_name ?? Auth::user()->name;
                        }
                    @endphp
                    <div class="font-semibold text-gray-900 trampix-user-name">{{ $displayName }}</div>
                    <div class="text-xs text-gray-500 capitalize trampix-user-role">{{ session('active_role') ?? Auth::user()->role }}</div>
                </div>
            </a>
            <!-- Botão de colapso desktop -->
            <button type="button" 
                    class="hidden lg:block p-1.5 rounded-md hover:bg-gray-100 transition-colors" 
                    @click="toggleDesktop()" 
                    aria-label="Colapsar sidebar">
                ←
            </button>
        </div>
    </div>

    @php $role = session('active_role') ?? Auth::user()->role; @endphp

    <!-- Grupos por papel ativo - mostra SOMENTE o grupo do papel atual -->
    <nav class="py-3 overflow-y-auto h-[calc(100vh-5rem)]">
        <!-- Dashboard - comum para todos os papéis -->
        <x-sidebar-item href="{{ route('dashboard') }}" :active="request()->is('dashboard')" icon="fa-solid fa-house">Dashboard</x-sidebar-item>
        <!-- Conectar - novo item abaixo de Dashboard -->
        <x-sidebar-item href="{{ route('connect.index') }}" :active="request()->routeIs('connect.index')" icon="fa-solid fa-share-nodes">Conectar</x-sidebar-item>
        
        <hr class="my-3">
        
        @if($role === 'freelancer')
            <x-sidebar-item href="{{ route('profiles.show', Auth::user()) }}" :active="request()->routeIs('profiles.show')" icon="fa-solid fa-user">Meu Perfil</x-sidebar-item>
            <x-sidebar-item href="{{ route('vagas.index') }}" :active="request()->routeIs('vagas.index')" icon="fa-solid fa-magnifying-glass">Buscar Vagas</x-sidebar-item>
            <x-sidebar-item href="{{ route('applications.index') }}" :active="request()->routeIs('applications.index')">
                <x-slot name="icon">
                    <i class="fa-solid fa-file-lines w-4 h-4"></i>
                </x-slot>
                Minhas Candidaturas
            </x-sidebar-item>
        @elseif($role === 'company')
            @php
                $routeExists = function($routeName) {
                    try {
                        return \Illuminate\Support\Facades\Route::has($routeName);
                    } catch (\Exception $e) {
                        return false;
                    }
                };
            @endphp
            <x-sidebar-item href="{{ route('profiles.show', Auth::user()) }}" :active="request()->routeIs('profiles.show')" icon="fa-solid fa-user">Meu Perfil</x-sidebar-item>
            <x-sidebar-item href="{{ $routeExists('job_vacancies.index') ? route('job_vacancies.index') : url('/job-vacancies') }}" :active="request()->is('job-vacancies*') || request()->is('vagas*')" icon="fa-solid fa-briefcase">Minhas Vagas</x-sidebar-item>
        @elseif($role === 'admin')
            <x-sidebar-item href="{{ url('/admin/users') }}" :active="request()->is('admin/users*')" icon="fa-solid fa-users">Usuários</x-sidebar-item>
            <x-sidebar-item href="{{ url('/admin/companies') }}" :active="request()->is('admin/companies*')" icon="fa-solid fa-building">Empresas</x-sidebar-item>
            <x-sidebar-item href="{{ url('/admin/freelancers') }}" :active="request()->is('admin/freelancers*')" icon="fa-solid fa-id-card">Freelancers</x-sidebar-item>
            <x-sidebar-item href="{{ url('/admin/settings') }}" :active="request()->is('admin/settings*')" icon="fa-solid fa-gear">Configurações</x-sidebar-item>
        @endif

        <hr class="my-3">
        <form method="POST" action="{{ route('logout') }}" class="px-2">
            @csrf
            <button @click="close()" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-50 text-red-600 hover:bg-red-50">
                <i class="fa-solid fa-right-from-bracket text-sm"></i>
                <span class="text-sm font-medium">Sair</span>
            </button>
        </form>
    </nav>
</aside>