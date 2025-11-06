@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header de Boas-vindas -->
    <div class="mb-8">
        <h1 class="trampix-h1">
            Olá, 
            <span class="trampix-user-name">
                {{ auth()->user()->display_name }}
            </span>! 👋
        </h1>
        <p class="text-gray-600 mt-2">
            @if(auth()->user()->isAdmin())
                Bem-vindo ao painel administrativo do Trampix
            @elseif(auth()->user()->isCompany())
                Gerencie suas vagas e candidaturas
            @elseif(auth()->user()->isFreelancer())
                Encontre as melhores oportunidades para você
            @else
                Complete seu perfil para começar a usar o Trampix
            @endif
        </p>
    </div>

    <!-- Conteúdo Dinâmico baseado no tipo de usuário -->
    @if(auth()->user()->isCompany())
        @include('dashboard.partials.company')
    @elseif(auth()->user()->isFreelancer())
        @include('dashboard.partials.freelancer')
    @else
        <!-- Usuário sem perfil definido -->
        <div class="text-center py-12">
            <div class="trampix-card max-w-md mx-auto">
                <div class="text-center">
                    <i class="fas fa-user-plus text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Complete seu perfil</h3>
                    <p class="text-gray-600 mb-6">Para começar a usar o Trampix, você precisa definir seu tipo de perfil.</p>
                    <div class="space-y-3">
                        <a href="{{ route('profile.selection') }}" class="btn-trampix-primary w-full">
                            Completar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações do Sistema -->
    <section class="pt-6 border-t border-gray-200 mt-8">
        <div class="text-center">
            <small class="text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Dashboard Trampix - Navegação intuitiva e dinâmica
            </small>
        </div>
    </section>
</div>
@endsection
