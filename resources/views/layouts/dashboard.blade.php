@php
    // Definir display name baseado no perfil ativo
    $activeRole = session('active_role');
    $displayName = Auth::user()->name;
    
    if ($activeRole === 'freelancer' && Auth::user()->freelancer) {
        $displayName = Auth::user()->freelancer->display_name ?? Auth::user()->name;
    } elseif ($activeRole === 'company' && Auth::user()->company) {
        $displayName = Auth::user()->company->display_name ?? Auth::user()->name;
    }
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Trampix') }} - Dashboard</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])


        <!-- Custom Styles -->
        @stack('styles')

        <!-- Trampix Styleguide (global tokens + buttons) -->
        <style>
            :root {
                --trampix-purple: #8F3FF7;
                --trampix-green: #B9FF66;
                --trampix-black: #191A23;
                --trampix-light-gray: #F3F3F3;
                --trampix-red: #FF4C4C;
                --trampix-dark-gray: #4A4A4A;
            }

            /* Tipografia básica do styleguide */
            .trampix-h1 { color: var(--trampix-purple); font-weight: 700; font-size: 2.5rem; }
            .trampix-h2 { color: var(--trampix-black); font-weight: 500; font-size: 2rem; }
            .trampix-h3 { color: var(--trampix-green); font-weight: 600; font-size: 1.5rem; }
            .trampix-p { color: var(--trampix-dark-gray); font-weight: 400; }

            /* Cards Trampix */
            .trampix-card {
                background: white;
                border-radius: 16px;
                padding: 24px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                border: 1px solid #e5e7eb;
                transition: all 0.3s ease;
                text-decoration: none;
                color: inherit;
                display: block;
            }
            .trampix-card:hover {
                box-shadow: 0 10px 25px rgba(143, 63, 247, 0.15);
                transform: translateY(-2px);
                text-decoration: none;
                color: inherit;
            }

            /* Cabeçalho de tabela com identidade Trampix (override Bootstrap) */
            table.table thead.trampix-table-header { background-color: var(--trampix-light-gray) !important; }
            table.table thead.trampix-table-header th { color: var(--trampix-black) !important; font-weight: 600; letter-spacing: .02em; padding-top: .75rem; padding-bottom: .75rem; }
            
            .btn-trampix-primary {
                display: inline-flex; align-items: center; justify-content: center;
                background-color: var(--trampix-purple);
                border: 1px solid var(--trampix-purple);
                color: #fff; border-radius: 12px;
                padding: 12px 24px; font-weight: 500; transition: all .3s ease;
                text-decoration: none;
            }
            .btn-trampix-primary:hover {
                background-color: var(--trampix-green);
                border-color: var(--trampix-green);
                color: var(--trampix-black);
                transform: translateY(-2px);
            }
            .btn-trampix-secondary {
                display: inline-flex; align-items: center; justify-content: center;
                background-color: var(--trampix-light-gray);
                border: 2px solid var(--trampix-purple);
                color: var(--trampix-black); border-radius: 12px;
                padding: 12px 24px; font-weight: 500; transition: all .3s ease;
                text-decoration: none;
            }
            .btn-trampix-secondary:hover {
                background-color: var(--trampix-green);
                border-color: var(--trampix-green);
                color: var(--trampix-black);
                transform: translateY(-2px);
            }
            .btn-trampix-danger {
                display: inline-flex; align-items: center; justify-content: center;
                background-color: var(--trampix-red);
                border: 1px solid var(--trampix-red);
                color: #fff; border-radius: 12px;
                padding: 12px 24px; font-weight: 500; transition: all .3s ease;
                text-decoration: none;
            }
            .btn-trampix-danger:hover {
                background-color: #e63946;
                border-color: #e63946;
                color: #fff;
                transform: translateY(-2px);
            }
            .btn-glow { box-shadow: 0 0 0 rgba(0,0,0,0); transition: box-shadow .25s, transform .15s; }
            .btn-glow:hover { box-shadow: 0 10px 25px rgba(143, 63, 247, .25); transform: translateY(-1px); }

            /* Header navigation styles */
            .header-nav {
                background: white;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            /* Sidebar styles */
            .trampix-sidebar {
                background: white;
                border-right: 1px solid #e5e7eb;
                box-shadow: 2px 0 4px rgba(0, 0, 0, 0.05);
                transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            }

            .sidebar-card {
                background: transparent;
                border: none;
                border-radius: 12px;
                padding: 16px;
                margin: 8px 12px;
                transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
                text-decoration: none;
                color: var(--trampix-dark-gray);
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
            }

            .sidebar-card:hover {
                background: var(--trampix-light-gray);
                color: var(--trampix-purple);
                transform: translateX(8px);
                text-decoration: none;
            }

            .sidebar-card.active {
                background: linear-gradient(135deg, var(--trampix-purple), #a855f7);
                color: white;
                box-shadow: 0 4px 12px rgba(143, 63, 247, 0.3);
            }

            .sidebar-card.active:hover {
                background: linear-gradient(135deg, #7c3aed, var(--trampix-purple));
                color: white;
                transform: translateX(8px);
            }

            .sidebar-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            }

            .sidebar-card:not(.active) .sidebar-icon {
                background: var(--trampix-light-gray);
                color: var(--trampix-purple);
            }

            .sidebar-card.active .sidebar-icon {
                background: rgba(255, 255, 255, 0.2);
                color: white;
            }

            .sidebar-card:hover:not(.active) .sidebar-icon {
                background: var(--trampix-purple);
                color: white;
            }

            .sidebar-text {
                font-weight: 500;
                font-size: 14px;
                transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            }

            /* Sidebar behavior - fixed position, follows scroll */
            .trampix-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 20;
                height: 100vh;
                flex-shrink: 0;
                transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            }
            
            /* Main content offset to account for fixed sidebar */
            .main-content-offset {
                margin-left: 5rem; /* 80px - collapsed sidebar width */
                transition: margin-left 0.3s ease-in-out;
            }
            
            /* Expanded sidebar offset */
            .main-content-offset.expanded {
                margin-left: 14rem; /* 224px - expanded sidebar width */
            }
        </style>
    </head>
    <body class="bg-gray-50 font-sans antialiased">
        
        <div class="min-h-screen" x-data="{ sidebarExpanded: false }">
            <!-- Sidebar Component -->
            <div @mouseenter="sidebarExpanded = true" @mouseleave="sidebarExpanded = false">
                <x-sidebar />
            </div>
            
            <!-- Main Content -->
            <div class="main-content-offset transition-all duration-700" 
                 :class="{ 'expanded': sidebarExpanded }"
                 style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                
                <!-- Top Navigation Bar -->
                <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center min-h-16 py-2">
                        <!-- Page Title or Welcome Message -->
                        <div class="flex-1 text-center">
                            @if(request()->routeIs('dashboard'))
                                <h1 class="text-lg font-medium text-gray-900">Painel Geral</h1>
                            @else
                                @if(isset($header))
                                    <h1 class="text-lg font-medium text-gray-900">{{ $header }}</h1>
                                @elseif (View::hasSection('header'))
                                    @yield('header')
                                @else
                                    <h1 class="text-lg font-medium text-gray-900">
                                        @switch(true)
                                            @case(request()->routeIs('vagas.*'))
                                                Vagas
                                                @break
                                            @case(request()->routeIs('profile.*'))
                                                Perfil
                                                @break
                                            @case(request()->routeIs('profiles.*'))
                                                Perfil
                                                @break
                                            @case(request()->routeIs('applications.*'))
                                                Candidaturas
                                                @break
                                            @case(request()->routeIs('admin.*'))
                                                Administração
                                                @break
                                            @case(request()->routeIs('companies.*'))
                                                Empresas
                                                @break
                                            @case(request()->routeIs('freelancers.*'))
                                                Freelancers
                                                @break
                                            @case(request()->routeIs('styleguide'))
                                                Styleguide
                                                @break
                                            @default
                                                Trampix
                                        @endswitch
                                    </h1>
                                @endif
                            @endif
                        </div>

                        <!-- Profile Icon with Dropdown -->
                        <div class="relative flex flex-col items-center" x-data="{ open: false }" @click.away="open = false">
                            <!-- Profile Photo Button -->
                            <button 
                                @click="open = !open"
                                @keydown.escape="open = false"
                                class="flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-200 hover:bg-gray-300 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 touch-manipulation"
                                aria-label="Menu do perfil"
                                aria-expanded="false"
                                :aria-expanded="open.toString()"
                            >
                                @if(Auth::user()->profile_photo_path)
                                    <img 
                                        src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                        alt="Foto de perfil de {{ $displayName }}"
                                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-white shadow-sm"
                                    >
                                @else
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-white font-medium text-xs sm:text-sm"
                                         style="{{ ($activeRole === 'company') 
                                            ? 'background: linear-gradient(to bottom right, #1ca751, var(--trampix-green));' 
                                            : 'background: linear-gradient(to bottom right, #3b82f6, #8F3FF7);' }}">
                                        {{ strtoupper(substr($displayName, 0, 1)) }}
                                    </div>
                                @endif
                            </button>

                            <!-- Etiqueta discreta abaixo do avatar com cor do papel -->
                            @php
                                $activeRole = session('active_role')
                                    ?? (Auth::user()->isCompany() ? 'company' : (Auth::user()->isFreelancer() ? 'freelancer' : null));
                            @endphp
                            <div class="mt-1 text-center">
                                @if($activeRole === 'freelancer')
                                    <span class="badge" style="background-color: rgba(143, 63, 247, 0.12); color: #3f3f46; border: 1px solid rgba(143, 63, 247, 0.25);">🧑‍💻 Freelancer</span>
                                @elseif($activeRole === 'company')
                                    <span class="badge" style="background-color: rgba(185, 255, 102, 0.18); color: #3f3f46; border: 1px solid rgba(185, 255, 102, 0.35);">🏢 Empresa</span>
                                @endif
                            </div>

                            <!-- Dropdown Menu -->
                            <div 
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-44 sm:w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="profile-menu-button"
                            >
                                <!-- Profile / Account Link -->
                                @if(Auth::user()->isAdmin())
                                    <a 
                                        href="{{ route('profile.account') }}" 
                                        class="flex items-center px-3 sm:px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-150 touch-manipulation"
                                        role="menuitem"
                                        @click="open = false"
                                    >
                                        <div class="w-4 h-4 mr-3 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user-shield text-xs text-blue-500"></i>
                                        </div>
                                        <span class="truncate">Minha Conta</span>
                                    </a>
                                @else
                                    <a 
                                        href="{{ route('profiles.show', Auth::user()) }}" 
                                        class="flex items-center px-3 sm:px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-150 touch-manipulation"
                                        role="menuitem"
                                        @click="open = false"
                                    >
                                        <div class="w-4 h-4 mr-3 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-xs text-blue-500"></i>
                                        </div>
                                        <span class="truncate">Visualizar Perfil</span>
                                    </a>
                                @endif

                                <!-- Divider -->
                                <hr class="my-1 border-gray-200">
                                
                                <!-- Logout Button -->
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <button 
                                        type="submit"
                                        class="flex items-center w-full px-3 sm:px-4 py-2 text-sm text-red-600 hover:bg-red-50 active:bg-red-100 transition-colors duration-150 touch-manipulation"
                                        role="menuitem"
                                        @click="open = false"
                                    >
                                        <div class="w-4 h-4 mr-3 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-sign-out-alt text-xs text-red-500"></i>
                                        </div>
                                        <span class="truncate">Sair</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </main>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Input masks and URL validators (CNPJ, LinkedIn, Portfolio) -->
        <script src="{{ asset('js/masks.js') }}"></script>
        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>