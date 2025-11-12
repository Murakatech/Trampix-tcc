@extends('layouts.app')

@section('content')

    <!-- Sidebar (apenas para usuários autenticados) -->
    @auth
        <x-sidebar />
    @endauth

    <!-- Layout principal com sidebar -->
    <div class="sidebar-layout {{ auth()->check() ? 'ml-20' : '' }}">
        
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center">
                        <h1 class="trampix-h1">Vagas Disponíveis</h1>
                    </div>
                    <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ $vagas->total() }} vagas encontradas</span>
                        @guest
                            <a href="{{ route('login') }}" class="btn-trampix-secondary">Entrar</a>
                            <a href="{{ route('register') }}" class="btn-trampix-primary">Cadastrar</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

{{-- Componentes de Confirmação --}}
<x-action-confirmation 
    actionType="candidatura" 
    modalId="applicationConfirmationModal" 
    :showTip="true"
    confirmText="Ir para a vaga" />

<x-action-confirmation 
    actionType="exclusao" 
    modalId="deleteConfirmationModal" />

@push('scripts')
<script>
function showApplicationConfirmation(vagaId, jobTitle, companyName, redirectUrl) {
    showActionModal('applicationConfirmationModal', {
        actionType: 'candidatura',
        jobTitle,
        companyName,
        message: `Você será redirecionado para a página da vaga "${jobTitle}". Lá você poderá enviar sua candidatura com uma mensagem personalizada. Deseja continuar?`,
        onConfirm: () => {
            showNotification('Redirecionando para a vaga...', 'info');
            window.location.href = redirectUrl;
        },
        onCancel: () => {
            showNotification('Candidatura cancelada.', 'info');
        }
    });
}

function showDeleteConfirmation(vagaId, jobTitle, companyName) {
    showActionModal('deleteConfirmationModal', {
        actionType: 'exclusao',
        jobTitle,
        companyName,
        message: `⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir a vaga "${jobTitle}"?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos permanentemente.`,
        onConfirm: () => {
            const form = document.getElementById(`deleteForm-${vagaId}`);
            showNotification('Excluindo vaga...', 'warning');
            form?.submit();
        },
        onCancel: () => {
            showNotification('Exclusão cancelada.', 'info');
        }
    });
}
</script>
@endpush

