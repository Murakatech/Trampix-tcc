@extends('layouts.app')

@section('header')
<div class="bg-white shadow">
    <div class="container py-4">
        <h1 class="h2 mb-0">Editar Vaga</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vagas.update', $vaga) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título *</label>
                    <input type="text" id="title" name="title" 
                           class="form-control @error('title') is-invalid @enderror" 
                           value="{{ old('title', $vaga->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descrição *</label>
                    <textarea id="description" name="description" rows="4" 
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $vaga->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="requirements" class="form-label">Requisitos</label>
                    <textarea id="requirements" name="requirements" rows="3" 
                              class="form-control @error('requirements') is-invalid @enderror">{{ old('requirements', $vaga->requirements) }}</textarea>
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
                                   value="{{ old('category', $vaga->category) }}" placeholder="Ex: Desenvolvimento, Design, Marketing">
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
                                @foreach(['PJ','CLT','Estágio','Freelance'] as $tipo)
                                    <option value="{{ $tipo }}" {{ old('contract_type', $vaga->contract_type) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
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
                                @foreach(['Remoto','Híbrido','Presencial'] as $loc)
                                    <option value="{{ $loc }}" {{ old('location_type', $vaga->location_type) == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
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
                                   value="{{ old('salary_range', $vaga->salary_range) }}" placeholder="Ex: R$ 3.000 - R$ 5.000">
                            @error('salary_range')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="status" class="form-label">Status da vaga</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status', $vaga->status) == 'active' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle text-success"></i> Ativa
                        </option>
                        <option value="closed" {{ old('status', $vaga->status) == 'closed' ? 'selected' : '' }}>
                            <i class="fas fa-times-circle text-danger"></i> Encerrada
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Atualizar Vaga
                    </button>
                    <a href="{{ route('vagas.show', $vaga) }}" class="btn btn-secondary">
                        <i class="fas fa-eye me-1"></i>Visualizar
                    </a>
                    <a href="{{ route('vagas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
