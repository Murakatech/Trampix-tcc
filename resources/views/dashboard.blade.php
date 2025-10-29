@php
    use Illuminate\Support\Facades\Gate;
@endphp

@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<!-- Navegação Principal por Cards -->
<div class="mb-8">
    <h2 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
        <i class="fas fa-compass text-purple-500 mr-2"></i> Navegação Rápida
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <!-- Buscar Vagas - Sempre visível -->
        <a href="{{ route('vagas.index') }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('vagas.index') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-search text-purple-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Buscar Vagas</p>
        </a>

        <!-- Ver Perfil - Sempre visível -->
        <a href="{{ route('profiles.show', auth()->user()) }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('profiles.show') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-user-circle text-blue-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Ver Perfil</p>
        </a>

        <!-- Editar Perfil - Sempre visível -->
        <a href="{{ route('profile.edit') }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('profile.edit') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-edit text-green-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Editar Perfil</p>
        </a>

        @if(Gate::allows('isCompany'))
        <!-- Nova Vaga - Empresa -->
        <a href="{{ route('vagas.create') }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('vagas.create') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-plus-circle text-green-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Nova Vaga</p>
        </a>
        @endif

        @if(Gate::allows('isFreelancer'))
        <!-- Candidaturas - Freelancer -->
        <a href="{{ route('applications.index') }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('applications.index') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-clipboard-list text-blue-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Candidaturas</p>
        </a>
        @endif

        <!-- Styleguide - Sempre visível -->
        <a href="{{ route('styleguide') }}" class="trampix-card text-center hover:scale-105 transition-all duration-300 {{ request()->routeIs('styleguide') ? 'ring-2 ring-purple-500' : '' }}">
            <i class="fas fa-palette text-orange-500 text-2xl mb-2"></i>
            <p class="text-sm font-medium text-gray-900">Styleguide</p>
        </a>
    </div>
</div>

<div class="space-y-8">

    {{-- Seção ADMIN --}}
    @if(Gate::allows('isAdmin'))
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-cog text-purple-500 mr-2"></i> Área Administrativa
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Freelancers</p>
                            <p class="text-sm text-gray-500">Gerenciar freelancers cadastrados</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.companies') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-building text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Empresas</p>
                            <p class="text-sm text-gray-500">Gerenciar empresas cadastradas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.applications') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Candidaturas</p>
                            <p class="text-sm text-gray-500">Monitorar todas candidaturas</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Seção EMPRESA --}}
    @if(Gate::allows('isCompany'))
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-briefcase text-green-500 mr-2"></i> Área da Empresa
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('vagas.create') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Nova Vaga</p>
                            <p class="text-sm text-gray-500">Criar nova oportunidade</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Minhas Vagas</p>
                            <p class="text-sm text-gray-500">Gerenciar vagas publicadas</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Seção FREELANCER --}}
    @if(Gate::allows('isFreelancer'))
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-user text-blue-500 mr-2"></i> Área do Freelancer
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Minhas Candidaturas</p>
                            <p class="text-sm text-gray-500">Acompanhar candidaturas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-search text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Buscar Vagas</p>
                            <p class="text-sm text-gray-500">Encontrar oportunidades</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Informações do Sistema --}}
    <section class="pt-6 border-t border-gray-200">
        <div class="text-center">
            <small class="text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Dashboard Trampix - Navegação intuitiva sem botões tradicionais
            </small>
        </div>
    </section>

</div>
@endsection
