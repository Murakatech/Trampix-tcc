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
                
                // Mostrar modal de confirmação personalizado
                showActionModal('companyProfileModal', {
                    actionType: 'generic',
                    message: `Deseja visualizar o perfil da empresa ${companyName}?`,
                    onConfirm: () => {
                        showNotification(`Redirecionando para o perfil da ${companyName}...`, 'info');
                        
                        // Redirecionamento real para o perfil da empresa
                        window.location.href = `/companies/${companyId}`;
                    },
                    onCancel: () => {
                        showNotification('Visualização cancelada.', 'info');
                    }
                });
                
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
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const coverLetter = this.querySelector('textarea[name="cover_letter"]');

            // Validação básica
            if (coverLetter && coverLetter.value.trim().length > 1000) {
                showNotification('A carta de apresentação deve ter no máximo 1000 caracteres.', 'warning');
                coverLetter.focus();
                return;
            }

            const jobTitle = (applicationForm?.dataset?.jobTitle) || '';
            const companyName = (applicationForm?.dataset?.companyName) || 'Empresa';

            showActionModal('applicationConfirmationModal', {
                actionType: 'candidatura',
                jobTitle,
                companyName,
                message: `Você está prestes a se candidatar à vaga "${jobTitle}" na empresa "${companyName}". Deseja continuar?`,
                onConfirm: () => {
                    if (submitButton) {
                        submitButton.dataset.originalContent = submitButton.innerHTML;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
                        submitButton.disabled = true;
                    }
                    applicationForm.submit();
                },
                onCancel: () => {
                    showNotification('Candidatura cancelada.', 'info');
                    if (submitButton) {
                        submitButton.innerHTML = submitButton.dataset.originalContent || submitButton.innerHTML;
                        submitButton.disabled = false;
                    }
                }
            });
        });
    }

    // Tratamento dos formulários de ação da empresa
    const deleteForm = document.querySelector('form[action*="destroy"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            showActionModal('deleteConfirmationModal', {
                actionType: 'exclusao',
                jobTitle: deleteForm.dataset.jobTitle,
                companyName: deleteForm.dataset.companyName,
                message: `⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir a vaga "${deleteForm.dataset.jobTitle}"?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos permanentemente.`,
                onConfirm: () => {
                    showNotification('Excluindo vaga...', 'warning');
                    deleteForm.submit();
                },
                onCancel: () => {
                    showNotification('Exclusão cancelada.', 'info');
                }
            });
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
    // Ver Perfil da Empresa — redirecionamento direto
    document.querySelectorAll('.company-profile-btn').forEach(button => {
        button.addEventListener('click', function() {
            const companyId = this.dataset.companyId;
            // Redirecionar diretamente para o perfil público da empresa
            window.location.href = `/companies/${companyId}`;
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

    // Confirmação antes de enviar candidatura com modal customizado
    const candidateForm = document.querySelector('form[action*="applications.store"]');
    if (candidateForm) {
        candidateForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Sempre prevenir o envio inicial
            
            // Usar novo componente ActionConfirmation
             const jobTitle = candidateForm.dataset.jobTitle || '';
             const companyName = candidateForm.dataset.companyName || 'Empresa';
             showActionModal('applicationConfirmationModal', {
                 actionType: 'candidatura',
                 jobTitle: jobTitle,
                 companyName: companyName,
                 message: `Você está prestes a se candidatar à vaga <strong>${jobTitle}</strong> na empresa <strong>${companyName}</strong>. Deseja continuar?`,
                 onConfirm: () => {
                     // Submeter formulário após confirmação
                     candidateForm.submit();
                 },
                 onCancel: () => {
                     console.log('Candidatura cancelada pelo usuário');
                 }
             });
        });
    }
});
</script>
@endpush

