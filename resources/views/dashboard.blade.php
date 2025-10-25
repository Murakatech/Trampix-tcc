@php
    use Illuminate\Support\Facades\Gate;
@endphp

@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
@endsection

@section('content')
<!-- Menu de Navegação Horizontal -->
<div class="bg-white rounded-lg shadow-sm border mb-8 p-4">
    <div class="flex flex-wrap gap-4 justify-center">
        <!-- Dashboard Home -->
        <a href="{{ route('dashboard') }}" class="btn-trampix-{{ request()->routeIs('dashboard') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Dashboard
        </a>

        <!-- Buscar Vagas -->
        <a href="{{ route('vagas.index') }}" class="btn-trampix-{{ request()->routeIs('vagas.index') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-search mr-2"></i>
            Buscar Vagas
        </a>

        @if(Gate::allows('isAdmin'))
        <!-- Links ADMIN -->
        <a href="{{ route('admin.freelancers') }}" class="btn-trampix-{{ request()->routeIs('admin.freelancers') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-users mr-2"></i>
            Freelancers
        </a>
        
        <a href="{{ route('admin.companies') }}" class="btn-trampix-{{ request()->routeIs('admin.companies') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-building mr-2"></i>
            Empresas
        </a>
        
        <a href="{{ route('admin.applications') }}" class="btn-trampix-{{ request()->routeIs('admin.applications') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-clipboard-list mr-2"></i>
            Candidaturas
        </a>
        @endif

        @if(Gate::allows('isCompany'))
        <!-- Links EMPRESA -->
        <a href="{{ route('vagas.create') }}" class="btn-trampix-{{ request()->routeIs('vagas.create') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-plus mr-2"></i>
            Nova Vaga
        </a>
        
        <a href="{{ route('vagas.index') }}" class="btn-trampix-{{ request()->routeIs('vagas.index') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-list mr-2"></i>
            Minhas Vagas
        </a>
        @endif

        @if(Gate::allows('isFreelancer'))
        <!-- Links FREELANCER -->
        <a href="{{ route('applications.index') }}" class="btn-trampix-{{ request()->routeIs('applications.index') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-clipboard-list mr-2"></i>
            Minhas Candidaturas
        </a>
        @endif

        <!-- Links PERFIL -->
        <a href="{{ route('profiles.show', auth()->user()) }}" class="btn-trampix-{{ request()->routeIs('profiles.show') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-user-circle mr-2"></i>
            Ver Perfil
        </a>
        
        <a href="{{ route('profile.edit') }}" class="btn-trampix-{{ request()->routeIs('profile.edit') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-edit mr-2"></i>
            Editar Perfil
        </a>

        <!-- Styleguide -->
        <a href="{{ route('styleguide') }}" class="btn-trampix-{{ request()->routeIs('styleguide') ? 'primary' : 'secondary' }} text-sm">
            <i class="fas fa-palette mr-2"></i>
            Styleguide
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
                <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-users text-purple-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Freelancers</p>
                            <p class="text-sm text-gray-500">Gerenciar freelancers</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.companies') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-building text-green-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Empresas</p>
                            <p class="text-sm text-gray-500">Gerenciar empresas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.applications') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-list text-blue-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Candidaturas</p>
                            <p class="text-sm text-gray-500">Ver todas candidaturas</p>
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
                <a href="{{ route('vagas.create') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-plus-circle text-green-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Nova Vaga</p>
                            <p class="text-sm text-gray-500">Criar nova vaga</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-list text-blue-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Minhas Vagas</p>
                            <p class="text-sm text-gray-500">Gerenciar vagas</p>
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
                <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-list text-blue-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Minhas Candidaturas</p>
                            <p class="text-sm text-gray-500">Ver candidaturas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-[1.02] transition">
                    <div class="flex items-center">
                        <i class="fas fa-search text-green-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-900">Buscar Vagas</p>
                            <p class="text-sm text-gray-500">Encontrar oportunidades</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Ferramentas de Desenvolvimento --}}
    <section class="pt-6 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <small class="text-gray-500">Ferramentas de Desenvolvimento</small>
            <a href="{{ route('styleguide') }}" class="btn-trampix-secondary text-sm">
                <i class="fas fa-palette mr-1"></i> Ver Styleguide
            </a>
        </div>
    </section>

</div>
@endsection
