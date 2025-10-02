<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Trampix') }}</title>

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Custom Styles -->
        @stack('styles')
    </head>
    <body>
        <div id="app">
            @include('layouts.navigation')

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
            <main class="py-4">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
