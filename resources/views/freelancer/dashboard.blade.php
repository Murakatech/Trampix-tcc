@extends('layouts.dashboard')

@push('styles')
<style>
    /* Animações personalizadas para o dashboard */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
        }
        50% {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.8);
        }
    }
    
    /* Classes para animações */
    .animate-slide-up {
        animation: slideInUp 0.6s ease-out;
    }
    
    .animate-slide-left {
        animation: slideInLeft 0.5s ease-out;
    }
    
    .animate-slide-right {
        animation: slideInRight 0.5s ease-out;
    }
    
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    /* Hover effects aprimorados */
    .trampix-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Indicador de loading suave */
    .loading-indicator {
        position: relative;
        overflow: hidden;
    }
    
    .loading-indicator::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* Transições suaves para status badges */
    .status-badge {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .status-badge:hover {
        transform: scale(1.05);
        filter: brightness(1.1);
    }
</style>
@endpush

@section('header', 'Dashboard Freelancer')

@section('content')
<!-- Cabeçalho em Gradiente (padrão admin, adaptado ao freelancer) -->
<div class="space-y-8">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">
                    Bem-vindo(a), <span class="trampix-user-name">{{ ucwords($freelancer->display_name ?? $freelancer->user->name) }}</span>! 💼
                </h2>
                <p class="text-blue-100">
                    Acesse rapidamente suas candidaturas, encontre vagas recomendadas e atualize seu perfil.
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-user-astronaut text-6xl text-blue-300"></i>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas (mesmo padrão visual do admin) -->
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-bolt text-blue-500 mr-2"></i>
            Ações Rápidas
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('vagas.index') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-briefcase text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Buscar Vagas</p>
                        <p class="text-gray-900 font-semibold">Explorar oportunidades</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('applications.index') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-paper-plane text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Minhas Candidaturas</p>
                        <p class="text-gray-900 font-semibold">Acompanhar status</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('profile.edit') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-user-edit text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Editar Perfil</p>
                        <p class="text-gray-900 font-semibold">Atualizar informações</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('vagas.index') }}" class="trampix-card hover:shadow-lg transition-all">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-lightbulb text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Recomendações</p>
                        <p class="text-gray-900 font-semibold">Ver vagas recomendadas</p>
                    </div>
                </div>
            </a>
        </div>
    </section>

    <!-- Resumo de Estatísticas -->
    <div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="trampix-card bg-blue-50 border-blue-200">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $applicationsStats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="trampix-card bg-yellow-50 border-yellow-200">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pendentes</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $applicationsStats['pending'] }}</p>
                    </div>
                </div>
            </div>

            <div class="trampix-card bg-green-50 border-green-200">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Aceitas</p>
                        <p class="text-2xl font-bold text-green-600">{{ $applicationsStats['accepted'] }}</p>
                    </div>
                </div>
            </div>

            <div class="trampix-card bg-red-50 border-red-200">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rejeitadas</p>
                        <p class="text-2xl font-bold text-red-600">{{ $applicationsStats['rejected'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Principal: Duas Seções -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Seção 1: Últimas Aplicações -->
            <div class="trampix-card hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" id="latest-applications">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3 transition-all duration-300 hover:bg-blue-200 hover:scale-110">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 transition-colors duration-300 hover:text-blue-600">Últimas Aplicações</h2>
                        @if($recentApplications->count() > 0)
                            <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full animate-pulse transition-all duration-300 hover:bg-blue-200">
                                {{ $recentApplications->count() }} novas
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('applications.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-all duration-200 hover:scale-105">
                        Ver Todas →
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recentApplications as $index => $application)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300 hover:border-blue-300 group transform hover:scale-[1.02] application-card" 
                             style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                                        {{ Str::limit($application->jobVacancy->title, 40) }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $application->jobVacancy->category?->name ?? 'Categoria não definida' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Aplicado em {{ $application->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    @switch($application->status)
                                        @case('pending')
                                            <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Pendente
                                            </span>
                                            @break
                                        @case('accepted')
                                            <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Aceita
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                                Rejeitada
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500">Nenhuma aplicação encontrada</p>
                            <a href="{{ route('vagas.index') }}" class="btn-trampix-primary mt-4 inline-block">
                                Buscar Vagas
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Seção 2: Vagas Recomendadas -->
            <div class="trampix-card hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" id="recommended-jobs">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg mr-3 transition-all duration-300 hover:bg-green-200 hover:scale-110">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 transition-colors duration-300 hover:text-green-600">Vagas Recomendadas</h2>
                        @if($recommendedJobs->count() > 0)
                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full animate-pulse transition-all duration-300 hover:bg-green-200">
                                {{ $recommendedJobs->count() }} novas
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('vagas.index') }}" class="text-green-600 hover:text-green-800 text-sm font-medium transition-all duration-200 hover:scale-105">
                        Ver Todas →
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($recommendedJobs as $index => $job)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300 hover:border-green-300 group transform hover:scale-[1.02] job-card" 
                             style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-green-600 transition-colors duration-200">
                                        {{ Str::limit($job->title, 40) }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $job->category?->name ?? 'Categoria não definida' }}
                                    </p>
                                    <div class="flex items-center mt-2 text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1 transition-transform duration-200 hover:scale-110" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Publicado em {{ $job->created_at->format('d/m/Y') }}
                                    </div>
                                    @if($job->budget)
                                        <p class="text-sm font-medium text-green-600 mt-1">
                                            R$ {{ number_format($job->budget, 2, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="ml-4 flex flex-col items-end">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                        Recomendada
                                    </span>
                                    <a href="{{ route('vagas.show', $job->id) }}" 
                                       class="btn-trampix-secondary text-xs px-3 py-1 hover:bg-green-600 hover:text-white transition-all duration-200">
                                        Candidatar-se
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                            </svg>
                            <p class="text-gray-500">Nenhuma vaga recomendada no momento</p>
                            <p class="text-sm text-gray-400 mt-1">Complete seu perfil para receber recomendações personalizadas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para atualizações em tempo real -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let lastUpdateTime = null;
    
    // Função para buscar atualizações via AJAX
    async function fetchUpdates() {
        try {
            const response = await fetch('{{ route("freelancer.dashboard.updates") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                updateDashboardData(data);
                lastUpdateTime = data.lastUpdate;
            }
        } catch (error) {
            console.log('Erro ao buscar atualizações:', error);
        }
    }
    
    // Função para atualizar os dados no dashboard
    function updateDashboardData(data) {
        // Atualizar estatísticas
        const stats = data.applicationsStats;
        document.querySelector('[data-stat="total"] .text-2xl').textContent = stats.total;
        document.querySelector('[data-stat="pending"] .text-2xl').textContent = stats.pending;
        document.querySelector('[data-stat="accepted"] .text-2xl').textContent = stats.accepted;
        document.querySelector('[data-stat="rejected"] .text-2xl').textContent = stats.rejected;
        
    // Atualizar contador no header rápido (exibe total no bloco de Ações Rápidas não mais necessário)
        // Mantemos o total apenas nas estatísticas principais
        
        // Atualizar indicadores de novas aplicações
        const latestAppsIndicator = document.querySelector('#latest-applications .animate-pulse');
        if (latestAppsIndicator && data.newApplicationsCount > 0) {
            latestAppsIndicator.textContent = `${data.newApplicationsCount} novas`;
            latestAppsIndicator.classList.add('animate-pulse');
        }
        
        // Atualizar indicadores de vagas recomendadas
        const recommendedJobsIndicator = document.querySelector('#recommended-jobs .animate-pulse');
        if (recommendedJobsIndicator && data.newRecommendedJobsCount > 0) {
            recommendedJobsIndicator.textContent = `${data.newRecommendedJobsCount} novas`;
            recommendedJobsIndicator.classList.add('animate-pulse');
        }
        
        // Notificação de atualização removida permanentemente
    }
    
    // Função de notificação de atualização removida permanentemente
    
    // Polling para atualizações em tempo real (a cada 60 segundos)
    setInterval(fetchUpdates, 60000);
    
    // Buscar atualizações imediatamente após 5 segundos
    setTimeout(fetchUpdates, 5000);

    // Animação de entrada para os cards principais
    const cards = document.querySelectorAll('.trampix-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Animação de entrada para os cards individuais de aplicações
    const applicationCards = document.querySelectorAll('.application-card');
    applicationCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, 500 + (index * 150));
    });
    
    // Animação de entrada para os cards individuais de vagas
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateX(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateX(0)';
        }, 500 + (index * 150));
    });
    
    // Adicionar atributos data para facilitar atualizações
    document.querySelector('.grid .trampix-card:nth-child(1)').setAttribute('data-stat', 'total');
    document.querySelector('.grid .trampix-card:nth-child(2)').setAttribute('data-stat', 'pending');
    document.querySelector('.grid .trampix-card:nth-child(3)').setAttribute('data-stat', 'accepted');
    document.querySelector('.grid .trampix-card:nth-child(4)').setAttribute('data-stat', 'rejected');
});
</script>
@endsection