@push('styles')
<style>
    /* Animações para os cards */
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

    /* Melhorias no layout de filtros */
    .filter-section {
        background: #ffffff;
        border: 1px solid #e5e7eb; /* cinza leve */
        border-radius: 8px;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }

    .filter-section:hover {
        box-shadow: none;
        border-color: #d1d5db; /* leve realce no hover */
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 768px) {
        .filter-grid {
            grid-template-columns: 2fr 1fr 1fr;
        }
    }

    @media (min-width: 1024px) {
        .filter-grid {
            grid-template-columns: 3fr 1fr 1fr 1fr;
        }
    }

    .filter-input-group {
        position: relative;
    }

    .filter-input-group .trampix-input {
        padding-left: 2.5rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        border-radius: 6px;
        background-color: #fff;
        border-color: #e5e7eb;
    }

    .filter-input-group .input-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        pointer-events: none;
        z-index: 10;
    }

    .filter-input-group .trampix-input:focus + .input-icon,
    .filter-input-group .trampix-input:focus-within + .input-icon {
        color: #7c3aed;
    }

    /* Melhorias nos botões dos cards */
    .card-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    @media (min-width: 640px) {
        .card-actions {
            grid-template-columns: 1fr 1fr;
        }
        
        .card-actions.has-three-buttons {
            grid-template-columns: 1fr 1fr 1fr;
        }
        
        .card-actions.has-four-buttons {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
    }

    .card-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        min-height: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .card-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .card-btn:hover:before {
        left: 100%;
    }

    .card-btn-primary {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);
    }

    .card-btn-primary:hover {
        background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%);
        box-shadow: 0 4px 8px rgba(124, 58, 237, 0.3);
        transform: translateY(-1px);
    }

    .card-btn-secondary {
        background: #f8fafc;
        color: #374151;
        border: 1px solid #d1d5db;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .card-btn-secondary:hover {
        background: #f1f5f9;
        border-color: #9ca3af;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .card-btn-outline {
        background: transparent;
        color: #7c3aed;
        border: 1px solid #7c3aed;
    }

    .card-btn-outline:hover {
        background: #7c3aed;
        color: white;
        box-shadow: 0 2px 4px rgba(124, 58, 237, 0.2);
        transform: translateY(-1px);
    }

    .card-btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .card-btn-danger:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        transform: translateY(-1px);
    }

    .card-btn:focus {
        outline: none;
        ring: 2px;
        ring-color: #7c3aed;
        ring-offset: 2px;
    }

    .card-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .card-btn:disabled:hover {
        transform: none !important;
        box-shadow: none !important;
    }

    /* Badges melhorados */
    .job-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .job-badge:hover {
        transform: scale(1.05);
    }

    .badge-category {
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%);
        color: #6d28d9;
        border: 1px solid #c4b5fd;
    }

    .badge-contract {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1d4ed8;
        border: 1px solid #93c5fd;
    }

    .badge-location {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }

    .badge-applied {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1px solid #6ee7b7;
    }

    /* Loading state para filtros */
    .filter-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Melhorias de responsividade */
    @media (max-width: 640px) {
        .job-card {
            margin-bottom: 1rem;
        }
        
        .card-btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            min-height: 2.25rem;
        }
        
        .trampix-card {
            padding: 1rem;
        }
        
        .filter-grid {
            gap: 1rem;
        }
        
        .card-actions {
            grid-template-columns: 1fr !important;
            gap: 0.5rem;
        }
    }

    /* Melhorias de acessibilidade */
    @media (prefers-reduced-motion: reduce) {
        .job-card,
        .job-badge,
        .card-btn {
            transition: none;
            animation: none;
        }
        
        .fadeInUp {
            animation: none;
            opacity: 1;
            transform: none;
        }
        
        .card-btn:hover {
            transform: none;
        }
    }

    /* Alto contraste */
    @media (prefers-contrast: high) {
        .trampix-card {
            border: 2px solid #000;
        }
        
        .card-btn-primary {
            border: 2px solid #000;
        }
        
        .card-btn-secondary {
            border: 2px solid #000;
        }
        
        .filter-section {
            border: 2px solid #000;
        }
    }

    /* Integração com Sidebar */
    .sidebar-layout {
        transition: margin-left 0.3s ease-in-out;
    }

    /* Responsividade da sidebar */
    @media (max-width: 1024px) {
        .sidebar-layout {
            margin-left: 0 !important;
        }
        
        .trampix-sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 30;
        }
        
        .trampix-sidebar.mobile-open {
            transform: translateX(0);
        }
        
        /* Ajustar navbar em mobile quando sidebar está presente */
        .trampix-navbar {
            position: relative;
            z-index: 25;
        }
    }

    /* Ajustes para quando a sidebar está expandida */
    @media (min-width: 1024px) {
        .sidebar-layout.sidebar-expanded {
            margin-left: 14rem; /* w-56 */
        }
    }

    /* Overlay para mobile */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 15;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }


</style>
@endpush

