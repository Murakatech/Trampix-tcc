@extends('layouts.dashboard')

@section('header')
@php
    $activeRole = session('active_role') ?? (auth()->user()->isCompany() ? 'company' : (auth()->user()->isFreelancer() ? 'freelancer' : null));
@endphp
<div class="d-flex justify-content-center align-items-center">
    <h1 class="font-medium text-gray-900 text-center" style="font-size: 25px; color: #000;">Minhas Vagas</h1>
</div>
@endsection

@section('content')
<div class="container mt-4">
    <!-- Ação: Nova Vaga fora da navbar -->
    <div class="mb-3">
        <a href="{{ route('vagas.create') }}" class="btn-trampix-company btn-glow d-block w-100 text-center py-3">
            <i class="fas fa-plus me-2"></i>Nova Vaga
        </a>
    </div>
    {{-- Alerts de sessão --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Estatísticas --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-briefcase text-primary fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total de Vagas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['active'] }}</h3>
                    <p class="text-muted mb-0">Vagas Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle text-danger fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['closed'] }}</h3>
                    <p class="text-muted mb-0">Vagas Fechadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-users text-info fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['total_applications'] }}</h3>
                    <p class="text-muted mb-0">Total de Candidaturas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros (copiados da tela pública de Vagas) --}}
    @php
        $filtersApplied = request()->hasAny(['category', 'contract_type', 'location_type', 'search', 'rating_order']);
        $locationsList = ['Remoto','Híbrido','Presencial'];
        $categoriesList = $categories ?? [];
    @endphp
    @push('styles')
    <style>
        .filter-section { background:#fff; border:1px solid #e5e7eb; border-radius:8px; transition: border-color .2s ease, background-color .2s ease; }
        .filter-section:hover { box-shadow:none; border-color:#d1d5db; }
    </style>
    @endpush
    <div class="filter-section p-4 mb-4" role="search" aria-label="Filtros de busca de vagas da empresa">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="trampix-h2 text-gray-900 d-flex align-items-center" id="filters-heading">
                <i class="fas fa-filter me-2 {{ $activeRole === 'company' ? 'text-green-600' : 'text-purple-600' }}"></i>Filtros de Busca
            </h2>
            <button id="toggleFilters"
                    class="btn-trampix-secondary text-sm"
                    aria-expanded="{{ $filtersApplied ? 'true' : 'false' }}"
                    aria-controls="filtersContent"
                    aria-describedby="filters-heading">
                <i class="fas {{ $filtersApplied ? 'fa-chevron-up' : 'fa-chevron-down' }} me-2" id="filterIcon"></i>
                <span>{{ $filtersApplied ? 'Ocultar' : 'Mostrar' }}</span>
            </button>
        </div>

        <div id="filtersContent" class="{{ $filtersApplied ? '' : 'hidden' }}">
            <x-filter-panel
                class="p-0 w-100"
                :showHeader="false"
                noContainer="true"
                :action="route('company.vagas.index')"
                method="GET"
                applyLabel="Aplicar Filtros"
                :resetHref="route('company.vagas.index')"
                :categories="$categoriesList"
                :locationTypes="$locationsList"
                :selectedCategory="request('category')"
                :selectedLocationType="request('location_type')"
                searchName="search"
                :searchValue="request('search')"
            />
        </div>
    </div>

    {{-- Loading indicator para filtros --}}
    <div id="loadingIndicator" class="hidden">
        <div class="d-flex align-items-center justify-content-center py-3">
            <i class="fas fa-spinner fa-spin me-2 text-purple-600"></i>
            <span class="text-muted">Carregando vagas...</span>
        </div>
    </div>

    {{-- Lista de Vagas --}}
    <div id="resultsContainer">
    @if ($vagas->isEmpty())
        <div class="trampix-card text-center py-5">
            <div class="card-body">
                <i class="fas fa-briefcase text-muted" style="font-size: 4rem;"></i>
                <h3 class="trampix-h3 mt-3 mb-2">Nenhuma vaga encontrada</h3>
                @if(request()->hasAny(['category', 'status', 'contract_type', 'location_type']))
                    <p class="text-muted mb-3">Tente ajustar os filtros ou</p>
                    <a href="{{ route('company.vagas.index') }}" class="btn-trampix-secondary me-2">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </a>
                @else
                    <p class="text-muted mb-3">Comece criando sua primeira vaga de emprego</p>
                @endif
                <a href="{{ route('vagas.create') }}" class="btn-trampix-company">
                    <i class="fas fa-plus me-1"></i>Criar Nova Vaga
                </a>
            </div>
        </div>
    @else
        <div class="trampix-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="trampix-table-header">
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Modalidade</th>
                                <th>Status</th>
                                <th>Candidaturas</th>
                                <th>Criada em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vagas as $vaga)
                                <tr>
                                    <td>
                                        <a href="{{ route('company.vagas.show', $vaga->id) }}" class="text-decoration-none fw-bold">
                                            {{ $vaga->title }}
                                        </a>
                                        @if($vaga->description)
                                            <br><small class="text-muted">{{ Str::limit($vaga->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $vaga->category?->name ?? $vaga->category ?? 'Sem categoria' }}</span>
                                    </td>
                                    <td>
                                        @if($vaga->contract_type)
                                            <span class="badge bg-info">{{ $vaga->contract_type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->location_type)
                                            <span class="badge bg-success">{{ $vaga->location_type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Ativa
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Fechada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vaga->applications->count() }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $vaga->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('company.vagas.show', $vaga->id) }}" 
                                               class="btn-trampix-secondary btn-glow px-3 py-2 text-sm"
                                               title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @php $activeRole = session('active_role') ?? (auth()->user()->isCompany() ? 'company' : (auth()->user()->isFreelancer() ? 'freelancer' : null)); @endphp
                                            <a href="{{ route('vagas.edit', $vaga->id) }}" 
                                               class="{{ $activeRole === 'company' ? 'btn-trampix-company' : 'btn-trampix-primary' }} btn-glow px-3 py-2 text-sm"
                                               title="Editar vaga">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('company.vagas.toggle-status', $vaga->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn-trampix-warning btn-glow px-3 py-2 text-sm"
                                                        title="{{ $vaga->status === 'active' ? 'Encerrar vaga' : 'Reativar vaga' }}">
                                                    <i class="fas fa-{{ $vaga->status === 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('vagas.destroy', $vaga->id) }}" 
                                                  method="POST" class="d-inline"
                                                  id="deleteForm-{{ $vaga->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        class="btn-trampix-danger btn-glow px-3 py-2 text-sm" 
                                                        onclick="showDeleteConfirmation({{ $vaga->id }}, '{{ $vaga->title }}', '{{ $vaga->empresa->nome ?? 'Empresa' }}')"
                                                        title="Excluir vaga">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginação --}}
                @if ($vagas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $vagas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    </div>
</div>

{{-- Componente de Confirmação --}}
<x-action-confirmation 
    actionType="exclusao" 
    modalId="deleteConfirmationModal" />

@push('scripts')
<script>
    // Toggle suave dos filtros
    (function(){
        const filterToggle = document.getElementById('toggleFilters');
        const filterSection = document.getElementById('filtersContent');
        if (!filterToggle || !filterSection) return;
        filterToggle.addEventListener('click', function(){
            const isHidden = filterSection.classList.contains('hidden');
            const icon = this.querySelector('i');
            const toggleText = this.querySelector('span');
            if (isHidden){
                filterSection.classList.remove('hidden');
                filterSection.style.maxHeight = '0';
                filterSection.style.overflow = 'hidden';
                requestAnimationFrame(()=>{
                    filterSection.style.transition = 'max-height .3s ease-out';
                    filterSection.style.maxHeight = filterSection.scrollHeight + 'px';
                });
                this.setAttribute('aria-expanded', 'true');
                if (icon){ icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
                if (toggleText){ toggleText.textContent = 'Ocultar'; }
            } else {
                filterSection.style.maxHeight = '0';
                setTimeout(()=>{
                    filterSection.classList.add('hidden');
                    filterSection.style.maxHeight = '';
                    filterSection.style.overflow = '';
                    filterSection.style.transition = '';
                }, 300);
                this.setAttribute('aria-expanded', 'false');
                if (icon){ icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); }
                if (toggleText){ toggleText.textContent = 'Mostrar'; }
            }
        });
    })();

    // Busca suave (AJAX) baseada em data-filter-form
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

        function showLoading(state){ if (!loadingEl) return; loadingEl.classList[state ? 'remove' : 'add']('hidden'); }

        async function fetchResults(url){
            const myRequestId = ++latestRequestId;
            showLoading(true);
            try {
                if (currentFetchController) currentFetchController.abort();
                currentFetchController = new AbortController();
                const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }, signal: currentFetchController.signal });
                if (!res.ok) return;
                const html = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newResults = doc.querySelector(resultsSel);
                if (myRequestId !== latestRequestId) return;
                if (newResults && resultsEl) resultsEl.innerHTML = newResults.innerHTML;
            } catch(e){ if (e && e.name === 'AbortError') return; }
            finally { showLoading(false); currentFetchController = null; }
        }

        function scheduleSubmit(){
            if (submitTimer) clearTimeout(submitTimer);
            submitTimer = setTimeout(()=>{
                const params = new URLSearchParams(new FormData(form));
                const url = `${form.action}?${params.toString()}`;
                window.history.replaceState({}, '', url);
                fetchResults(url);
            }, SUBMIT_DEBOUNCE_MS);
        }

        form.addEventListener('submit', function(ev){ ev.preventDefault(); scheduleSubmit(); });
        form.querySelectorAll('select').forEach(sel=> sel.addEventListener('change', scheduleSubmit));

        const searchInput = form.querySelector('[data-search-input="true"]');
        const suggestTargetSel = searchInput ? searchInput.getAttribute('data-suggest-target') : null;
        const suggestBox = suggestTargetSel ? document.querySelector(suggestTargetSel) : null;
        let tId;
        function debounce(fn, ms){ return function(){ clearTimeout(tId); const args = arguments; tId = setTimeout(()=>fn.apply(null, args), ms); } }
        async function loadSuggestions(q){
            if (!suggestBox) return;
            if (!q || q.length < 2){ suggestBox.classList.add('hidden'); suggestBox.innerHTML=''; return; }
            try{
                const res = await fetch(`/api/vagas/suggest?search=${encodeURIComponent(q)}`);
                const ct = res.headers.get('content-type') || '';
                if (!res.ok || !ct.includes('application/json')) throw new Error(`Resposta inválida (${res.status})`);
                const data = await res.json();
                const items = (data.suggestions || []).slice(0,8);
                if (!items.length){ suggestBox.classList.add('hidden'); suggestBox.innerHTML=''; return; }
                suggestBox.innerHTML = items.map(s => `<button type="button" class="block w-100 text-start px-3 py-2 hover:bg-purple-50">${s}</button>`).join('');
                suggestBox.classList.remove('hidden');
                suggestBox.querySelectorAll('button').forEach(btn => btn.addEventListener('click', ()=>{ if (searchInput) searchInput.value = btn.textContent; suggestBox.classList.add('hidden'); scheduleSubmit(); }));
            }catch(e){ suggestBox.classList.add('hidden'); suggestBox.innerHTML=''; }
        }
        if (searchInput && suggestBox){ const debounced = debounce(loadSuggestions, 250); searchInput.addEventListener('input', (e)=> debounced(e.target.value)); document.addEventListener('click', (e)=>{ if (!suggestBox.contains(e.target) && e.target !== searchInput){ suggestBox.classList.add('hidden'); } }); }
    })();
    // Função para excluir vaga
    function showDeleteConfirmation(vagaId, jobTitle, companyName) {
        showActionModal('deleteConfirmationModal', {
            actionType: 'exclusao',
            message: `🗑️ Tem certeza que deseja excluir a vaga "${jobTitle}" da empresa ${companyName}?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos.`,
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

@endsection