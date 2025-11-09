@extends('layouts.dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="trampix-h1">Avaliação pós-contrato</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('vagas.status', $application->job_vacancy_id) }}">Status da Vaga</a></li>
                <li class="breadcrumb-item active">Avaliar</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2"></div>
 </div>
@endsection

@section('content')
<div class="container mt-4">
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $targetName = $isCompanyOwner
            ? ($application->freelancer->user->name)
            : ($application->jobVacancy->company->display_name ?? $application->jobVacancy->company->name ?? $application->jobVacancy->company->user->name);
        $targetType = $isCompanyOwner ? 'Freelancer' : 'Empresa';
    @endphp

    <div class="trampix-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h3 class="trampix-h3 mb-0"><i class="fas fa-star me-2"></i>Avaliar {{ $targetType }}: {{ $targetName }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('applications.evaluate.store', $application) }}" method="POST">
                @csrf

                <p class="text-muted mb-3">Responda de 5 a 10 perguntas com notas de 1 a 5. A média será a nota final exibida no perfil futuramente.</p>

                @php
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

                <div class="row">
                    @foreach($questions as $idx => $q)
                        @if($idx < 10)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">{{ $q }}</label>
                            <div class="d-flex align-items-center gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="btn btn-sm btn-outline-secondary">
                                        <input type="radio" name="ratings[{{ $idx }}]" value="{{ $i }}" class="visually-hidden" required>
                                        <i class="fas fa-star{{ $i <= 3 ? '' : ' text-warning' }}"></i> {{ $i }}
                                    </label>
                                @endfor
                            </div>
                            @error('ratings.'.$idx)
                                <div class="text-danger small">Selecione uma nota.</div>
                            @enderror
                        </div>
                        @endif
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label">Comentário (opcional)</label>
                    <textarea name="comments" class="form-control" rows="3" placeholder="Escreva um breve feedback"></textarea>
                    @error('comments')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('vagas.status', $application->job_vacancy_id) }}" class="btn-trampix-secondary"><i class="fas fa-arrow-left me-2"></i>Voltar</a>
                    <button type="submit" class="btn-trampix-primary btn-glow"><i class="fas fa-paper-plane me-2"></i>Enviar avaliação</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection