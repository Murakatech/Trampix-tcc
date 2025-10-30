@extends('layouts.dashboard')

@section('header')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <h1 class="trampix-h1">Vagas Disponíveis</h1>
    <div class="mt-4 sm:mt-0">
        <span class="text-sm text-gray-600">{{ $vagas->total() }} vagas encontradas</span>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Animações para os cards */
    .job-card {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Efeito de linha limitada para descrição */
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

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

    /* Loading state para filtros */
    .filter-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Transições suaves para badges */
    .badge {
        transition: all 0.2s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Melhorias de responsividade */
    @media (max-width: 640px) {
        .job-card {
            margin-bottom: 1rem;
        }
        
        .job-card .btn-trampix-primary,
        .job-card .btn-trampix-secondary,
        .job-card .btn-trampix-outline {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .trampix-card {
            padding: 1rem;
        }
        
        .grid {
            gap: 1rem;
        }
    }

    /* Melhorias de acessibilidade */
    @media (prefers-reduced-motion: reduce) {
        .job-card,
        .badge,
        .company-profile-btn {
            transition: none;
            animation: none;
        }
        
        .fadeInUp {
            animation: none;
            opacity: 1;
            transform: none;
        }
    }

    /* Alto contraste */
    @media (prefers-contrast: high) {
        .trampix-card {
            border: 2px solid #000;
        }
        
        .btn-trampix-primary {
            border: 2px solid #000;
        }
        
        .btn-trampix-secondary {
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
        
        .text-gray-600 {
            color: #d1d5db;
        }
    }
</style>
@endpush

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
    document.querySelectorAll('.company-profile-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const companyId = this.dataset.companyId;
            const companyName = this.dataset.companyName;
            
            try {
                // Adicionar loading ao botão
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Carregando...';
                this.disabled = true;
                
                // Simular verificação de empresa (substituir por chamada real)
                await new Promise(resolve => setTimeout(resolve, 500));
                
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
    });

    // Funcionalidade dos filtros
    const filterForm = document.getElementById('filterForm');
    const filterToggle = document.getElementById('toggleFilters');
    const filterSection = document.getElementById('filtersContent');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Toggle do painel de filtros com animação suave
    if (filterToggle && filterSection) {
        filterToggle.addEventListener('click', function() {
            const isHidden = filterSection.classList.contains('hidden');
            const icon = this.querySelector('i');
            
            if (isHidden) {
                filterSection.classList.remove('hidden');
                filterSection.style.maxHeight = '0';
                filterSection.style.overflow = 'hidden';
                
                // Animar abertura
                requestAnimationFrame(() => {
                    filterSection.style.transition = 'max-height 0.3s ease-out';
                    filterSection.style.maxHeight = filterSection.scrollHeight + 'px';
                });
                
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                filterSection.style.maxHeight = '0';
                
                setTimeout(() => {
                    filterSection.classList.add('hidden');
                    filterSection.style.maxHeight = '';
                    filterSection.style.overflow = '';
                    filterSection.style.transition = '';
                }, 300);
                
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    }

    // Auto-submit em mudanças nos selects com tratamento de erro
    document.querySelectorAll('#contract_type, #location_type').forEach(select => {
        select.addEventListener('change', function() {
            try {
                showLoading();
                filterForm.submit();
            } catch (error) {
                console.error('Erro ao aplicar filtro:', error);
                showNotification('Erro ao aplicar filtro. Tente novamente.', 'error');
                hideLoading();
            }
        });
    });

    // Funcionalidade de busca com debounce e tratamento de erro
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                try {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        showLoading();
                        filterForm.submit();
                    }
                } catch (error) {
                    console.error('Erro na busca:', error);
                    showNotification('Erro na busca. Tente novamente.', 'error');
                    hideLoading();
                }
            }, 500);
        });
    }

    // Funções de loading melhoradas
    function showLoading() {
        const resultsContainer = document.getElementById('resultsContainer');
        
        if (loadingIndicator && resultsContainer) {
            loadingIndicator.classList.remove('hidden');
            resultsContainer.style.opacity = '0.5';
            resultsContainer.style.pointerEvents = 'none';
        }
    }

    function hideLoading() {
        const resultsContainer = document.getElementById('resultsContainer');
        
        if (loadingIndicator && resultsContainer) {
            loadingIndicator.classList.add('hidden');
            resultsContainer.style.opacity = '1';
            resultsContainer.style.pointerEvents = 'auto';
        }
    }

    // Esconder loading quando a página carregar
    window.addEventListener('load', function() {
        hideLoading();
        document.body.classList.remove('filter-loading');
    });

    // Detectar erros de rede
    window.addEventListener('online', () => {
        showNotification('Conexão restaurada!', 'success');
    });

    window.addEventListener('offline', () => {
        showNotification('Conexão perdida. Verifique sua internet.', 'warning');
    });

    // Tratamento global de erros
    window.addEventListener('error', (event) => {
        console.error('Erro global:', event.error);
        showNotification('Ocorreu um erro inesperado. Recarregue a página se necessário.', 'error');
    });
});
</script>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Alerts de sessão --}}
    @if (session('ok'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ session('ok') }}
        </div>
    @endif

    {{-- Sistema de Filtros --}}
    <div class="trampix-card" role="search" aria-label="Filtros de busca de vagas">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900" id="filters-heading">
                <i class="fas fa-filter mr-2 text-purple-600"></i>Filtros
            </h2>
            <button id="toggleFilters" 
                    class="text-purple-600 hover:text-purple-800 transition-colors duration-200"
                    aria-expanded="true"
                    aria-controls="filtersContent"
                    aria-describedby="filters-heading">
                <i class="fas fa-chevron-down" id="filterIcon"></i>
            </button>
        </div>
        
        <div id="filtersContent" class="space-y-4" aria-live="polite">
            <form method="GET" action="{{ route('vagas.index') }}" id="filterForm" class="space-y-4" role="form" aria-label="Formulário de filtros">
                {{-- Busca por texto --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-1"></i>Buscar vagas
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Digite o título da vaga, empresa ou palavra-chave..."
                               class="trampix-input w-full"
                               aria-describedby="search-help"
                               autocomplete="off">
                        <div id="search-help" class="text-xs text-gray-500 mt-1">
                            Busque por título, descrição ou requisitos da vaga
                        </div>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" 
                                class="btn-trampix-primary w-full focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                    </div>
                </div>

                {{-- Filtros por categoria e tipo --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Categorias --}}
                    <div>
                        <label for="categories" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tags mr-1"></i>Categorias
                        </label>
                        <select id="categories" 
                                name="categories[]" 
                                multiple 
                                class="trampix-input w-full h-32"
                                aria-describedby="categories-help"
                                aria-label="Selecione uma ou mais categorias">
                            @foreach($availableCategories as $category)
                                <option value="{{ $category }}" 
                                        {{ in_array($category, request('categories', [])) ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        <div id="categories-help" class="text-xs text-gray-500 mt-1">
                            Segure Ctrl (Windows) ou Cmd (Mac) para selecionar múltiplas categorias
                        </div>
                    </div>

                    {{-- Tipo de Contrato --}}
                    <div>
                        <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-1"></i>Tipo de Contrato
                        </label>
                        <select id="contract_type" 
                                name="contract_type" 
                                class="trampix-input w-full"
                                aria-label="Selecione o tipo de contrato">
                            <option value="">Todos os tipos</option>
                            @foreach($contractTypes as $type)
                                <option value="{{ $type }}" {{ request('contract_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipo de Localização --}}
                    <div>
                        <label for="location_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>Localização
                        </label>
                        <select id="location_type" 
                                name="location_type" 
                                class="trampix-input w-full"
                                aria-label="Selecione o tipo de localização">
                            <option value="">Todas as localizações</option>
                            @foreach($locationTypes as $type)
                                <option value="{{ $type }}" {{ request('location_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Botões de ação --}}
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="btn-trampix-primary focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            aria-describedby="apply-filters-help">
                        <i class="fas fa-filter mr-2"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('vagas.index') }}" 
                       class="btn-trampix-secondary focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                       aria-describedby="clear-filters-help">
                        <i class="fas fa-times mr-2"></i>Limpar Filtros
                    </a>
                </div>
                <div class="sr-only">
                    <div id="apply-filters-help">Aplicar os filtros selecionados à lista de vagas</div>
                    <div id="clear-filters-help">Remover todos os filtros aplicados</div>
                </div>
            </form>
        </div>
    </div>

    {{-- Loading indicator --}}
    <div id="loadingIndicator" class="hidden">
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
            <span class="ml-3 text-gray-600">Carregando vagas...</span>
        </div>
    </div>

    {{-- Resultados --}}
    <div id="resultsContainer">
        @if ($vagas->isEmpty())
            <div class="trampix-card text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-search text-4xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma vaga encontrada</h3>
                <p class="text-gray-600 mb-4">Tente ajustar os filtros ou remover algumas restrições.</p>
                <a href="{{ route('vagas.index') }}" class="btn-trampix-secondary">
                    <i class="fas fa-refresh mr-2"></i>Ver todas as vagas
                </a>
            </div>
        @else
            {{-- Grid de Vagas --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" 
                 role="list" 
                 aria-label="Lista de vagas disponíveis">
                @foreach ($vagas as $index => $vaga)
                    <article class="trampix-card hover:shadow-lg transition-all duration-300 transform hover:scale-[1.02] job-card focus-within:ring-2 focus-within:ring-purple-500 focus-within:ring-offset-2" 
                             style="animation-delay: {{ $index * 0.1 }}s"
                             role="listitem"
                             aria-labelledby="job-title-{{ $vaga->id }}"
                             tabindex="0">
                        {{-- Header do Card --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 id="job-title-{{ $vaga->id }}" 
                                    class="font-semibold text-gray-900 mb-2 hover:text-purple-600 transition-colors duration-200">
                                    <a href="{{ route('vagas.show', $vaga->id) }}" 
                                       class="text-decoration-none focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 rounded"
                                       aria-describedby="job-company-{{ $vaga->id }} job-description-{{ $vaga->id }}">
                                        {{ Str::limit($vaga->title, 50) }}
                                    </a>
                                </h3>
                                <div id="job-company-{{ $vaga->id }}" 
                                     class="flex items-center text-sm text-gray-600 mb-2">
                                    <i class="fas fa-building mr-2 text-purple-500" aria-hidden="true"></i>
                                    <span>{{ $vaga->company->name ?? 'Empresa não informada' }}</span>
                                </div>
                            </div>
                            
                            {{-- Status da candidatura --}}
                            @auth
                                @can('isFreelancer')
                                    @php
                                        $hasApplied = $vaga->applications()->where('freelancer_id', auth()->user()->freelancer?->id)->exists();
                                    @endphp
                                    @if ($hasApplied)
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                                  role="status"
                                                  aria-label="Você já se candidatou para esta vaga">
                                                <i class="fas fa-check mr-1" aria-hidden="true"></i>Candidatado
                                            </span>
                                        </div>
                                    @endif
                                @endcan
                            @endauth
                        </div>

                        {{-- Badges de informação --}}
                        <div class="flex flex-wrap gap-2 mb-4" role="list" aria-label="Informações da vaga">
                            @if($vaga->category)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                                      role="listitem"
                                      aria-label="Categoria: {{ $vaga->category }}">
                                    <i class="fas fa-tag mr-1" aria-hidden="true"></i>{{ $vaga->category }}
                                </span>
                            @endif
                            @if($vaga->contract_type)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                      role="listitem"
                                      aria-label="Tipo de contrato: {{ $vaga->contract_type }}">
                                    <i class="fas fa-briefcase mr-1" aria-hidden="true"></i>{{ $vaga->contract_type }}
                                </span>
                            @endif
                            @if($vaga->location_type)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                      role="listitem"
                                      aria-label="Tipo de localização: {{ $vaga->location_type }}">
                                    <i class="fas fa-map-marker-alt mr-1" aria-hidden="true"></i>{{ $vaga->location_type }}
                                </span>
                            @endif
                        </div>

                        {{-- Descrição --}}
                        <p id="job-description-{{ $vaga->id }}" 
                           class="text-sm text-gray-600 mb-4 line-clamp-3"
                           aria-label="Descrição da vaga">
                            {{ Str::limit($vaga->description, 120) }}
                        </p>

                        {{-- Salário --}}
                        @if($vaga->salary_range)
                            <div class="flex items-center text-sm text-gray-700 mb-4" 
                                 aria-label="Faixa salarial: {{ $vaga->salary_range }}">
                                <i class="fas fa-dollar-sign mr-2 text-green-500" aria-hidden="true"></i>
                                <span class="font-medium">{{ $vaga->salary_range }}</span>
                            </div>
                        @endif

                        {{-- Ações --}}
                        <div class="flex flex-col sm:flex-row gap-2 pt-4 border-t border-gray-200" 
                             role="group" 
                             aria-label="Ações disponíveis para esta vaga">
                            {{-- Ver Detalhes --}}
                            <a href="{{ route('vagas.show', $vaga->id) }}" 
                               class="btn-trampix-secondary flex-1 text-center focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                               aria-label="Ver detalhes completos da vaga {{ $vaga->title }}">
                                <i class="fas fa-eye mr-2" aria-hidden="true"></i>Ver Detalhes
                            </a>

                            {{-- Ver Perfil da Empresa --}}
                            @if($vaga->company)
                                <button type="button" 
                                        class="btn-trampix-outline flex-1 text-center company-profile-btn focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                                        data-company-id="{{ $vaga->company->id }}"
                                        data-company-name="{{ $vaga->company->name }}"
                                        aria-label="Ver perfil da empresa {{ $vaga->company->name }}"
                                        title="Ver perfil da {{ $vaga->company->name }}">
                                    <i class="fas fa-building mr-2" aria-hidden="true"></i>Perfil
                                </button>
                            @endif

                            {{-- Candidatar-se --}}
                            @auth
                                @can('isFreelancer')
                                    @if (!$hasApplied)
                                        <form action="{{ route('applications.store', $vaga->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn-trampix-primary w-full focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                                    aria-label="Candidatar-se para a vaga {{ $vaga->title }}"
                                                    onclick="return confirm('Deseja se candidatar para a vaga {{ $vaga->title }}?')">
                                                <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>Candidatar
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            @endauth

                            {{-- Ações da empresa --}}
                            @can('update', $vaga)
                                <a href="{{ route('vagas.edit', $vaga->id) }}" 
                                   class="btn-trampix-secondary flex-1 text-center focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                   aria-label="Editar vaga {{ $vaga->title }}">
                                    <i class="fas fa-edit mr-2" aria-hidden="true"></i>Editar
                                </a>
                            @endcan

                            @can('delete', $vaga)
                                <form action="{{ route('vagas.destroy', $vaga->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn-trampix-danger w-full focus:ring-2 focus:ring-red-500 focus:ring-offset-2" 
                                            aria-label="Excluir vaga {{ $vaga->title }}"
                                            onclick="return confirm('Tem certeza que deseja excluir a vaga {{ $vaga->title }}? Esta ação não pode ser desfeita.')">
                                        <i class="fas fa-trash mr-2" aria-hidden="true"></i>Excluir
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </article>
                @endforeach
            </div>
            
            {{-- Paginação --}}
            @if ($vagas->hasPages())
                <div class="flex justify-center mt-8">
                    {{ $vagas->links() }}
                </div>
            @endif
    @endif
</div>
@endsection
