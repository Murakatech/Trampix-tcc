@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-user text-purple-600 mr-2"></i>
    Dashboard Freelancer
</h1>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Cabeçalho de Boas-vindas -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">
                    Olá, {{ $freelancer->user->name }}! 👋
                </h2>
                <p class="text-purple-100">
                    Bem-vindo ao seu painel de freelancer. Aqui você pode acompanhar suas candidaturas e encontrar novas oportunidades.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-laptop-code text-6xl text-purple-300"></i>
            </div>
        </div>
    </div>

    <!-- Resumo de Candidaturas -->
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-chart-bar text-purple-500 mr-2"></i>
            Resumo das Candidaturas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $applicationsStats['total'] }}</div>
                <div class="text-gray-600 font-medium">Total</div>
                <i class="fas fa-clipboard-list text-purple-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Em Andamento -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $applicationsStats['pending'] }}</div>
                <div class="text-gray-600 font-medium">Em Andamento</div>
                <i class="fas fa-clock text-blue-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Aceitas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $applicationsStats['accepted'] }}</div>
                <div class="text-gray-600 font-medium">Aceitas</div>
                <i class="fas fa-check-circle text-green-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Rejeitadas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-red-600 mb-2">{{ $applicationsStats['rejected'] }}</div>
                <div class="text-gray-600 font-medium">Rejeitadas</div>
                <i class="fas fa-times-circle text-red-300 text-2xl mt-2"></i>
            </div>
        </div>
    </section>

    <!-- Barra de Busca -->
    <section>
        <div class="trampix-card">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-search text-purple-500 mr-2"></i>
                Buscar Vagas
            </h3>
            
            <form action="{{ route('vagas.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               placeholder="Digite palavras-chave, cargo ou empresa..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn-trampix-primary px-8">
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </button>
                </div>
                
                <!-- Filtros -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="area" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todas as áreas</option>
                        <option value="tecnologia">Tecnologia</option>
                        <option value="design">Design</option>
                        <option value="marketing">Marketing</option>
                        <option value="redacao">Redação</option>
                        <option value="consultoria">Consultoria</option>
                    </select>
                    
                    <select name="nivel" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todos os níveis</option>
                        <option value="junior">Júnior</option>
                        <option value="pleno">Pleno</option>
                        <option value="senior">Sênior</option>
                    </select>
                    
                    <select name="localidade" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Todas as localidades</option>
                        <option value="remoto">Remoto</option>
                        <option value="presencial">Presencial</option>
                        <option value="hibrido">Híbrido</option>
                    </select>
                </div>
            </form>
        </div>
    </section>

    <!-- Vagas Recentes/Recomendadas -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Vagas Recomendadas
            </h3>
            <a href="{{ route('vagas.index') }}" class="btn-trampix-secondary">
                <i class="fas fa-eye mr-2"></i>
                Ver todas as vagas
            </a>
        </div>
        
        @if($recentJobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentJobs as $job)
                    <a href="{{ route('vagas.show', $job) }}" class="trampix-card hover:scale-[1.02] transition-transform">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-gray-900 text-lg">{{ $job->title }}</h4>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                        
                        <p class="text-gray-600 mb-3 line-clamp-2">{{ Str::limit($job->description, 100) }}</p>
                        
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>
                                <i class="fas fa-building mr-1"></i>
                                {{ $job->company->name }}
                            </span>
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $job->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        @if($job->salary_min && $job->salary_max)
                            <div class="mt-3 text-purple-600 font-semibold">
                                R$ {{ number_format($job->salary_min, 0, ',', '.') }} - R$ {{ number_format($job->salary_max, 0, ',', '.') }}
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="trampix-card text-center py-8">
                <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">Nenhuma vaga disponível no momento.</p>
                <a href="{{ route('vagas.index') }}" class="btn-trampix-primary mt-4">
                    <i class="fas fa-search mr-2"></i>
                    Explorar Vagas
                </a>
            </div>
        @endif
    </section>

    <!-- Candidaturas Recentes -->
    @if($recentApplications->count() > 0)
        <section>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-2"></i>
                    Candidaturas Recentes
                </h3>
                <a href="{{ route('applications.index') }}" class="btn-trampix-secondary">
                    <i class="fas fa-list mr-2"></i>
                    Ver todas
                </a>
            </div>
            
            <div class="trampix-card">
                <div class="space-y-4">
                    @foreach($recentApplications as $application)
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $application->jobVacancy->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $application->jobVacancy->company->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Candidatura enviada em {{ $application->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Em Análise',
                                        'accepted' => 'Aceita',
                                        'rejected' => 'Rejeitada'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$application->status] }}">
                                    {{ $statusLabels[$application->status] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Links Rápidos -->
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-rocket text-purple-500 mr-2"></i>
            Ações Rápidas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('vagas.index') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform">
                <i class="fas fa-search text-purple-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Buscar Vagas</h4>
                <p class="text-gray-600 text-sm">Encontre novas oportunidades</p>
            </a>
            
            <a href="{{ route('applications.index') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform">
                <i class="fas fa-clipboard-list text-blue-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Minhas Candidaturas</h4>
                <p class="text-gray-600 text-sm">Acompanhe suas aplicações</p>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform">
                <i class="fas fa-user-edit text-green-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Editar Perfil</h4>
                <p class="text-gray-600 text-sm">Atualize suas informações</p>
            </a>
        </div>
    </section>

</div>
@endsection