@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">{{ $vaga->title }}</h1>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de notificações
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const bgColor = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type] || 'bg-blue-500';
        
        notification.className += ` ${bgColor} text-white`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remover após 5 segundos
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Ver Perfil da Empresa com tratamento de erro
    const companyProfileBtn = document.querySelector('.company-profile-btn');
    if (companyProfileBtn) {
        companyProfileBtn.addEventListener('click', async function() {
            const companyId = this.dataset.companyId;
            const companyName = this.dataset.companyName;
            
            try {
                // Adicionar loading ao botão
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
                this.disabled = true;
                
                // Simular verificação de empresa (substituir por chamada real)
                await new Promise(resolve => setTimeout(resolve, 800));
                
                // Restaurar botão
                this.innerHTML = originalContent;
                this.disabled = false;
                
                // Mostrar modal de confirmação
                if (confirm(`Deseja visualizar o perfil da empresa ${companyName}?`)) {
                    showNotification(`Redirecionando para o perfil da ${companyName}...`, 'info');
                    
                    // Redirecionamento real para o perfil da empresa
                    window.location.href = `/companies/${companyId}`;
                }
                
            } catch (error) {
                // Restaurar botão em caso de erro
                this.innerHTML = originalContent;
                this.disabled = false;
                
                console.error('Erro ao carregar perfil da empresa:', error);
                showNotification('Erro ao carregar perfil da empresa. Tente novamente.', 'error');
            }
        });
    }

    // Scroll suave para formulário de candidatura
    const applyButton = document.querySelector('a[href="#application-form"]');
    if (applyButton) {
        applyButton.addEventListener('click', function(e) {
            e.preventDefault();
            const applicationForm = document.getElementById('application-form');
            if (applicationForm) {
                applicationForm.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
                showNotification('Formulário de candidatura localizado!', 'info');
            }
        });
    }

    // Tratamento do formulário de candidatura
    const applicationForm = document.querySelector('form[action*="applications"]');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const coverLetter = this.querySelector('textarea[name="cover_letter"]');
            
            try {
                // Validação básica
                if (coverLetter && coverLetter.value.trim().length > 1000) {
                    e.preventDefault();
                    showNotification('A carta de apresentação deve ter no máximo 1000 caracteres.', 'warning');
                    coverLetter.focus();
                    return;
                }

                // Confirmação antes de enviar
                if (!confirm('Tem certeza que deseja se candidatar a esta vaga?')) {
                    e.preventDefault();
                    return;
                }

                // Adicionar loading ao botão
                if (submitButton) {
                    const originalContent = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
                    submitButton.disabled = true;
                    
                    // Restaurar botão após timeout (caso algo dê errado)
                    setTimeout(() => {
                        submitButton.innerHTML = originalContent;
                        submitButton.disabled = false;
                    }, 10000);
                }

                showNotification('Enviando candidatura...', 'info');

            } catch (error) {
                e.preventDefault();
                console.error('Erro ao processar candidatura:', error);
                showNotification('Erro ao processar candidatura. Tente novamente.', 'error');
                
                // Restaurar botão
                if (submitButton) {
                    submitButton.innerHTML = submitButton.dataset.originalContent || 'Candidatar-se';
                    submitButton.disabled = false;
                }
            }
        });
    }

    // Tratamento dos formulários de ação da empresa
    const deleteForm = document.querySelector('form[action*="destroy"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const confirmDelete = confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir esta vaga?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos permanentemente.');
            
            if (confirmDelete) {
                const secondConfirm = confirm('Confirma novamente a exclusão da vaga?');
                if (secondConfirm) {
                    showNotification('Excluindo vaga...', 'warning');
                    this.submit();
                }
            }
        });
    }

    // Animações suaves para badges
    const badges = document.querySelectorAll('.badge-animate');
    badges.forEach((badge, index) => {
        badge.style.animationDelay = `${index * 0.1}s`;
        badge.classList.add('animate-fadeInUp');
    });

    // Detectar erros de rede
    window.addEventListener('online', () => {
        showNotification('Conexão restaurada!', 'success');
    });

    window.addEventListener('offline', () => {
        showNotification('Conexão perdida. Algumas funcionalidades podem não funcionar.', 'warning');
    });

    // Tratamento global de erros
    window.addEventListener('error', (event) => {
        console.error('Erro global:', event.error);
        showNotification('Ocorreu um erro inesperado. Recarregue a página se necessário.', 'error');
    });

    // Validação em tempo real da carta de apresentação
    const coverLetterTextarea = document.querySelector('textarea[name="cover_letter"]');
    if (coverLetterTextarea) {
        const maxLength = 1000;
        const counter = document.createElement('div');
        counter.className = 'text-sm text-gray-500 mt-1';
        coverLetterTextarea.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - coverLetterTextarea.value.length;
            counter.textContent = `${remaining} caracteres restantes`;
            
            if (remaining < 100) {
                counter.className = 'text-sm text-yellow-600 mt-1';
            } else if (remaining < 0) {
                counter.className = 'text-sm text-red-600 mt-1 font-semibold';
                counter.textContent = `Excedeu em ${Math.abs(remaining)} caracteres!`;
            } else {
                counter.className = 'text-sm text-gray-500 mt-1';
            }
        }

        coverLetterTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Inicializar contador
    }

    // Tooltip para botões com informações adicionais
    const tooltipButtons = document.querySelectorAll('[data-tooltip]');
    tooltipButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-800 rounded shadow-lg -top-8 left-1/2 transform -translate-x-1/2';
            tooltip.textContent = this.dataset.tooltip;
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });

        button.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.absolute');
            if (tooltip) tooltip.remove();
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Hover tooltip para empresa */
    .company-profile-btn {
        position: relative;
    }

    .company-profile-btn:hover::after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 5px;
    }

    .company-profile-btn:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
    }

    /* Animação suave para badges */
    .inline-flex {
        transition: all 0.2s ease;
    }

    .inline-flex:hover {
        transform: scale(1.05);
    }

    /* Estilo para prosa */
    .prose {
        line-height: 1.6;
    }

    .prose p {
        margin-bottom: 1rem;
    }

    /* Melhorias de responsividade */
    @media (max-width: 768px) {
        .trampix-card {
            padding: 1rem;
        }
        
        .trampix-h1 {
            font-size: 1.5rem;
            line-height: 1.3;
        }
        
        .btn-trampix-primary,
        .btn-trampix-secondary,
        .btn-trampix-outline {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }
        
        .flex-col.sm\:flex-row {
            gap: 0.75rem;
        }
    }

    @media (max-width: 640px) {
        .space-y-6 {
            gap: 1rem;
        }
        
        .border-b.border-gray-200.pb-6.mb-6 {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .grid.grid-cols-1.md\:grid-cols-2 {
            gap: 1rem;
        }
    }

    /* Melhorias de acessibilidade */
    @media (prefers-reduced-motion: reduce) {
        .company-profile-btn,
        .inline-flex,
        .transition-all,
        .transition-colors {
            transition: none;
            animation: none;
        }
    }

    /* Alto contraste */
    @media (prefers-contrast: high) {
        .trampix-card {
            border: 2px solid #000;
        }
        
        .border-gray-200 {
            border-color: #000;
        }
        
        .btn-trampix-primary,
        .btn-trampix-secondary,
        .btn-trampix-outline {
            border: 2px solid #000;
        }
    }

    /* Modo escuro */
    @media (prefers-color-scheme: dark) {
        .trampix-card {
            background-color: #1f2937;
            border-color: #374151;
        }
        
        .text-gray-900 {
            color: #f9fafb;
        }
        
        .text-gray-600,
        .text-gray-700 {
            color: #d1d5db;
        }
        
        .border-gray-200 {
            border-color: #374151;
        }
        
        .bg-gray-50 {
            background-color: #374151;
        }
    }

    /* Melhorias para telas pequenas */
    @media (max-width: 480px) {
        .text-sm {
            font-size: 0.8rem;
        }
        
        .px-4.py-3 {
            padding: 0.75rem;
        }
        
        .space-x-4 > * + * {
            margin-left: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidade do botão "Ver Perfil da Empresa"
    document.querySelectorAll('.company-profile-btn').forEach(button => {
        button.addEventListener('click', function() {
            const companyId = this.dataset.companyId;
            const companyName = this.dataset.companyName;
            
            // Por enquanto, vamos mostrar um modal ou redirecionar
            // Aqui você pode implementar a lógica para mostrar o perfil da empresa
            if (confirm(`Deseja ver o perfil da empresa ${companyName}?`)) {
                // Redirecionar para a página de perfil da empresa
                // window.location.href = `/empresas/${companyId}`;
                
                // Por enquanto, vamos mostrar um alerta
                alert(`Funcionalidade em desenvolvimento.\nEmpresa: ${companyName}\nID: ${companyId}`);
            }
        });
    });

    // Animação suave para scroll até o formulário de candidatura
    const candidateButton = document.querySelector('a[href="#candidate"]');
    if (candidateButton) {
        candidateButton.addEventListener('click', function(e) {
            e.preventDefault();
            const candidateSection = document.querySelector('#candidate-section');
            if (candidateSection) {
                candidateSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }

    // Confirmação antes de enviar candidatura
    const candidateForm = document.querySelector('form[action*="applications.store"]');
    if (candidateForm) {
        candidateForm.addEventListener('submit', function(e) {
            const coverLetter = this.querySelector('textarea[name="cover_letter"]').value;
            const message = coverLetter.trim() 
                ? 'Tem certeza que deseja enviar sua candidatura com a mensagem personalizada?'
                : 'Tem certeza que deseja enviar sua candidatura? Você pode adicionar uma mensagem personalizada para se destacar.';
            
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Alerts de sessão --}}
    @if (session('ok'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm" 
             role="alert" 
             aria-live="polite">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600" aria-hidden="true"></i>
                <span class="font-medium">{{ session('ok') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm" 
             role="alert" 
             aria-live="assertive">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mr-3 text-red-600 mt-0.5" aria-hidden="true"></i>
                <div>
                    <h4 class="font-medium mb-2">Erro na candidatura:</h4>
                    <ul class="list-disc list-inside space-y-1" role="list">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm" role="listitem">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Breadcrumb --}}
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6" 
         aria-label="Navegação estrutural">
        <a href="{{ route('vagas.index') }}" 
           class="hover:text-purple-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 rounded">
            <i class="fas fa-briefcase mr-1" aria-hidden="true"></i>Vagas
        </a>
        <i class="fas fa-chevron-right text-gray-400" aria-hidden="true"></i>
        <span class="text-gray-900 font-medium" aria-current="page">{{ $vaga->title }}</span>
    </nav>

    {{-- Card Principal da Vaga --}}
    <main class="trampix-card" role="main" aria-labelledby="job-title">
        {{-- Header com título e empresa --}}
        <header class="border-b border-gray-200 pb-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 id="job-title" class="trampix-h1 text-gray-900 mb-4">{{ $vaga->title }}</h1>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 gap-2 sm:gap-0 mb-4">
                        <div class="flex items-center text-gray-600" aria-label="Informações da empresa">
                            <i class="fas fa-building mr-2 text-purple-500" aria-hidden="true"></i>
                            <span class="font-medium">{{ $vaga->company->name ?? 'Empresa não informada' }}</span>
                        </div>
                        
                        {{-- Botão Ver Perfil da Empresa --}}
                        @if($vaga->company)
                            <button type="button" 
                                    class="btn-trampix-outline company-profile-btn focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                                    data-company-id="{{ $vaga->company->id }}"
                                    data-company-name="{{ $vaga->company->name }}"
                                    aria-label="Ver perfil da empresa {{ $vaga->company->name }}"
                                    title="Ver perfil da {{ $vaga->company->name }}">
                                <i class="fas fa-external-link-alt mr-2" aria-hidden="true"></i>Ver Perfil
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </header>

                    {{-- Badges de informação --}}
                    <div class="flex flex-wrap gap-2">
                        @if($vaga->category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                <i class="fas fa-tag mr-2"></i>{{ $vaga->category }}
                            </span>
                        @endif
                        @if($vaga->contract_type)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-briefcase mr-2"></i>{{ $vaga->contract_type }}
                            </span>
                        @endif
                        @if($vaga->location_type)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-map-marker-alt mr-2"></i>{{ $vaga->location_type }}
                            </span>
                        @endif
                        @if($vaga->status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-circle mr-2"></i>{{ ucfirst($vaga->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Salário --}}
                @if($vaga->salary_range)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 lg:min-w-[200px]">
                        <div class="flex items-center text-green-700">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            <div>
                                <p class="text-sm font-medium">Faixa Salarial</p>
                                <p class="text-lg font-bold">{{ $vaga->salary_range }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Conteúdo da vaga --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Coluna principal --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Descrição --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-file-alt mr-2 text-purple-500"></i>Descrição da Vaga
                    </h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($vaga->description ?? 'Descrição não informada.')) !!}
                    </div>
                </div>

                {{-- Requisitos --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-list-check mr-2 text-purple-500"></i>Requisitos
                    </h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($vaga->requirements ?? 'Requisitos não informados.')) !!}
                    </div>
                </div>
            </div>

            {{-- Sidebar de ações --}}
            <div class="space-y-6">
                {{-- Card de candidatura --}}
                @auth
                    @can('isFreelancer')
                        @php
                            $freelancerId = auth()->user()->freelancer->id ?? null;
                            $alreadyApplied = $freelancerId
                                ? \App\Models\Application::where('job_vacancy_id', $vaga->id)
                                    ->where('freelancer_id', $freelancerId)
                                    ->exists()
                                : false;
                        @endphp

                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            @if ($alreadyApplied)
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-check text-2xl text-green-600"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Candidatura Enviada!</h4>
                                    <p class="text-sm text-gray-600">Você já se candidatou a esta vaga. Aguarde o retorno da empresa.</p>
                                </div>
                            @else
                                <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-paper-plane mr-2 text-purple-500"></i>Candidatar-se
                                </h4>
                                
                                <form method="POST" action="{{ route('applications.store', $vaga->id) }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="cover_letter" class="block text-sm font-medium text-gray-700 mb-2">
                                            Mensagem para o recrutador (opcional)
                                        </label>
                                        <textarea
                                            id="cover_letter"
                                            name="cover_letter"
                                            rows="4"
                                            class="trampix-input"
                                            placeholder="Conte um pouco sobre você e por que se interessa por esta vaga..."
                                        >{{ old('cover_letter') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn-trampix-primary w-full">
                                        <i class="fas fa-paper-plane mr-2"></i>Enviar Candidatura
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endcan
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-2xl text-purple-600"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Faça login para se candidatar</h4>
                        <p class="text-sm text-gray-600 mb-4">Você precisa estar logado como freelancer para se candidatar a esta vaga.</p>
                        <a href="{{ route('login') }}" class="btn-trampix-primary">
                            <i class="fas fa-sign-in-alt mr-2"></i>Fazer Login
                        </a>
                    </div>
                @endauth

                {{-- Ações da empresa --}}
                @can('isCompany')
                    @if(($vaga->company?->user_id) === auth()->id())
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-cog mr-2 text-blue-500"></i>Gerenciar Vaga
                            </h4>
                            
                            <div class="space-y-3">
                                <a href="{{ route('applications.byVacancy', $vaga->id) }}" 
                                   class="btn-trampix-secondary w-full text-center">
                                    <i class="fas fa-users mr-2"></i>Ver Candidatos
                                </a>
                                
                                <a href="{{ route('vagas.edit', $vaga) }}" 
                                   class="btn-trampix-outline w-full text-center">
                                    <i class="fas fa-edit mr-2"></i>Editar Vaga
                                </a>
                                
                                <form action="{{ route('vagas.destroy', $vaga) }}" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-trampix-danger w-full">
                                        <i class="fas fa-trash mr-2"></i>Excluir Vaga
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