@push('scripts')
<script>
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

    // Ver Perfil da Empresa - redirecionamento direto
    document.querySelectorAll('.company-profile-btn').forEach(button => {
        button.addEventListener('click', function() {
            const companyId = this.dataset.companyId;
            
            // Redirecionamento direto para o perfil da empresa
            window.location.href = `/companies/${companyId}`;
        });
    });

    // Funcionalidade dos filtros
    const filterForm = document.getElementById('filterForm');
    const filterToggle = document.getElementById('toggleFiltersBtn');
    const filterSection = document.getElementById('filtersContent');
    const loadingIndicator = document.getElementById('loadingIndicator');

    // Wrappers globais de loading para compatibilidade com chamadas existentes
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

    // Toggle do painel de filtros com animação suave
    if (filterToggle && filterSection) {
        filterToggle.addEventListener('click', function() {
            const isHidden = filterSection.classList.contains('hidden');
            const icon = this.querySelector('i');
            const toggleText = this.querySelector('span');
            
            if (isHidden) {
                filterSection.classList.remove('hidden');
                filterSection.style.maxHeight = '0';
                filterSection.style.overflow = 'hidden';
                
                // Animar abertura
                requestAnimationFrame(() => {
                    filterSection.style.transition = 'max-height 0.3s ease-out';
                    filterSection.style.maxHeight = filterSection.scrollHeight + 'px';
                });
                
                this.setAttribute('aria-expanded', 'true');
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
                if (toggleText) toggleText.textContent = 'Ocultar';
            } else {
                filterSection.style.maxHeight = '0';
                
                setTimeout(() => {
                    filterSection.classList.add('hidden');
                    filterSection.style.maxHeight = '';
                    filterSection.style.overflow = '';
                    filterSection.style.transition = '';
                }, 300);
                
                this.setAttribute('aria-expanded', 'false');
                if (icon) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
                if (toggleText) toggleText.textContent = 'Mostrar';
            }
        });
    }

    // [REMOVIDO] Auto-submit duplicado que acionava reload; unificado no bloco de busca suave abaixo

    // Funcionalidade de busca com debounce e tratamento de erro
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            
            // Feedback visual durante a digitação
            const inputGroup = this.closest('.filter-input-group');
            const icon = inputGroup?.querySelector('.input-icon');
            
            if (icon) {
                icon.className = 'fas fa-spinner fa-spin input-icon';
            }
            
            searchTimeout = setTimeout(() => {
                try {
                    if (this.value.length >= 3 || this.value.length === 0) {
                        const form = this.closest('form');
                        if (form) {
                            form.classList.add('filter-loading');
                        }
                        
                        showLoading();
                        // Disparar submit para o listener de busca suave do formulário
                        form.dispatchEvent(new Event('submit', { cancelable: true }));
                    } else {
                        // Restaurar ícone original
                        if (icon) {
                            icon.className = 'fas fa-search input-icon';
                        }
                    }
                } catch (error) {
                    console.error('Erro na busca:', error);
                    showNotification('Erro na busca. Tente novamente.', 'error');
                    hideLoading();
                    
                    // Restaurar ícone original
                    if (icon) {
                        icon.className = 'fas fa-search input-icon';
                    }
                }
            }, 500);
        });
        
        // Restaurar ícone quando o campo perde o foco
        searchInput.addEventListener('blur', function() {
            const inputGroup = this.closest('.filter-input-group');
            const icon = inputGroup?.querySelector('.input-icon');
            
            if (icon && !icon.classList.contains('fa-spin')) {
                icon.className = 'fas fa-search input-icon';
            }
        });

        // Submeter imediatamente ao pressionar Enter no campo de busca
        searchInput.addEventListener('keydown', function(e){
            if (e.key === 'Enter'){
                e.preventDefault();
                const form = this.closest('form');
                if (form){
                    showLoading();
                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        });
    }

    // Funções de loading melhoradas
    // Mantemos o controle de loading no bloco de busca suave

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

    // Melhorar acessibilidade dos botões
    document.querySelectorAll('.card-btn').forEach(button => {
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Feedback visual para formulários de candidatura
    document.querySelectorAll('form[action*="applications"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button && !button.disabled) {
                button.disabled = true;
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
                
                // Timeout de segurança
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = originalContent;
                }, 5000);
            }
        });
    });

    // Tratamento global de erros
    window.addEventListener('error', (event) => {
        console.error('Erro global:', event.error);
        showNotification('Ocorreu um erro inesperado. Recarregue a página se necessário.', 'error');
    });

    // Integração com Sidebar
    function initSidebarIntegration() {
        const sidebar = document.getElementById('sidebar');
        const mainLayout = document.querySelector('.sidebar-layout');
        
        if (!sidebar || !mainLayout) return;

        // Detectar quando a sidebar expande/contrai
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const isExpanded = sidebar.classList.contains('w-56');
                    
                    if (window.innerWidth >= 1024) {
                        if (isExpanded) {
                            mainLayout.classList.add('sidebar-expanded');
                        } else {
                            mainLayout.classList.remove('sidebar-expanded');
                        }
                    }
                }
            });
        });

        observer.observe(sidebar, { attributes: true });

        // Melhorar acessibilidade - navegação por teclado
        const sidebarLinks = sidebar.querySelectorAll('a, button');
        sidebarLinks.forEach(link => {
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    link.click();
                }
            });
        });

        // Responsividade
        function handleResize() {
            if (window.innerWidth < 1024) {
                mainLayout.classList.remove('sidebar-expanded');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Executar na inicialização
    }

    // Inicializar integração da sidebar quando autenticado
    @auth
        // Lazy loading para melhor performance
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebarIntegration);
        } else {
            initSidebarIntegration();
        }
        
        // Otimização de performance - debounce para resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(handleResize, 150);
        });
    @endauth
