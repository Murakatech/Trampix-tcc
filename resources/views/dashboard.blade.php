@php
    use Illuminate\Support\Facades\Gate;
@endphp

@extends('layouts.app')

@section('header')
<div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500">Bem-vindo, {{ Auth::user()->name }} 👋</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="space-y-10">

        {{-- Seção ADMIN --}}
        @if(Gate::allows('isAdmin'))
            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-cog text-purple-500 mr-2"></i> Área Administrativa
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-briefcase text-purple-500 mr-2"></i> Área da Empresa
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="{{ route('vagas.create') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-plus text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Nova Vaga</p>
                                <p class="text-sm text-gray-500">Criar nova vaga</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('home') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-list text-green-500 text-2xl mr-3"></i>
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
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-user text-purple-500 mr-2"></i> Área do Freelancer
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Minhas Candidaturas</p>
                                <p class="text-sm text-gray-500">Ver candidaturas</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('home') }}" class="trampix-card hover:scale-[1.02] transition">
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
</div>
@endsection
