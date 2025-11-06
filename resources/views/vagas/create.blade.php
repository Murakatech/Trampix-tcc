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
                            <label for="category_id" class="form-label">Categoria da Vaga</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">Selecione uma categoria...</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
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
@endsection
