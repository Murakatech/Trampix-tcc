@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-building text-purple-600 mr-2"></i>
    Dashboard Empresa
</h1>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- Cabeçalho de Boas-vindas -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">
                    Bem-vinda, {{ $company->name }}! 🏢
                </h2>
                <p class="text-green-100">
                    Gerencie suas vagas, acompanhe candidaturas e encontre os melhores talentos para sua empresa.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-users text-6xl text-green-300"></i>
            </div>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-chart-line text-green-500 mr-2"></i>
            Estatísticas Gerais
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Vagas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $jobStats['total'] }}</div>
                <div class="text-gray-600 font-medium">Total de Vagas</div>
                <i class="fas fa-briefcase text-green-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Vagas Abertas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $jobStats['open'] }}</div>
                <div class="text-gray-600 font-medium">Vagas Abertas</div>
                <i class="fas fa-door-open text-blue-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Vagas Fechadas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-gray-600 mb-2">{{ $jobStats['closed'] }}</div>
                <div class="text-gray-600 font-medium">Vagas Fechadas</div>
                <i class="fas fa-door-closed text-gray-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Total de Candidaturas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $jobStats['total_applications'] }}</div>
                <div class="text-gray-600 font-medium">Total de Candidaturas</div>
                <i class="fas fa-user-check text-purple-300 text-2xl mt-2"></i>
            </div>
        </div>
    </section>

    <!-- Ações Rápidas -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                Ações Rápidas
            </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('job-vacancies.create') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform bg-green-50 border-green-200">
                <i class="fas fa-plus text-green-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Criar Nova Vaga</h4>
                <p class="text-gray-600 text-sm">Publique uma nova oportunidade</p>
            </a>
            
            <a href="{{ route('applications.manage') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform">
                <i class="fas fa-users text-blue-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Gerenciar Candidaturas</h4>
                <p class="text-gray-600 text-sm">Analise candidatos</p>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="trampix-card text-center hover:scale-[1.02] transition-transform">
                <i class="fas fa-building-user text-purple-500 text-3xl mb-3"></i>
                <h4 class="font-bold text-gray-900 mb-2">Editar Perfil</h4>
                <p class="text-gray-600 text-sm">Atualize dados da empresa</p>
            </a>
        </div>
    </section>

    <!-- Minhas Vagas -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                <i class="fas fa-list text-green-500 mr-2"></i>
                Minhas Vagas
            </h3>
            <a href="{{ route('job-vacancies.create') }}" class="btn-trampix-primary">
                <i class="fas fa-plus mr-2"></i>
                Criar Nova Vaga
            </a>
        </div>
        
        @if($jobs->count() > 0)
            <div class="space-y-4">
                @foreach($jobs as $job)
                    <div class="trampix-card">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex-1 mb-4 lg:mb-0">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="text-xl font-bold text-gray-900">{{ $job->title }}</h4>
                                    <span class="ml-4 px-3 py-1 rounded-full text-sm font-medium 
                                        {{ $job->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $job->status === 'open' ? 'Aberta' : 'Fechada' }}
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 mb-3 line-clamp-2">{{ Str::limit($job->description, 150) }}</p>
                                
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    <span>
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $job->applications_count }} candidato{{ $job->applications_count !== 1 ? 's' : '' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        Criada em {{ $job->created_at->format('d/m/Y') }}
                                    </span>
                                    @if($job->salary_min && $job->salary_max)
                                        <span>
                                            <i class="fas fa-dollar-sign mr-1"></i>
                                            R$ {{ number_format($job->salary_min, 0, ',', '.') }} - R$ {{ number_format($job->salary_max, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-3 lg:ml-6">
                                <a href="{{ route('job-vacancies.show', $job) }}" 
                                   class="btn-trampix-secondary text-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    Visualizar
                                </a>
                                
                                <a href="{{ route('job-vacancies.edit', $job) }}" 
                                   class="btn-trampix-secondary text-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Editar
                                </a>
                                
                                <a href="{{ route('applications.job', $job) }}" 
                                   class="btn-trampix-primary text-center relative">
                                    <i class="fas fa-users mr-2"></i>
                                    Ver Candidatos
                                    @if($job->applications_count > 0)
                                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                            {{ $job->applications_count }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Paginação se necessário -->
            @if($jobs->hasPages())
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif
        @else
            <div class="trampix-card text-center py-12">
                <i class="fas fa-briefcase text-gray-300 text-6xl mb-4"></i>
                <h4 class="text-xl font-semibold text-gray-600 mb-2">Nenhuma vaga criada ainda</h4>
                <p class="text-gray-500 mb-6">Comece criando sua primeira vaga para atrair talentos!</p>
                <a href="{{ route('job-vacancies.create') }}" class="btn-trampix-primary">
                    <i class="fas fa-plus mr-2"></i>
                    Criar Primeira Vaga
                </a>
            </div>
        @endif
    </section>

    <!-- Candidaturas Recentes -->
    @if($recentApplications->count() > 0)
        <section>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                    Candidaturas Recentes
                </h3>
                <a href="{{ route('applications.manage') }}" class="btn-trampix-secondary">
                    <i class="fas fa-list mr-2"></i>
                    Ver todas
                </a>
            </div>
            
            <div class="trampix-card">
                <div class="space-y-4">
                    @foreach($recentApplications as $application)
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $application->freelancer->user->name }}</h4>
                                <p class="text-sm text-gray-600">Candidatou-se para: {{ $application->jobVacancy->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $application->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendente',
                                        'accepted' => 'Aceita',
                                        'rejected' => 'Rejeitada'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$application->status] }}">
                                    {{ $statusLabels[$application->status] }}
                                </span>
                                <a href="{{ route('applications.show', $application) }}" 
                                   class="text-purple-600 hover:text-purple-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</div>
@endsection