</script>
@endpush
 
<div class="space-y-6">
    {{-- Alerts de sessão --}}
    @if (session('ok'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>{{ session('ok') }}
        </div>
    @endif

    {{-- Sistema de Filtros --}}
    @php
        $filtersApplied = request()->hasAny(['categories', 'category', 'location_type', 'search', 'segment_id', 'rating_order']);
    @endphp
    <div class="filter-section p-4 mb-6" role="search" aria-label="Filtros de busca de vagas">
        <div class="flex items-center justify-between mb-6">
            <h2 class="trampix-h2 text-gray-900 flex items-center" id="filters-heading">
                <i class="fas fa-filter mr-3 text-purple-600"></i>Filtros de Busca
            </h2>
            <button type="button"
                    id="toggleFiltersBtn"
                    class="card-btn card-btn-outline text-sm"
                    aria-expanded="{{ $filtersApplied ? 'true' : 'false' }}"
                    aria-controls="filtersContent">
                <i class="fas {{ $filtersApplied ? 'fa-chevron-up' : 'fa-chevron-down' }} mr-2" id="filterIcon"></i>
                <span>{{ $filtersApplied ? 'Ocultar' : 'Mostrar' }}</span>
            </button>
        </div>
        <div id="filtersContent" class="transition-all duration-300 ease-in-out {{ $filtersApplied ? '' : 'hidden' }}" aria-live="polite">
            @php
                $categoriesList = $availableCategories ?? [];
                $locationsList  = $locationTypes ?? ['Remoto','Híbrido','Presencial'];
                $segmentsList   = $segments ?? \App\Models\Segment::orderBy('name')->select('id','name')->get();
            @endphp
            <x-filter-panel
                class="p-0 w-full"
                :showHeader="false"
                noContainer="true"
                :action="route('vagas.index')"
                method="GET"
                applyLabel="Aplicar Filtros"
                :resetHref="route('vagas.index')"
                :categories="$categoriesList"
                :segments="$segmentsList"
                :locationTypes="$locationsList"
                :selectedCategory="request('category')"
                :selectedSegmentId="request('segment_id')"
                :selectedLocationType="request('location_type')"
                searchName="search"
                :searchValue="request('search')"
            />
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
                    <article class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-transform duration-200 hover:-translate-y-0.5 job-card focus-within:ring-2 focus-within:ring-purple-500 focus-within:ring-offset-2" 
                             style="animation-delay: {{ $index * 0.1 }}s"
                             role="listitem"
                             aria-labelledby="job-title-{{ $vaga->id }}"
                             tabindex="0">
                        {{-- Header do Card --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 id="job-title-{{ $vaga->id }}" 
                                    class="text-lg font-semibold text-gray-900 mb-2 hover:text-purple-600 transition-colors duration-200">
                                    <a href="{{ route('vagas.show', $vaga->id) }}" 
                                       class="text-decoration-none focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 rounded"
                                       aria-describedby="job-company-{{ $vaga->id }} job-description-{{ $vaga->id }}">
                                        {{ Str::limit($vaga->title, 50) }}
                                    </a>
                                </h3>
                                <div id="job-company-{{ $vaga->id }}" 
                                     class="flex items-center text-sm text-gray-600 mb-2 flex-wrap gap-2">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-building mr-2 text-purple-500" aria-hidden="true"></i>
                                        <span>{{ $vaga->company->name ?? 'Empresa não informada' }}</span>
                                    </span>
                                    @php
                                        // Em algumas vagas legadas, $vaga->category é uma string (nome da categoria)
                                        // e há colisão de nome com a relação Eloquent "category".
                                        // Para evitar "Attempt to read property 'segment' on string",
                                        // usamos explicitamente o valor da relação se houver category_id.
                                        $categoryRel = $vaga->category_id ? $vaga->getRelationValue('category') : null;
                                        $jobSegmentName = $categoryRel?->segment?->name;
                                        $companySegmentName = $vaga->company?->segment?->name;
                                    @endphp
                                    @if($jobSegmentName)
                                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200" title="Segmento da vaga">
                                            <i class="fas fa-layer-group mr-1 text-gray-500" aria-hidden="true"></i>
                                            {{ $jobSegmentName }}
                                        </span>
                                    @endif
                                    @if($companySegmentName)
                                        <span class="inline-flex items-center text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200" title="Segmento da empresa">
                                            <i class="fas fa-industry mr-1 text-gray-500" aria-hidden="true"></i>
                                            {{ $companySegmentName }}
                                        </span>
                                    @endif
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

                        {{-- Badges de informação (destacadas) --}}
                        <div class="flex flex-wrap gap-3 mb-4" role="list" aria-label="Informações da vaga">
                            @php
                                // Evita acessar propriedade em string: prioriza relação carregada, senão usa string legada
                                $categoryRel = $vaga->category_id ? $vaga->getRelationValue('category') : null;
                                $categoryLabel = $categoryRel?->name ?? ($vaga->category ?? null);
                            @endphp
                            @if($categoryLabel)
                                <span class="job-badge badge-category text-sm font-semibold px-3 py-1.5 rounded-full border border-purple-200 bg-purple-50 text-purple-700 shadow-sm"
                                      role="listitem"
                                      aria-label="Categoria: {{ $categoryLabel }}">
                                    <i class="fas fa-tag mr-2" aria-hidden="true"></i>{{ $categoryLabel }}
                                </span>
                            @endif
                            {{-- Tipo de contrato removido: todos os contratos são freelance --}}
                            @if($vaga->location_type)
                                <span class="job-badge badge-location text-sm font-semibold px-3 py-1.5 rounded-full border border-blue-200 bg-blue-50 text-blue-700 shadow-sm"
                                      role="listitem"
                                      aria-label="Tipo de localização: {{ $vaga->location_type }}">
                                    <i class="fas fa-map-marker-alt mr-2" aria-hidden="true"></i>{{ $vaga->location_type }}
                                </span>
                            @endif
                        </div>

                        {{-- Descrição --}}
                        <p id="job-description-{{ $vaga->id }}" 
                           class="text-sm text-gray-700 mb-4 line-clamp-3"
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
                        <div class="flex flex-wrap gap-3 mt-4" role="group" aria-label="Ações disponíveis para esta vaga">
                            <a href="{{ route('vagas.show', $vaga->id) }}" 
                               class="card-btn card-btn-primary"
                               aria-label="Ver detalhes completos da vaga {{ $vaga->title }}">
                                <i class="fas fa-eye mr-2" aria-hidden="true"></i>Ver Detalhes
                            </a>

                            {{-- Ver Perfil da Empresa --}}
                            @if($vaga->company)
                                <button type="button" 
                                        class="card-btn card-btn-secondary company-profile-btn"
                                        data-company-id="{{ $vaga->company->id }}"
                                        data-company-name="{{ $vaga->company->name }}"
                                        aria-label="Ver perfil da empresa {{ $vaga->company->name }}">
                                    <i class="fas fa-building mr-2" aria-hidden="true"></i>Perfil
                                </button>
                            @endif

                            {{-- Candidatar-se --}}
                            @auth
                                @can('isFreelancer')
                                    @php
                                        $hasApplied = $vaga->applications()
                                            ->where('freelancer_id', auth()->user()->freelancer?->id)
                                            ->exists();
                                    @endphp
                                    @if (!$hasApplied)
                                        <a href="{{ route('vagas.show', $vaga->id) }}"
                                           class="card-btn card-btn-outline"
                                           aria-label="Ir para a vaga {{ $vaga->title }}">
                                            <i class="fas fa-arrow-right mr-2" aria-hidden="true"></i>Ir para a vaga
                                        </a>
                                    @else
                                        <button type="button" 
                                                class="card-btn card-btn-outline opacity-60 cursor-not-allowed" 
                                                aria-label="Você já se candidatou para esta vaga">
                                            <i class="fas fa-check mr-2" aria-hidden="true"></i>Candidatado
                                        </button>
                                    @endif
                                @endcan
                            @endauth

                            {{-- Ações da empresa --}}
                            @can('update', $vaga)
                                <a href="{{ route('vagas.edit', $vaga->id) }}" 
                                   class="card-btn card-btn-outline"
                                   aria-label="Editar vaga {{ $vaga->title }}">
                                    <i class="fas fa-edit mr-2" aria-hidden="true"></i>Editar
                                </a>
                            @endcan

                            @can('delete', $vaga)
                                <form action="{{ route('vagas.destroy', $vaga->id) }}" method="POST" class="contents" id="deleteForm-{{ $vaga->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            class="card-btn card-btn-danger" 
                                            aria-label="Excluir vaga {{ $vaga->title }}"
                                            onclick="showDeleteConfirmation({{ $vaga->id }}, '{{ $vaga->title }}', '{{ $vaga->company->name ?? 'Empresa' }}')">
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

    {{-- Busca suave (AJAX) e sugestões --}}
    <script>
    (function(){
        const filtersContent = document.getElementById('filtersContent');
        if (!filtersContent) return;
        const form = filtersContent.querySelector('form[data-filter-form="true"]');
        if (!form) return;
        const resultsSel = form.getAttribute('data-results-container') || '#resultsContainer';
        const loadingSel = form.getAttribute('data-loading') || '#loadingIndicator';
        const resultsEl = document.querySelector(resultsSel);
        const loadingEl = document.querySelector(loadingSel);
        let currentFetchController = null;
        let latestRequestId = 0;
        let submitTimer = null;
        const SUBMIT_DEBOUNCE_MS = 250;

        function showLoading(state){
            if (!loadingEl) return;
            loadingEl.classList[state ? 'remove' : 'add']('hidden');
        }

        async function fetchResults(url){
            const myRequestId = ++latestRequestId;
            showLoading(true);
            try {
                // Cancelar requisição anterior se existir (evita erros ao mudar vários filtros)
                if (currentFetchController) {
                    currentFetchController.abort();
                }
                currentFetchController = new AbortController();
                const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }, signal: currentFetchController.signal });
                if (!res.ok) {
                    // Falha silenciosa: manter resultados atuais sem erro visível
                    return;
                }
                const html = await res.text();
                // Parse e extrai o container de resultados da resposta inteira
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newResults = doc.querySelector(resultsSel);
                // Ignorar respostas antigas (se outra atualização já foi agendada/concluída)
                if (myRequestId !== latestRequestId) {
                    return;
                }
                if (newResults && resultsEl) {
                    resultsEl.innerHTML = newResults.innerHTML;
                } else {
                    // Falha silenciosa se não encontrar o container esperado
                    return;
                }
            } catch (e) {
                // Ignorar aborts e erros de rede de forma silenciosa
                if (e && e.name === 'AbortError') {
                    return;
                }
                // Mantém resultados atuais sem erro visível
                return;
            } finally {
                showLoading(false);
                currentFetchController = null;
            }
        }

        function scheduleSubmit(){
            if (submitTimer) {
                clearTimeout(submitTimer);
            }
            submitTimer = setTimeout(() => {
                const params = new URLSearchParams(new FormData(form));
                const url = `${form.action}?${params.toString()}`;
                window.history.replaceState({}, '', url);
                fetchResults(url);
            }, SUBMIT_DEBOUNCE_MS);
        }

        // Intercepta o envio do formulário para busca suave
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            scheduleSubmit();
        });

        // Auto-submeter selects ao alterar valores
        form.querySelectorAll('select').forEach(sel => {
            sel.addEventListener('change', () => {
                scheduleSubmit();
            });
        });

        // Lógica dinâmica: ao escolher segmento, buscar categorias relacionadas
        const segmentSelect = form.querySelector('[data-segment-select="true"]');
        const categorySelect = form.querySelector('[data-category-select="true"]');
        async function loadCategoriesBySegment(segId, preselectValue){
            if (!categorySelect) return;
            // Reset options (placeholder depende do estado)
            categorySelect.innerHTML = '<option value="">' + (segId ? 'Todas' : 'Selecione um segmento') + '</option>';
            if (!segId){
                categorySelect.setAttribute('disabled','disabled');
                categorySelect.classList.add('opacity-60','cursor-not-allowed');
                return;
            }
            try {
                const res = await fetch(`/api/segments/${encodeURIComponent(segId)}/categories`);
                const ct = res.headers.get('content-type') || '';
                if (!res.ok || !ct.includes('application/json')) throw new Error('Resposta inválida');
                const data = await res.json();
                const cats = (data.categories || []);
                cats.forEach(name => {
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    if (preselectValue && preselectValue === name) opt.selected = true;
                    categorySelect.appendChild(opt);
                });
                categorySelect.removeAttribute('disabled');
                categorySelect.classList.remove('opacity-60','cursor-not-allowed');
            } catch(e) {
                // Em erro, mantém desabilitado
                categorySelect.setAttribute('disabled','disabled');
                categorySelect.classList.add('opacity-60','cursor-not-allowed');
            }
        }
        if (segmentSelect && categorySelect){
            // Carregar na inicialização se já houver segmento selecionado
            const initialSeg = segmentSelect.value;
            const initialCat = categorySelect.getAttribute('data-initial') || '{{ addslashes(request('category')) }}';
            if (initialSeg){
                loadCategoriesBySegment(initialSeg, initialCat);
            }
            segmentSelect.addEventListener('change', (e) => {
                const segId = e.target.value;
                // Ao trocar segmento, limpa categoria e carrega novas opções
                if (categorySelect){ categorySelect.value = ''; }
                loadCategoriesBySegment(segId, null);
            });
        }

        // Sugestões no campo de busca com debounce
        const searchInput = form.querySelector('[data-search-input="true"]');
        const suggestTargetSel = searchInput ? searchInput.getAttribute('data-suggest-target') : null;
        const suggestBox = suggestTargetSel ? document.querySelector(suggestTargetSel) : null;
        let tId;
        function debounce(fn, ms){
            return function(){
                clearTimeout(tId);
                const args = arguments;
                tId = setTimeout(()=>fn.apply(null, args), ms);
            }
        }
        async function loadSuggestions(q){
            if (!suggestBox) return;
            if (!q || q.length < 2){ suggestBox.classList.add('hidden'); suggestBox.innerHTML=''; return; }
            try{
                const res = await fetch(`/api/vagas/suggest?search=${encodeURIComponent(q)}`);
                // Garantir JSON válido antes de parsear
                const ct = res.headers.get('content-type') || '';
                if (!res.ok || !ct.includes('application/json')) {
                    throw new Error(`Resposta inválida (${res.status})`);
                }
                const data = await res.json();
                const items = (data.suggestions || []).slice(0,8);
                if (!items.length){ suggestBox.classList.add('hidden'); suggestBox.innerHTML=''; return; }
                suggestBox.innerHTML = items.map(s => `<button type="button" class="block w-full text-left px-3 py-2 hover:bg-purple-50">${s}</button>`).join('');
                suggestBox.classList.remove('hidden');
                // Click em sugestão preenche e agenda submit suave
                suggestBox.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (searchInput){ searchInput.value = btn.textContent; }
                        suggestBox.classList.add('hidden');
                        scheduleSubmit();
                    });
                });
            }catch(e){ 
                // Falha silenciosa: esconder e limpar sugestões
                suggestBox.classList.add('hidden');
                suggestBox.innerHTML='';
            }
        }
        if (searchInput && suggestBox){
            const debounced = debounce(loadSuggestions, 250);
            searchInput.addEventListener('input', (e)=> debounced(e.target.value));
            // Fechar ao clicar fora
            document.addEventListener('click', (e)=>{
                if (!suggestBox.contains(e.target) && e.target !== searchInput){ suggestBox.classList.add('hidden'); }
            });
        }
    })();
    </script>
        </div> <!-- Fecha content -->
    </div> <!-- Fecha layout principal com sidebar -->
</div> <!-- Fecha min-h-screen -->
@endsection
