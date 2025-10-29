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
                transition: all 0.3s ease;
            }

            .sidebar-card {
                background: transparent;
                border: none;
                border-radius: 12px;
                padding: 16px;
                margin: 8px 12px;
                transition: all 0.3s ease;
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
                transform: translateX(4px);
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
                transform: translateX(4px);
            }

            .sidebar-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                transition: all 0.3s ease;
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
            }

            /* Sidebar behavior - same for all screen sizes */
            .trampix-sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 40;
                height: 100vh;
                top: 0;
                width: 280px;
                transition: transform 0.3s ease-in-out;
            }
            
            .trampix-sidebar.sidebar-open {
                transform: translateX(0);
            }
        </style>
    </head>
    <body>
        <!-- Main Content -->
        <div class="min-h-screen bg-gray-50">
            
            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content Area -->
            <div class="w-full flex flex-col">
                <!-- Top Navigation Bar -->
                <header class="bg-white shadow-sm border-b sticky top-0 z-30">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <!-- Menu Toggle & Logo -->
                        <div class="flex items-center space-x-3">
                            <!-- Sidebar Toggle -->
                            <button class="p-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-gray-100 transition-colors" 
                                    onclick="toggleSidebar()">
                                <i class="fas fa-bars text-lg"></i>
                            </button>
                            
                            <!-- Logo -->
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-briefcase text-white text-sm"></i>
                                </div>
                                <h2 class="text-purple-600 font-bold text-lg">Trampix</h2>
                            </div>
                        </div>

                        <!-- Page Title -->
                        <div class="flex-1 px-4 text-center">
                            @if(isset($header))
                                {{ $header }}
                            @elseif (View::hasSection('header'))
                                @yield('header')
                            @endif
                        </div>

                        <!-- Right Side Actions -->
                        <div class="flex items-center space-x-4">
                            <!-- User Profile -->
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
                            
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-gray-200">
                                    @if($profilePhoto)
                                        <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                             alt="Foto de Perfil" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-400 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="hidden md:block">
                                    <p class="text-gray-900 font-medium text-sm">{{ Auth::user()->name }}</p>
                                </div>
                            </div>
                            
                            <!-- Notifications -->
                            <button class="p-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-bell text-lg"></i>
                            </button>
                            
                            <!-- Profile Link -->
                            <a href="{{ route('profiles.show', auth()->user()) }}" 
                               class="p-2 rounded-lg text-gray-600 hover:text-purple-600 hover:bg-gray-100 transition-colors">
                                <i class="fas fa-user-circle text-lg"></i>
                            </a>
                            
                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-2 rounded-lg text-gray-600 hover:text-red-600 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sign-out-alt text-lg"></i>
                                </button>
                            </form>
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
        
        <!-- Sidebar Toggle Script -->
        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('sidebar-open');
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const toggleButton = event.target.closest('[onclick="toggleSidebar()"]');
                
                if (!sidebar.contains(event.target) && !toggleButton && sidebar.classList.contains('sidebar-open')) {
                    sidebar.classList.remove('sidebar-open');
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const sidebar = document.getElementById('sidebar');
                
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('sidebar-open');
                }
            });
        </script>
        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>