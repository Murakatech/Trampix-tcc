@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Nova Vaga</h1>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <form action="{{ \Illuminate\Support\Facades\Gate::allows('isAdmin') ? route('admin.vagas.store') : route('vagas.store') }}" method="POST">
                @csrf
                
                @can('isAdmin')
                <div class="mb-3">
                    <label for="company_id" class="form-label">Empresa dona da vaga *</label>
                    <input type="text" id="companySearch" class="form-control mb-2" placeholder="Digite para pesquisar empresas...">
                    <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror" size="8">
                        <option value="">Selecione uma empresa...</option>
                        @foreach(($companies ?? []) as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Use o campo acima para filtrar pelo nome da empresa.</small>
                    @error('company_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @endcan

                <div class="mb-3">
                    <label for="title" class="form-label">Título *</label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição *</label>
                    <textarea id="description" name="description" rows="4" 
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="requirements" class="form-label">Requisitos</label>
                    <textarea id="requirements" name="requirements" rows="3" 
                              class="form-control @error('requirements') is-invalid @enderror">{{ old('requirements') }}</textarea>
                    @error('requirements')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="segment_id" class="form-label">Segmento</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="hidden" id="segment_id" name="segment_id" value="{{ old('segment_id') }}">
                                @php
                                    $selectedSegmentName = '';
                                    $oldSeg = old('segment_id');
                                    if(!empty($oldSeg) && !empty($segments)){
                                        foreach($segments as $seg){ if((string)$seg->id === (string)$oldSeg) { $selectedSegmentName = $seg->name; break; } }
                                    }
                                @endphp
                                <input type="text" id="segment_name" class="form-control" value="{{ $selectedSegmentName }}" placeholder="Nenhum segmento selecionado" readonly>
                                <button type="button" id="openSegmentModal" class="btn-trampix-primary btn-glow"><i class="fas fa-list me-1"></i>Escolher Segmento</button>
                            </div>
                            <small class="text-muted">Escolha um segmento para habilitar e listar as categorias relacionadas.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categoria da Vaga</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" disabled>
                                <option value="">Selecione uma categoria...</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat->id }}" data-segment-id="{{ $cat->segment_id ?? '' }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="contract_type" class="form-label">Tipo de contrato</label>
                            <select id="contract_type" name="contract_type" 
                                    class="form-select @error('contract_type') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                <option value="PJ" {{ old('contract_type') == 'PJ' ? 'selected' : '' }}>PJ</option>
                                <option value="CLT" {{ old('contract_type') == 'CLT' ? 'selected' : '' }}>CLT</option>
                                <option value="Estágio" {{ old('contract_type') == 'Estágio' ? 'selected' : '' }}>Estágio</option>
                                <option value="Freelance" {{ old('contract_type') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                            </select>
                            @error('contract_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location_type" class="form-label">Modalidade de trabalho</label>
                            <select id="location_type" name="location_type" 
                                    class="form-select @error('location_type') is-invalid @enderror">
                                <option value="">Selecione...</option>
                                <option value="Remoto" {{ old('location_type') == 'Remoto' ? 'selected' : '' }}>Remoto</option>
                                <option value="Híbrido" {{ old('location_type') == 'Híbrido' ? 'selected' : '' }}>Híbrido</option>
                                <option value="Presencial" {{ old('location_type') == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                            </select>
                            @error('location_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="salary_range" class="form-label">Faixa salarial</label>
                            <input type="text" id="salary_range" name="salary_range" 
                                   class="form-control @error('salary_range') is-invalid @enderror" 
                                   value="{{ old('salary_range') }}" placeholder="Ex: R$ 3.000 - R$ 5.000">
                            @error('salary_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-trampix-primary btn-glow">
                        <i class="fas fa-save me-1"></i>Salvar Vaga
                    </button>
                    <a href="{{ route('vagas.index') }}" class="btn-trampix-secondary btn-glow">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@can('isAdmin')
@push('styles')
<style>
    /* Ajuste para seletor alto com rolagem confortável */
    #company_id { max-height: 240px; overflow-y: auto; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Busca/filtro de empresas (apenas admin)
    const searchInput = document.getElementById('companySearch');
    const select = document.getElementById('company_id');
    if (!searchInput || !select) return;

    const options = Array.from(select.options);

    function filterOptions(term) {
        const lower = term.trim().toLowerCase();
        // Preservar primeira opção (placeholder)
        const placeholder = options[0];
        const filtered = options.slice(1).filter(opt => opt.text.toLowerCase().includes(lower));
        // Limpar e repopular
        select.innerHTML = '';
        select.appendChild(placeholder);
        filtered.forEach(opt => select.appendChild(opt));
    }

    searchInput.addEventListener('input', (e) => {
        filterOptions(e.target.value);
    });
});
</script>
@endpush
@endcan
@push('styles')
<style>
    /* Modal simples para seleção de segmento */
    #segmentModal { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 1050; }
    #segmentModal.show { display: flex; }
    #segmentModal .modal-card { background: #fff; border-radius: 8px; max-width: 640px; width: calc(100% - 2rem); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
    #segmentModal .modal-header { padding: 12px 16px; border-bottom: 1px solid #eee; display:flex; justify-content: space-between; align-items:center; }
    #segmentModal .modal-body { padding: 12px 16px; max-height: 380px; overflow-y: auto; }
    #segmentModal .segments-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }
    .segment-option { display:block; padding: 10px 12px; border:1px solid #ddd; border-radius:6px; cursor:pointer; text-decoration:none; color:#333; background:#fafafa; }
    .segment-option:hover { background:#f0f8ff; border-color:#bcd; }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro dependente Segmento -> Categoria (disponível para todos)
    const hiddenSegmentInput = document.getElementById('segment_id');
    const segmentNameInput = document.getElementById('segment_name');
    const categorySelect = document.getElementById('category_id');
    const openSegmentModalBtn = document.getElementById('openSegmentModal');

    // Dados de segmentos para obter nomes no front
    const segmentsData = @json(($segments ?? []));
    function getSegmentNameById(id){
        const s = segmentsData.find(x => String(x.id) === String(id));
        return s ? s.name : '';
    }

    if (hiddenSegmentInput && categorySelect) {
        const allOptions = Array.from(categorySelect.querySelectorAll('option'));

        function applyFilter() {
            const selectedSegmentId = hiddenSegmentInput.value;
            const placeholder = allOptions[0];
            const filtered = allOptions.slice(1).filter(opt => {
                const seg = opt.getAttribute('data-segment-id') || '';
                return selectedSegmentId && seg === selectedSegmentId;
            });

            // Preservar valor selecionado atual se ainda existir
            const currentValue = categorySelect.value;
            categorySelect.innerHTML = '';
            categorySelect.appendChild(placeholder.cloneNode(true));
            filtered.forEach(opt => categorySelect.appendChild(opt.cloneNode(true)));

            const hasCurrent = Array.from(categorySelect.options).some(o => o.value === currentValue);
            if (hasCurrent) categorySelect.value = currentValue;

            // Habilitar/Desabilitar categorias conforme segmento selecionado
            categorySelect.disabled = !selectedSegmentId;
        }

        // Auto-definir segmento com base na categoria atual (ex.: retorno com erros de validação)
        const selectedCategoryOption = categorySelect.querySelector('option:checked');
        if (selectedCategoryOption) {
            const segForCat = selectedCategoryOption.getAttribute('data-segment-id') || '';
            if (segForCat) {
                hiddenSegmentInput.value = segForCat;
                if (segmentNameInput) segmentNameInput.value = getSegmentNameById(segForCat);
            }
        }
        applyFilter();
    }

    // Modal simples para escolher segmento
    if (openSegmentModalBtn && hiddenSegmentInput) {
        openSegmentModalBtn.addEventListener('click', () => {
            const modal = document.getElementById('segmentModal');
            if (modal) modal.classList.add('show');
        });
    }
    document.addEventListener('click', (e) => {
        const modal = document.getElementById('segmentModal');
        if (!modal) return;
        if (e.target.matches('#closeSegmentModal') || e.target.matches('#segmentModal')) {
            modal.classList.remove('show');
        }
        if (e.target.matches('.segment-option')) {
            const id = e.target.getAttribute('data-id');
            const name = e.target.textContent.trim();
            hiddenSegmentInput.value = id;
            if (segmentNameInput) segmentNameInput.value = name;
            // Reaplicar filtro ao escolher segmento
            if (typeof applyFilter === 'function') applyFilter();
            modal.classList.remove('show');
        }
    });
});
</script>
@endpush
@push('scripts')
<script>
// Renderização do modal com opções de segmentos (geral)
document.addEventListener('DOMContentLoaded', function(){
    // Evitar dupla inserção do modal
    if (document.getElementById('segmentModal')) return;
    const container = document.createElement('div');
    container.id = 'segmentModal';
    container.innerHTML = `
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="segmentModalLabel">
        <div class="modal-header">
            <h5 id="segmentModalLabel" class="mb-0">Escolher Segmento</h5>
            <button type="button" id="closeSegmentModal" class="btn btn-sm btn-outline-secondary">Fechar</button>
        </div>
        <div class="modal-body">
            <div class="segments-grid">
                @foreach(($segments ?? []) as $seg)
                <button type="button" class="segment-option" data-id="{{ $seg->id }}">{{ $seg->name }}</button>
                @endforeach
            </div>
        </div>
    </div>`;
    document.body.appendChild(container);
});
</script>
@endpush
@endsection
