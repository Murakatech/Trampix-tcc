@extends('layouts.dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="trampix-h1">Status da Vaga</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @if($isCompanyOwner)
                    <li class="breadcrumb-item"><a href="{{ route('company.vagas.index') }}">Minhas Vagas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('company.vagas.show', $vaga->id) }}">{{ $vaga->title }}</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('applications.index') }}">Minhas Candidaturas</a></li>
                @endif
                <li class="breadcrumb-item active">Status</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2"></div>
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
        <div class="col-lg-8">
            <div class="trampix-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="trampix-h3 mb-0">Parceria ativa</h3>
                    <span class="badge bg-success">{{ $acceptedApplications->count() }} {{ $acceptedApplications->count() === 1 ? 'ativo' : 'ativos' }}</span>
                </div>
                <div class="card-body">
                    @if($acceptedApplications->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-handshake-slash" style="font-size: 2rem;"></i>
                            <div class="mt-2">Nenhuma parceria ativa no momento.</div>
                        </div>
                    @else
                        @foreach($acceptedApplications as $app)
                            <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-3">
                                <div class="d-flex align-items-center">
                                    @if($isCompanyOwner)
                                        <div class="avatar-circle freelancer me-3">{{ substr($app->freelancer->user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $app->freelancer->user->name }}</div>
                                            <small class="text-muted">{{ $app->freelancer->profession ?? 'Freelancer' }}</small>
                                            <div class="mt-1">
                                                <a href="{{ route('freelancers.show', $app->freelancer) }}" class="btn btn-sm btn-trampix-secondary" target="_blank">
                                                    <i class="fas fa-user me-1"></i>Ver perfil
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $companyDisplay = $vaga->company->display_name ?? $vaga->company->name ?? $vaga->company->user->name;
                                        @endphp
                                        <div class="avatar-circle company me-3">{{ substr($companyDisplay, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $companyDisplay }}</div>
                                            <small class="text-muted">Empresa</small>
                                            <div class="mt-1">
                                                <a href="{{ route('companies.show', $vaga->company) }}" class="btn btn-sm btn-trampix-secondary" target="_blank">
                                                    <i class="fas fa-building me-1"></i>Ver perfil
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    @if($isCompanyOwner)
                                        <form action="{{ route('applications.updateStatus', $app) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm btn-trampix-danger">
                                                <i class="fas fa-user-slash me-1"></i>Despedir
                                            </button>
                                        </form>
                                        <form action="{{ route('applications.updateStatus', $app) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="ended">
                                            <input type="hidden" name="finalize" value="1">
                                            <button type="submit" class="btn btn-sm btn-trampix-primary">
                                                <i class="fas fa-file-signature me-1"></i>Finalizar contrato
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('applications.resign', $app) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-trampix-danger">
                                                <i class="fas fa-door-open me-1"></i>Se demitir
                                            </button>
                                        </form>
                                        @php $companyDisplayBtn = $vaga->company->display_name ?? $vaga->company->name ?? $vaga->company->user->name; @endphp
                                        <a href="{{ route('applications.evaluate.create', $app) }}" class="btn btn-sm btn-company-primary disabled" aria-disabled="true">
                                            <i class="fas fa-star me-1"></i>Avaliar {{ $companyDisplayBtn }}
                                        </a>
                                    @endif
                                </div>
                                @if(!$isCompanyOwner)
                                    <div class="mt-2 text-muted small">Em andamento</div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="trampix-card mb-4">
                <div class="card-header">
                    <h3 class="trampix-h3 mb-0">Vaga</h3>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Título:</strong> {{ $vaga->title }}</div>
                    <div class="mb-2"><strong>Status:</strong> {{ $vaga->status === 'active' ? 'Ativa' : 'Fechada' }}</div>
                    <div class="mb-2"><strong>Candidaturas:</strong> {{ $vaga->applications->count() }}</div>
                    <a href="{{ route('company.vagas.show', $vaga->id) }}" class="btn-trampix-secondary w-100 mt-2"><i class="fas fa-arrow-left me-2"></i>Voltar para a vaga</a>
                </div>
            </div>
            {{-- Contrato finalizado: habilitar avaliações --}}
            @if($endedApplications->count() > 0)
            <div class="trampix-card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="trampix-h3 mb-0">Contrato finalizado</h3>
                    <span class="badge bg-secondary">{{ $endedApplications->count() }}</span>
                </div>
                <div class="card-body">
                    @foreach($endedApplications as $app)
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-3">
                            <div class="d-flex align-items-center">
                                @if($isCompanyOwner)
                                    <div class="avatar-circle freelancer me-3">{{ substr($app->freelancer->user->name, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold">{{ $app->freelancer->user->name }}</div>
                                        <small class="text-muted">{{ $app->freelancer->profession ?? 'Freelancer' }}</small>
                                    </div>
                                @else
                                    @php
                                        $companyDisplay = $vaga->company->display_name ?? $vaga->company->name ?? $vaga->company->user->name;
                                    @endphp
                                    <div class="avatar-circle company me-3">{{ substr($companyDisplay, 0, 1) }}</div>
                                    <div>
                                        <div class="fw-bold">{{ $companyDisplay }}</div>
                                        <small class="text-muted">Empresa</small>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @if($isCompanyOwner)
                                    @if(empty($app->evaluated_by_company_at))
                                        <a href="{{ route('applications.evaluate.create', $app) }}" class="btn btn-sm btn-company-primary btn-glow">
                                            <i class="fas fa-star me-1"></i>Avaliar {{ $app->freelancer->user->name }}
                                        </a>
                                        <span class="badge bg-warning text-dark">Avaliação pendente</span>
                                    @else
                                        <span class="badge bg-success">Avaliação enviada</span>
                                        @php $myAvg = $app->company_rating_avg ?? $app->company_rating; @endphp
                                        @if(!empty($myAvg))
                                            <span class="badge bg-primary">{{ number_format((float)$myAvg, 1) }}/5</span>
                                            <a href="{{ route('applications.evaluate.show', $app) }}" class="btn btn-sm btn-outline-secondary">Avaliação Completa</a>
                                        @endif
                                    @endif
                                @else
                                    @php $companyDisplayBtn = $vaga->company->display_name ?? $vaga->company->name ?? $vaga->company->user->name; @endphp
                                    @if(empty($app->evaluated_by_freelancer_at))
                                        <a href="{{ route('applications.evaluate.create', $app) }}" class="btn btn-sm btn-company-primary btn-glow">
                                            <i class="fas fa-star me-1"></i>Avaliar {{ $companyDisplayBtn }}
                                        </a>
                                        <span class="badge bg-warning text-dark">Avaliação pendente</span>
                                    @else
                                        <span class="badge bg-success">Avaliação enviada</span>
                                        @php $myAvgF = $app->freelancer_rating_avg ?? $app->freelancer_rating; @endphp
                                        @if(!empty($myAvgF))
                                            <span class="badge bg-warning text-dark">{{ number_format((float)$myAvgF, 1) }}/5</span>
                                            <a href="{{ route('applications.evaluate.show', $app) }}" class="btn btn-sm btn-outline-secondary">Avaliação Completa</a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 36px; height: 36px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 600; color: #fff;
    }
    .avatar-circle.freelancer { background: var(--trampix-freelancer-primary, #6f42c1); }
    .avatar-circle.company { background: var(--trampix-company-primary, #0d6efd); }
    .btn-glow { position: relative; }
    .btn-glow::after { content:''; position:absolute; inset:-2px; border-radius:8px; box-shadow:0 0 0.75rem rgba(13,110,253,.25); opacity:.6; }
    @media (max-width: 992px) {
        .avatar-circle { width: 32px; height: 32px; }
    }
}
</style>
@endpush