@section('content')
{{-- Layout Redesenhado com Seção de Aplicação Fixa --}}
<div class="min-h-screen bg-gray-50">
    {{-- Container Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        
        {{-- Alerts de sessão --}}
        @if (session('ok'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm mb-6" 
                 role="alert" 
                 aria-live="polite">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-600" aria-hidden="true"></i>
                    <span class="font-medium">{{ session('ok') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm mb-6" 
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
        <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-8" 
             aria-label="Navegação estrutural">
            <a href="{{ route('vagas.index') }}" 
               class="hover:text-purple-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 rounded px-2 py-1">
                <i class="fas fa-briefcase mr-1" aria-hidden="true"></i>Vagas
            </a>
            <i class="fas fa-chevron-right text-gray-400" aria-hidden="true"></i>
            <span class="text-gray-900 font-medium" aria-current="page">{{ $vaga->title }}</span>
        </nav>

        {{-- Layout Principal: Grid Responsivo --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            
            {{-- Conteúdo Principal da Vaga --}}
            <div class="xl:col-span-3">
                <main class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" role="main" aria-labelledby="job-title">
                    
                    {{-- Header da Vaga --}}
                    <header class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-8">
                        <div class="space-y-6">
                            {{-- Título e Empresa --}}
                            <div>
                                <h1 id="job-title" class="text-3xl lg:text-4xl font-bold mb-4 leading-tight">{{ $vaga->title }}</h1>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-center text-purple-100">
                                        <i class="fas fa-building mr-3 text-xl" aria-hidden="true"></i>
    <span class="text-lg font-medium">{{ $vaga->company->name ?? 'Empresa não informada' }}</span>
                                    </div>
                                    
                                    {{-- Botão Ver Perfil da Empresa --}}
                                    @if($vaga->company)
                                        <button type="button" 
                                                class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white font-medium rounded-lg transition-all duration-200 company-profile-btn focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-purple-600"
                                                data-company-id="{{ $vaga->company->id }}"
                                                data-company-name="{{ $vaga->company->name }}"
    aria-label="Ver perfil da empresa {{ $vaga->company->name }}"
                                                title="Ver perfil da {{ $vaga->company->name }}">
                                            <i class="fas fa-external-link-alt mr-2" aria-hidden="true"></i>Ver Perfil da Empresa
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Badges de Informação (mais destacadas) --}}
                            <div class="flex flex-wrap gap-3">
                                @php
                                    $categoryLabel = $vaga->category?->name ?? $vaga->category ?? null;
                                @endphp
                                @if($categoryLabel)
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-base font-semibold bg-white/30 text-white backdrop-blur-sm shadow-sm">
                                        <i class="fas fa-tag mr-2"></i>{{ $categoryLabel }}
                                    </span>
                                @endif
                                {{-- Tipo de contrato removido: todos os contratos são freelance --}}
                                @if($vaga->location_type)
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white/30 text-white backdrop-blur-sm shadow-sm">
                                        <i class="fas fa-map-marker-alt mr-2"></i>{{ $vaga->location_type }}
                                    </span>
                                @endif
                                @if($vaga->status)
                                    @php
                                        $statusMap = [
                                            'active' => 'Ativa',
                                            'closed' => 'Fechada',
                                            'paused' => 'Pausada',
                                        ];
                                        $statusLabel = $statusMap[$vaga->status] ?? ucfirst($vaga->status);
                                    @endphp
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-white/30 text-white backdrop-blur-sm shadow-sm">
                                        <i class="fas fa-circle mr-2"></i>{{ $statusLabel }}
                                    </span>
                                @endif
                            </div>

                            {{-- Salário --}}
                            @if($vaga->salary_range)
                                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6 inline-block">
                                    <div class="flex items-center text-white">
                                        <i class="fas fa-dollar-sign mr-3 text-2xl"></i>
                                        <div>
                                            <p class="text-sm font-medium text-purple-100">Faixa Salarial</p>
                                            <p class="text-2xl font-bold">{{ $vaga->salary_range }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </header>

                    {{-- Conteúdo da Vaga --}}
                    <div class="p-8 space-y-10">
                        {{-- Descrição --}}
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-file-alt text-purple-600"></i>
                                </div>
                                Descrição da Vaga
                            </h2>
                            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($vaga->description ?? 'Descrição não informada.')) !!}
                            </div>
                        </section>

                        {{-- Requisitos --}}
                        <section>
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-list-check text-blue-600"></i>
                                </div>
                                Requisitos
                            </h2>
                            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($vaga->requirements ?? 'Requisitos não informados.')) !!}
                            </div>
                        </section>
                    </div>
                </main>
            </div>

            {{-- Sidebar Fixa de Aplicação --}}
            <div class="xl:col-span-1">
                <div class="sticky top-6 space-y-6">
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

                        <div class="application-card bg-white rounded-xl shadow-lg border border-gray-200 p-6 lg:p-8 hover:shadow-xl transition-all duration-300">
                            @if ($alreadyApplied)
                                <div class="text-center space-y-6">
                                    <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto shadow-lg">
                                        <i class="fas fa-check text-4xl text-white"></i>
                                    </div>
                                    <div class="space-y-3">
                                        <h3 class="text-2xl font-bold text-gray-900">Candidatura Enviada!</h3>
                                        <p class="text-gray-600 leading-relaxed">Você já se candidatou a esta vaga. Aguarde o retorno da empresa.</p>
                                    </div>
                                    <div class="pt-2">
                                        <a href="{{ route('applications.index') }}" class="btn-trampix-secondary w-full">
                                            <i class="fas fa-list mr-2"></i>Ver Minhas Candidaturas
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-8">
                                    {{-- Cabeçalho do formulário --}}
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                            <i class="fas fa-paper-plane text-2xl text-white"></i>
                                        </div>
                                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Candidatar-se à Vaga</h3>
                                        <p class="text-gray-600 leading-relaxed">Envie sua candidatura e destaque-se para o recrutador</p>
                                    </div>
                                    
                                    {{-- Formulário de candidatura --}}
                                    <form method="POST" action="{{ route('applications.store', $vaga->id) }}" class="application-form space-y-6" id="applicationForm" data-job-title="{{ $vaga->title }}" data-company-name="{{ $vaga->company->name ?? 'Empresa' }}">
                                        @csrf
                                        
                                        {{-- Campo de mensagem --}}
                                        <div class="form-group">
                                            <label for="cover_letter" class="block text-sm font-bold text-gray-800 mb-4">
                                                <i class="fas fa-envelope mr-2 text-purple-500"></i>
                                                Mensagem para o recrutador
                                                <span class="text-gray-500 font-normal ml-1">(opcional)</span>
                                            </label>
                                            <div class="relative">
                                                <textarea
                                                    id="cover_letter"
                                                    name="cover_letter"
                                                    rows="10"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-y text-gray-700"
                                                    style="min-height: 200px"
                                                    placeholder="Conte um pouco sobre você, sua experiência e por que se interessa por esta vaga. Esta mensagem ajudará o recrutador a conhecer melhor seu perfil..."
                                                    maxlength="1000"
                                                >{{ old('cover_letter') }}</textarea>
                                                <div class="absolute bottom-3 right-3 text-xs text-gray-400 bg-white px-2 py-1 rounded" id="charCount">
                                                    0/1000 caracteres
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                                <p class="text-sm text-purple-700 flex items-start">
                                                    <i class="fas fa-lightbulb mr-2 mt-0.5 text-purple-500"></i>
                                                    <span><strong>Dica:</strong> Mencione suas habilidades relevantes e experiências relacionadas à vaga para se destacar</span>
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Botão de envio --}}
                                        <div class="pt-4">
                                            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:ring-4 focus:ring-purple-300" id="submitBtn">
                                                <span class="flex items-center justify-center">
                                                    <i class="fas fa-paper-plane mr-3 text-lg"></i>
                                                    <span id="btnText" class="text-lg">Enviar Candidatura</span>
                                                    <div class="hidden ml-3" id="loadingSpinner">
                                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                                                    </div>
                                                </span>
                                            </button>
                                        </div>
                                        
                                        {{-- Informação adicional --}}
                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 text-center">
                                            <p class="text-sm text-blue-700 flex items-center justify-center">
                                                <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                                                <span>Sua candidatura será enviada diretamente para a empresa. Mantenha seu perfil atualizado!</span>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endcan
                @else
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-user text-3xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Faça login para se candidatar</h3>
                        <p class="text-gray-600 mb-6 leading-relaxed">Você precisa estar logado como freelancer para se candidatar a esta vaga.</p>
                        <a href="{{ route('login') }}" class="btn-trampix-primary w-full py-3 text-lg font-semibold">
                            <i class="fas fa-sign-in-alt mr-2"></i>Fazer Login
                        </a>
                    </div>
                @endauth

                {{-- Ações da empresa --}}
                @can('isCompany')
                    @if(($vaga->company?->user_id) === auth()->id())
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-cog text-white text-xl"></i>
                                </div>
                                <h4 class="text-xl font-bold text-gray-900">Gerenciar Vaga</h4>
                            </div>
                            
                            <div class="space-y-4">
                                <a href="{{ route('applications.byVacancy', $vaga->id) }}" 
                                   class="btn-trampix-primary w-full py-3 text-lg font-semibold flex items-center justify-center group">
                                    <i class="fas fa-users mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                                    Ver Candidatos
                                </a>
                                
                                <a href="{{ route('vagas.edit', $vaga) }}" 
                                   class="btn-trampix-secondary w-full py-3 text-lg font-semibold flex items-center justify-center group">
                                    <i class="fas fa-edit mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                                    Editar Vaga
                                </a>
                                
                                <form action="{{ route('vagas.destroy', $vaga) }}" method="POST" id="deleteForm" data-job-title="{{ $vaga->title }}" data-company-name="{{ $vaga->company->name ?? 'Empresa' }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-3 px-6 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg hover:shadow-xl text-lg font-semibold flex items-center justify-center group">
                                        <i class="fas fa-trash mr-3 group-hover:scale-110 transition-transform duration-200"></i>
                                        Excluir Vaga
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para o textarea
    const coverLetterTextarea = document.getElementById('cover_letter');
    const charCountElement = document.getElementById('charCount');
    const applicationForm = document.getElementById('applicationForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const loadingSpinner = document.getElementById('loadingSpinner');

    if (coverLetterTextarea && charCountElement) {
        // Função para atualizar contador de caracteres
        function updateCharCount() {
            const currentLength = coverLetterTextarea.value.length;
            const maxLength = 1000;
            charCountElement.textContent = `${currentLength}/${maxLength} caracteres`;
            
            // Mudar cor baseado na proximidade do limite
            if (currentLength > maxLength * 0.9) {
                charCountElement.classList.add('text-red-500');
                charCountElement.classList.remove('text-gray-400', 'text-yellow-500');
            } else if (currentLength > maxLength * 0.7) {
                charCountElement.classList.add('text-yellow-500');
                charCountElement.classList.remove('text-gray-400', 'text-red-500');
            } else {
                charCountElement.classList.add('text-gray-400');
                charCountElement.classList.remove('text-yellow-500', 'text-red-500');
            }
        }

        // Atualizar contador ao digitar
        coverLetterTextarea.addEventListener('input', updateCharCount);
        
        // Inicializar contador
        updateCharCount();
    }

    // Feedback visual no envio do formulário
    if (applicationForm && submitBtn) {
        applicationForm.addEventListener('submit', function(e) {
            // Desabilitar botão e mostrar loading
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            if (btnText && loadingSpinner) {
                btnText.textContent = 'Enviando...';
                loadingSpinner.classList.remove('hidden');
            }
            
            // Simular delay mínimo para feedback visual
            setTimeout(() => {
                // O formulário será submetido normalmente
            }, 100);
        });
    }

    // Animação suave para o card de candidatura
    const applicationCard = document.querySelector('.application-card');
    if (applicationCard) {
        // Observar quando o card entra na viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, { threshold: 0.1 });
        
        observer.observe(applicationCard);
    }
});

// CSS para animação
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .application-card {
        opacity: 0;
    }
    
    .application-card.animate-fade-in-up {
        opacity: 1;
    }
    
    /* Melhorias no textarea */
    .trampix-input:focus {
        box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }
    
    /* Hover effect no botão */
    .btn-trampix-primary:hover {
        transform: translateY(-2px);
    }
    
    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .application-card {
            margin: 0 -1rem;
            border-radius: 0.75rem;
        }
        
        .form-group textarea {
            font-size: 16px; /* Previne zoom no iOS */
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush

{{-- Modais de Confirmação de Ação Customizados --}}
<x-action-confirmation 
    :actionType="'candidatura'"
    :jobTitle="$vaga->title"
    :companyName="$vaga->company->name ?? 'Empresa'"
    modalId="applicationConfirmationModal"
    :showTip="true" />

<x-action-confirmation 
    :actionType="'exclusao'"
    :jobTitle="$vaga->title"
    :companyName="$vaga->company->name ?? 'Empresa'"
    modalId="deleteConfirmationModal"
    :showTip="false" />

<x-action-confirmation 
    :actionType="'generic'"
    :jobTitle="''"
    :companyName="''"
    modalId="companyProfileModal"
    :showTip="false" />

@endsection
