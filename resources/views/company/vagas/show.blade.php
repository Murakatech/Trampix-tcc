@extends('layouts.dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="trampix-h1">{{ $vaga->title }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('company.vagas.index') }}">Minhas Vagas</a></li>
                <li class="breadcrumb-item active">{{ $vaga->title }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('vagas.edit', $vaga->id) }}" class="btn-trampix-primary">
            <i class="fas fa-edit me-2"></i>Editar Vaga
        </a>
        <form action="{{ route('company.vagas.toggle-status', $vaga->id) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-trampix-{{ $vaga->status === 'active' ? 'warning' : 'success' }}">
                <i class="fas fa-{{ $vaga->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                {{ $vaga->status === 'active' ? 'Fechar Vaga' : 'Reativar Vaga' }}
            </button>
        </form>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
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

    <div class="row">
        {{-- Informações da Vaga --}}
        <div class="col-lg-8">
            <div class="trampix-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="trampix-h3 mb-0">Detalhes da Vaga</h3>
                    @if($vaga->status === 'active')
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle me-1"></i>Ativa
                        </span>
                    @else
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-times-circle me-1"></i>Fechada
                        </span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Categoria:</strong>
                            @if($vaga->category)
                                <span class="badge bg-secondary ms-2">{{ $vaga->category }}</span>
                            @else
                                <span class="text-muted ms-2">Não informado</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Tipo de Contrato:</strong>
                            @if($vaga->contract_type)
                                <span class="badge bg-info ms-2">{{ $vaga->contract_type }}</span>
                            @else
                                <span class="text-muted ms-2">Não informado</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Modalidade:</strong>
                            @if($vaga->location_type)
                                <span class="badge bg-success ms-2">{{ $vaga->location_type }}</span>
                            @else
                                <span class="text-muted ms-2">Não informado</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Localização:</strong>
                            <span class="ms-2">{{ $vaga->location ?? 'Não informado' }}</span>
                        </div>
                    </div>

                    @if($vaga->salary_min || $vaga->salary_max)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Faixa Salarial:</strong>
                                <span class="ms-2">
                                    @if($vaga->salary_min && $vaga->salary_max)
                                        R$ {{ number_format($vaga->salary_min, 2, ',', '.') }} - R$ {{ number_format($vaga->salary_max, 2, ',', '.') }}
                                    @elseif($vaga->salary_min)
                                        A partir de R$ {{ number_format($vaga->salary_min, 2, ',', '.') }}
                                    @else
                                        Até R$ {{ number_format($vaga->salary_max, 2, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    @if($vaga->description)
                        <div class="mb-3">
                            <strong>Descrição:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($vaga->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($vaga->requirements)
                        <div class="mb-3">
                            <strong>Requisitos:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($vaga->requirements)) !!}
                            </div>
                        </div>
                    @endif

                    @if($vaga->benefits)
                        <div class="mb-3">
                            <strong>Benefícios:</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {!! nl2br(e($vaga->benefits)) !!}
                            </div>
                        </div>
                    @endif

                    <div class="row text-muted">
                        <div class="col-md-6">
                            <small><strong>Criada em:</strong> {{ $vaga->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-6">
                            <small><strong>Última atualização:</strong> {{ $vaga->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatísticas das Candidaturas --}}
        <div class="col-lg-4">
            <div class="trampix-card mb-4">
                <div class="card-header">
                    <h3 class="trampix-h3 mb-0">Estatísticas de Candidaturas</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-users text-primary" style="font-size: 3rem;"></i>
                        <h2 class="trampix-h2 mt-2 mb-0">{{ $vagaStats['total_applications'] }}</h2>
                        <p class="text-muted">Total de Candidaturas</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-warning mb-1">{{ $vagaStats['pending_applications'] }}</h4>
                                <small class="text-muted">Pendentes</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success mb-1">{{ $vagaStats['accepted_applications'] }}</h4>
                                <small class="text-muted">Aceitas</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-danger mb-1">{{ $vagaStats['rejected_applications'] }}</h4>
                            <small class="text-muted">Rejeitadas</small>
                        </div>
                    </div>

                    @if($vagaStats['total_applications'] > 0)
                        <div class="mt-3">
                            <a href="{{ route('applications.byVacancy', $vaga->id) }}" class="btn-trampix-primary w-100">
                                <i class="fas fa-list me-2"></i>Ver Todas as Candidaturas
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Ações Rápidas --}}
            <div class="trampix-card">
                <div class="card-header">
                    <h3 class="trampix-h3 mb-0">Ações Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('vagas.edit', $vaga->id) }}" class="btn-trampix-primary">
                            <i class="fas fa-edit me-2"></i>Editar Vaga
                        </a>
                        
                        @if($vagaStats['total_applications'] > 0)
                            <a href="{{ route('applications.byVacancy', $vaga->id) }}" class="btn-trampix-secondary">
                                <i class="fas fa-users me-2"></i>Gerenciar Candidaturas
                            </a>
                        @endif

                        <form action="{{ route('company.vagas.toggle-status', $vaga->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-trampix-{{ $vaga->status === 'active' ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-{{ $vaga->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                                {{ $vaga->status === 'active' ? 'Fechar Vaga' : 'Reativar Vaga' }}
                            </button>
                        </form>

                        <hr>

                        <form action="{{ route('vagas.destroy', $vaga->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn-trampix-danger w-100" 
                                    onclick="return confirm('Tem certeza que deseja excluir esta vaga? Esta ação não pode ser desfeita e todas as candidaturas serão perdidas.')">
                                <i class="fas fa-trash me-2"></i>Excluir Vaga
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Candidaturas Recentes --}}
    @if($vaga->applications->count() > 0)
        <div class="trampix-card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="trampix-h3 mb-0">Candidaturas Recentes</h3>
                <a href="{{ route('applications.byVacancy', $vaga->id) }}" class="btn-trampix-secondary btn-sm">
                    Ver Todas
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Freelancer</th>
                                <th>Data da Candidatura</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vaga->applications->take(5) as $application)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($application->freelancer->user->profile_photo)
                                                <img src="{{ asset('storage/' . $application->freelancer->user->profile_photo) }}" 
                                                     alt="Foto" class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $application->freelancer->user->name }}</strong>
                                                @if($application->freelancer->profession)
                                                    <br><small class="text-muted">{{ $application->freelancer->profession }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $application->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($application->status === 'pending')
                                            <span class="badge bg-warning">Pendente</span>
                                        @elseif($application->status === 'accepted')
                                            <span class="badge bg-success">Aceita</span>
                                        @else
                                            <span class="badge bg-danger">Rejeitada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('freelancers.show', $application->freelancer->id) }}" 
                                           class="btn-trampix-secondary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver Perfil
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection