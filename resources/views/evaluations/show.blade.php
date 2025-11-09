@extends('layouts.dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="trampix-h1">Avaliação Completa</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('vagas.status', $application->job_vacancy_id) }}">Status da Vaga</a></li>
                <li class="breadcrumb-item active">Avaliação</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2"></div>
 </div>
@endsection

@section('content')
<div class="container mt-4">
    @php
        $targetName = $isCompanyOwner
            ? ($application->freelancer->user->name)
            : ($application->jobVacancy->company->display_name ?? $application->jobVacancy->company->name ?? $application->jobVacancy->company->user->name);
        $targetType = $isCompanyOwner ? 'Freelancer' : 'Empresa';

        $ratings = $isCompanyOwner
            ? (is_array($application->company_ratings_json) ? $application->company_ratings_json : (json_decode($application->company_ratings_json ?? '[]', true) ?: []))
            : (is_array($application->freelancer_ratings_json) ? $application->freelancer_ratings_json : (json_decode($application->freelancer_ratings_json ?? '[]', true) ?: []));

        $avg = $isCompanyOwner ? ($application->company_rating_avg ?? $application->company_rating) : ($application->freelancer_rating_avg ?? $application->freelancer_rating);
        $comment = $isCompanyOwner ? $application->company_comment : $application->freelancer_comment;

        $questions = [
            'Qualidade do trabalho entregue',
            'Comunicação durante o projeto',
            'Cumprimento de prazos',
            'Profissionalismo',
            'Flexibilidade para ajustes',
            'Colaboração em equipe',
            'Clareza nas expectativas',
            'Resolução de problemas',
            'Organização',
            'Satisfação geral',
        ];
    @endphp

    <div class="trampix-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="trampix-h3 mb-0"><i class="fas fa-star me-2"></i>Avaliação de {{ $targetType }}: {{ $targetName }}</h3>
            <div>
                <span class="badge bg-primary">Média: {{ number_format((float)$avg, 1) }}</span>
            </div>
        </div>
        <div class="card-body">
            @if(empty($ratings))
                <div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Nenhuma avaliação registrada ainda.</div>
            @else
                <div class="row">
                    @foreach($questions as $idx => $q)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="fw-semibold">{{ $q }}</div>
                                <div>
                                    @php $r = $ratings[$idx] ?? null; @endphp
                                    @if($r)
                                        <span class="badge bg-light text-dark"><i class="fas fa-star text-warning me-1"></i>{{ $r }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-3">
                <label class="form-label">Comentário</label>
                <div class="form-control" style="min-height: 100px">{{ $comment ?: 'Sem comentário.' }}</div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('vagas.status', $application->job_vacancy_id) }}" class="btn-trampix-secondary"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                <a href="{{ route('applications.evaluate.create', $application) }}" class="btn-trampix-primary"><i class="fas fa-edit me-2"></i>Editar Avaliação</a>
            </div>
        </div>
    </div>
</div>
@endsection