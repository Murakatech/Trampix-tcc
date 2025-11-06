@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-gauge text-purple-600 mr-2"></i>
    Dashboard Admin
</h1>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Boas-vindas -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">
                    Bem-vindo(a), <span class="trampix-user-name">{{ auth()->user()->display_name ?? auth()->user()->name }}</span>! 🛠️
                </h2>
                <p class="text-purple-100">
                    Use as ferramentas abaixo para administrar freelancers, empresas, vagas e candidaturas.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-user-shield text-6xl text-purple-300"></i>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-bolt text-purple-500 mr-2"></i>
            Ações Rápidas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-id-card text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Freelancers</p>
                        <p class="text-gray-900 font-semibold">Listar e gerenciar</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.companies') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Empresas</p>
                        <p class="text-gray-900 font-semibold">Listar e gerenciar</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-tags text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Categorias</p>
                        <p class="text-gray-900 font-semibold">Gerenciar</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.applications') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-user-check text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Candidaturas</p>
                        <p class="text-gray-900 font-semibold">Acompanhar</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.vagas.create') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-plus text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Criar Vaga</p>
                        <p class="text-gray-900 font-semibold">Nova oportunidade</p>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <!-- Informações -->
    <section class="pt-6 border-t border-gray-200">
        <div class="text-center">
            <small class="text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Painel administrativo do Trampix
            </small>
        </div>
    </section>
</div>
@endsection