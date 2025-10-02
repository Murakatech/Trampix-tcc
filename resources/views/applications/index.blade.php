@extends('layouts.app')

@section('header')
    <h2 class="h4 mb-0">Minhas Candidaturas</h2>
@endsection

@section('content')
<div class="container">
    {{-- Mensagens de sucesso/erro --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alertas de status das candidaturas --}}
    @php
        $acceptedCount = $applications->where('status', 'accepted')->count();
        $rejectedCount = $applications->where('status', 'rejected')->count();
    @endphp

    @if($acceptedCount > 0)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-trophy me-2"></i>
            <strong>Parabéns!</strong> Você tem {{ $acceptedCount }} candidatura(s) aceita(s)! 
            <a href="#accepted-applications" class="alert-link">Ver detalhes abaixo</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($rejectedCount > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Você tem {{ $rejectedCount }} candidatura(s) que não foram selecionadas. 
            <strong>Continue tentando!</strong> Novas oportunidades aparecem todos os dias.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Lista de candidaturas --}}
    @if ($applications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-4"></i>
                <h3 class="h5 mb-3">Você não tem nenhuma aplicação</h3>
                <p class="text-muted mb-4">Comece a se candidatar às vagas disponíveis para aparecerem aqui.</p>
                <a href="{{ route('vagas.index') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>
                    Ver Vagas Disponíveis
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Vaga</th>
                                <th>Empresa</th>
                                <th>Status</th>
                                <th>Data da Candidatura</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $application->jobVacancy->title }}</div>
                                        <small class="text-muted">{{ $application->jobVacancy->category }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $application->jobVacancy->company->company_name }}</div>
                                        <small class="text-muted">{{ $application->jobVacancy->location_type }}</small>
                                    </td>
                                    <td>
                                        @if($application->status === 'accepted')
                            <span class="badge bg-success fs-6" id="accepted-applications">
                                <i class="fas fa-check-circle me-1"></i>Aceita
                            </span>
                        @elseif($application->status === 'rejected')
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle me-1"></i>Rejeitada
                            </span>
                        @else
                            <span class="badge bg-warning fs-6">
                                <i class="fas fa-clock me-1"></i>Pendente
                            </span>
                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $application->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('vagas.show', $application->jobVacancy) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Ver Vaga
                                            </a>
                                            
                                            @if($application->status === 'pending')
                                                <form action="{{ route('applications.cancel', $application) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Tem certeza que deseja cancelar esta candidatura?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Estatísticas --}}
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="card-title text-primary">{{ $applications->count() }}</h3>
                        <p class="card-text text-muted">Total de Candidaturas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="card-title text-success">{{ $applications->where('status', 'accepted')->count() }}</h3>
                        <p class="card-text text-muted">Aceitas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="card-title text-warning">{{ $applications->where('status', 'pending')->count() }}</h3>
                        <p class="card-text text-muted">Pendentes</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection