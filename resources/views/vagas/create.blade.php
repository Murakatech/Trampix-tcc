@extends('layouts.app')

@section('header')
<div class="bg-white shadow">
    <div class="container py-4">
        <h1 class="h2 mb-0">Nova Vaga</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vagas.store') }}" method="POST">
                @csrf
                
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
                            <label for="category" class="form-label">Categoria</label>
                            <input type="text" id="category" name="category" 
                                   class="form-control @error('category') is-invalid @enderror" 
                                   value="{{ old('category') }}" placeholder="Ex: Desenvolvimento, Design, Marketing">
                            @error('category')
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
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar Vaga
                    </button>
                    <a href="{{ route('vagas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
