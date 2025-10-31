@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    @auth
        <!-- Sidebar integrada -->
        <x-sidebar />
    @endauth

    <!-- Layout principal com sidebar -->
    <div class="sidebar-layout {{ auth()->check() ? 'ml-20' : '' }}">
        <!-- Navbar integrada -->
        @include('layouts.navigation')
        
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
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-trampix-secondary">Dashboard</a>
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
    :showTip="true" />

<x-action-confirmation 
    actionType="exclusao" 
    modalId="deleteConfirmationModal" />

@push('scripts')
<script>
    // Função para mostrar confirmação de candidatura
    function showApplicationConfirmation(vagaId, jobTitle, companyName) {
        showActionModal('applicationConfirmationModal', {
            actionType: 'candidatura',
            jobTitle: jobTitle,
            companyName: companyName,
            message: `Deseja se candidatar para a vaga "${jobTitle}" na empresa ${companyName}?`,
            onConfirm: () => {
                const form = document.getElementById(`applicationForm-${vagaId}`);
                const submitButton = form.querySelector('button');
                
                // Adicionar loading ao botão
                if (submitButton) {
                    const originalContent = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
                    submitButton.disabled = true;
                }
                
                showNotification('Enviando candidatura...', 'info');
                form.submit();
            },
            onCancel: () => {
                showNotification('Candidatura cancelada.', 'info');
            }
        });
    }

    // Função para mostrar confirmação de exclusão
    function showDeleteConfirmation(vagaId, jobTitle, companyName) {
        showActionModal('deleteConfirmationModal', {
            actionType: 'exclusao',
            jobTitle: jobTitle,
            companyName: companyName,
            message: `⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir a vaga "${jobTitle}"?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos permanentemente.`,
            onConfirm: () => {
                const form = document.getElementById(`deleteForm-${vagaId}`);
                showNotification('Excluindo vaga...', 'warning');
                form.submit();
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

    /* Melhorias no layout de filtros */
    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .filter-section:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
        transition: all 0.2s ease;
        border-radius: 8px;
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

    /* Modo escuro */
    @media (prefers-color-scheme: dark) {
        .trampix-card {
            background-color: #1f2937;
            border-color: #374151;
        }
        
        .filter-section {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border-color: #374151;
        }
        
        .text-gray-900 {
            color: #f9fafb;
        }
        
        .text-gray-600 {
            color: #d1d5db;
        }
        
        .card-btn-secondary {
            background: #374151;
            color: #f9fafb;
            border-color: #4b5563;
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
    const filterToggle = document.getElementById('toggleFilters');
    const filterSection = document.getElementById('filtersContent');
    const loadingIndicator = document.getElementById('loadingIndicator');

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

    // Auto-submit em mudanças nos selects com tratamento de erro
    document.querySelectorAll('#categories, #contract_type, #location_type').forEach(select => {
        if (select) {
            select.addEventListener('change', function() {
                try {
                    // Adicionar feedback visual
                    this.style.opacity = '0.6';
                    this.disabled = true;
                    
                    const form = this.closest('form');
                    if (form) {
                        form.classList.add('filter-loading');
                    }
                    
                    showLoading();
                    setTimeout(() => {
                        filterForm.submit();
                    }, 300);
                } catch (error) {
                    console.error('Erro ao aplicar filtro:', error);
                    showNotification('Erro ao aplicar filtro. Tente novamente.', 'error');
                    hideLoading();
                    
                    // Restaurar estado do select
                    this.style.opacity = '1';
                    this.disabled = false;
                }
            });
        }
    });

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
                        filterForm.submit();
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
});
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
    <div class="filter-section p-6 mb-6" role="search" aria-label="Filtros de busca de vagas">
        <div class="flex items-center justify-between mb-6">
            <h2 class="trampix-h2 text-gray-900 flex items-center" id="filters-heading">
                <i class="fas fa-filter mr-3 text-purple-600"></i>Filtros de Busca
            </h2>
            <button id="toggleFilters" 
                    class="card-btn card-btn-outline text-sm"
                    aria-expanded="true"
                    aria-controls="filtersContent"
                    aria-describedby="filters-heading">
                <i class="fas fa-chevron-down mr-2" id="filterIcon"></i>
                <span>Ocultar</span>
            </button>
        </div>
        
        <div id="filtersContent" class="transition-all duration-300 ease-in-out" aria-live="polite">
            <form method="GET" action="{{ route('vagas.index') }}" id="filterForm" role="form" aria-label="Formulário de filtros">
                <div class="filter-grid">
                    {{-- Busca por texto --}}
                    <div class="filter-input-group">
                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-3">
                            Buscar vagas
                        </label>
                        <input type="text" 
                               id="search" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Digite o título da vaga, empresa ou palavra-chave..."
                               class="trampix-input w-full"
                               aria-describedby="search-help"
                               autocomplete="off">
                        <i class="fas fa-search input-icon"></i>
                        <div id="search-help" class="text-xs text-gray-500 mt-1">
                            Busque por título, descrição ou requisitos da vaga
                        </div>
                    </div>

                    {{-- Categorias --}}
                    <div>
                        <label for="categories" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-tags mr-2 text-purple-600"></i>Categorias
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
                        <label for="contract_type" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>Contrato
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
                        <label for="location_type" class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>Localização
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
                <div class="flex flex-col sm:flex-row gap-3 pt-6 mt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="card-btn card-btn-primary flex-1 sm:flex-none"
                            aria-describedby="apply-filters-help">
                        <i class="fas fa-search mr-2"></i>Aplicar Filtros
                    </button>
                    <a href="{{ route('vagas.index') }}" 
                       class="card-btn card-btn-secondary flex-1 sm:flex-none text-center"
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
                                <span class="job-badge badge-category"
                                      role="listitem"
                                      aria-label="Categoria: {{ $vaga->category }}">
                                    <i class="fas fa-tag mr-1" aria-hidden="true"></i>{{ $vaga->category }}
                                </span>
                            @endif
                            @if($vaga->contract_type)
                                <span class="job-badge badge-contract"
                                      role="listitem"
                                      aria-label="Tipo de contrato: {{ $vaga->contract_type }}">
                                    <i class="fas fa-briefcase mr-1" aria-hidden="true"></i>{{ $vaga->contract_type }}
                                </span>
                            @endif
                            @if($vaga->location_type)
                                <span class="job-badge badge-location"
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
                        @php
                            $isFreelancer = auth()->check() && auth()->user()->can('isFreelancer');
                            $canUpdate = auth()->check() && auth()->user()->can('update', $vaga);
                            $canDelete = auth()->check() && auth()->user()->can('delete', $vaga);
                            
                            $buttonCount = 2; // Ver Detalhes + Ver Empresa
                            if ($isFreelancer && !$hasApplied) $buttonCount++;
                            if ($canUpdate) $buttonCount++;
                            if ($canDelete) $buttonCount++;
                            
                            $gridClass = 'card-actions';
                            if ($buttonCount === 3) $gridClass .= ' has-three-buttons';
                            if ($buttonCount >= 4) $gridClass .= ' has-four-buttons';
                        @endphp
                        
                        <div class="{{ $gridClass }}" 
                             role="group" 
                             aria-label="Ações disponíveis para esta vaga">
                            {{-- Ver Detalhes --}}
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
                                    @if (!$hasApplied)
                                        <form action="{{ route('applications.store', $vaga->id) }}" method="POST" class="contents" id="applicationForm-{{ $vaga->id }}">
                                            @csrf
                                            <button type="button" 
                                                    class="card-btn card-btn-outline"
                                                    aria-label="Candidatar-se para a vaga {{ $vaga->title }}"
                                                    onclick="showApplicationConfirmation({{ $vaga->id }}, '{{ $vaga->title }}', '{{ $vaga->empresa->nome ?? 'Empresa' }}')">
                                                <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i>Candidatar
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" 
                                                class="card-btn card-btn-outline opacity-60 cursor-not-allowed" 
                                                disabled
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
                                            onclick="showDeleteConfirmation({{ $vaga->id }}, '{{ $vaga->title }}', '{{ $vaga->empresa->nome ?? 'Empresa' }}')">
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
        </div> <!-- Fecha content -->
    </div> <!-- Fecha layout principal com sidebar -->
</div> <!-- Fecha min-h-screen -->
@endsection
