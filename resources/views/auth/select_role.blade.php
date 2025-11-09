@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[var(--trampix-light-gray)] flex flex-col justify-center items-center py-12 px-4">
    <div class="trampix-card w-full max-w-md text-center p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="trampix-h1 mb-4">Selecione como deseja continuar</h1>
            <p class="text-gray-600 text-base">
                Escolha o perfil que deseja usar nesta sessão.
            </p>
        </div>

        <!-- Mensagens de feedback -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
                {{ session('info') }}
            </div>
        @endif

        <!-- Botões de seleção -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
            @if($hasFreelancer)
                <form action="{{ route('select-role.select') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="role" value="freelancer">
                    <button type="submit" 
                            class="btn-trampix-primary w-full sm:w-40 py-3 text-lg font-medium transition-all duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Freelancer
                        </div>
                    </button>
                </form>
            @endif

            @if($hasCompany)
                <form action="{{ route('select-role.select') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="role" value="company">
                    <button type="submit" 
                            class="btn-trampix-company w-full sm:w-40 py-3 text-lg font-medium transition-all duration-300 hover:transform hover:scale-105">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Empresa
                        </div>
                    </button>
                </form>
            @endif
        </div>

        <!-- Informação adicional -->
        <div class="text-sm text-gray-500 space-y-2">
            <p>
                Você poderá trocar de perfil depois, no menu principal.
            </p>
            @if($hasFreelancer && $hasCompany)
                <p class="text-xs text-gray-400">
                    Você possui ambos os perfis ativos e pode alternar entre eles a qualquer momento.
                </p>
            @endif
        </div>

        <!-- Link para dashboard (caso queira pular) -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <a href="{{ route('dashboard') }}" 
               class="text-sm text-gray-500 hover:text-gray-700 transition-colors duration-200">
                Ir para dashboard sem selecionar
            </a>
        </div>
    </div>

    <!-- Informações dos perfis (opcional) -->
    <div class="mt-8 max-w-2xl text-center">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-600">
            @if($hasFreelancer)
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-semibold text-purple-600 mb-2">Como Freelancer</h3>
                    <ul class="text-left space-y-1">
                        <li>• Candidatar-se a vagas</li>
                        <li>• Gerenciar portfólio</li>
                        <li>• Acompanhar aplicações</li>
                    </ul>
                </div>
            @endif

            @if($hasCompany)
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-semibold text-green-600 mb-2">Como Empresa</h3>
                    <ul class="text-left space-y-1">
                        <li>• Publicar vagas</li>
                        <li>• Gerenciar candidaturas</li>
                        <li>• Buscar talentos</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection