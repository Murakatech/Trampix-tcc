<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
    <meta name="app-debug" content="{{ config('app.debug') ? 'true' : 'false' }}">
    @auth
    <meta name="user-data" content="{{ json_encode([
        'id' => auth()->id(),
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
        // Priorizar papel ativo definido na sessão; se não houver, usar fallback baseado nos perfis do usuário
        'role' => session('active_role') ?? (auth()->user()->isAdmin() ? 'admin' : (auth()->user()->isCompany() ? 'company' : 'freelancer')),
        'permissions' => [], // Expandir conforme necessário
        'profile_photo' => auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : null
    ]) }}">
    @endauth

        <title>{{ config('app.name', 'Trampix') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('storage/img/logo_trampix.png') }}">

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

            /* Reset global para evitar faixa branca padrão do body */
            html, body { margin: 0; padding: 0; }

            /* Tipografia básica do styleguide */
            .trampix-h1 { color: #000000; font-weight: 700; font-size: 2rem; text-align: center; }
            .trampix-h2 { color: var(--trampix-black); font-weight: 500; font-size: 2rem; }
            .trampix-h3 { color: #1ca751; font-weight: 600; font-size: 1.5rem; }
            .trampix-p { color: var(--trampix-dark-gray); font-weight: 400; }

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
        </style>
    </head>
    <body>
        <div id="app">
            @if (!request()->routeIs('vagas.index'))
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @if(isset($header))
                <header class="bg-white shadow-sm border-bottom">
                    <div class="container py-3">
                        {{ $header }}
                    </div>
                </header>
            @elseif (View::hasSection('header'))
                <header class="bg-white shadow-sm border-bottom">
                    <div class="container py-3">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="{{ request()->routeIs('vagas.index') ? '' : 'py-4' }}">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Input masks and client-side validation helpers -->
        <script src="{{ asset('js/masks.js') }}"></script>
        

        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
