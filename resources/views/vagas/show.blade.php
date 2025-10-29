@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">{{ $vaga->title }}</h1>
@endsection

@section('content')
<div class="container mt-4">
    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Erros de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Empresa:</strong> {{ $vaga->company->name ?? '-' }}</p>
                    <p><strong>Categoria:</strong> <span class="badge bg-secondary">{{ $vaga->category ?? '-' }}</span></p>
                    <p><strong>Tipo de contrato:</strong> <span class="badge bg-info">{{ $vaga->contract_type ?? '-' }}</span></p>
                    <p><strong>Local:</strong> <span class="badge bg-success">{{ $vaga->location_type ?? '-' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Faixa salarial:</strong> {{ $vaga->salary_range ?? '-' }}</p>
                    <p><strong>Status:</strong> <span class="badge bg-primary">{{ $vaga->status ?? 'active' }}</span></p>
                </div>
            </div>
            
            <hr>
            
            <div class="mb-3">
                <h5>Descrição</h5>
                <p>{{ $vaga->description ?? '-' }}</p>
            </div>
            
            <div class="mb-3">
                <h5>Requisitos</h5>
                <p>{{ $vaga->requirements ?? '-' }}</p>
            </div>

            {{-- Aplicar (freelancer logado) --}}
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

                    <hr>
                    
                    @if ($alreadyApplied)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Você já se candidatou a esta vaga.
                        </div>
                    @else
                        <form method="POST" action="{{ route('applications.store', $vaga->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Mensagem opcional ao recrutador</label>
                                <textarea
                                    id="cover_letter"
                                    name="cover_letter"
                                    rows="3"
                                    class="form-control"
                                    placeholder="Conte um pouco sobre você e por que se interessa por esta vaga..."
                                >{{ old('cover_letter') }}</textarea>
                            </div>

                            <button type="submit" class="btn-trampix-primary btn-glow">
                                <i class="fas fa-paper-plane me-2"></i>Candidatar-se
                            </button>
                        </form>
                    @endif
                @endcan
            @endauth

            {{-- Ações da empresa dona --}}
            @can('isCompany')
                @if(($vaga->company?->user_id) === auth()->id())
                    <hr>
                    <div class="d-flex gap-2">
                        <a href="{{ route('applications.byVacancy', $vaga->id) }}" class="btn-trampix-secondary btn-glow">
                            <i class="fas fa-users me-1"></i> Ver Candidatos
                        </a>
                        <a href="{{ route('vagas.edit', $vaga) }}" class="btn-trampix-secondary btn-glow">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <form action="{{ route('vagas.destroy', $vaga) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-trampix-danger btn-glow">
                                <i class="fas fa-trash me-1"></i> Excluir
                            </button>
                        </form>
                    </div>
                @endif
            @endcan
        </div>
    </div>
</div>
@endsection
