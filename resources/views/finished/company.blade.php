@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Trabalhos Finalizados</h1>
<p class="text-gray-500">Vagas concluídas e avaliadas pela sua empresa</p>
@endsection

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($applications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-archive fa-4x text-muted mb-4"></i>
                <h3 class="h5 mb-3">Nenhum trabalho finalizado ainda</h3>
                <p class="text-muted">Quando sua empresa finalizar e avaliar uma parceria, ela aparecerá aqui.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($applications as $application)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-1">{{ $application->jobVacancy->title }}</h5>
                                    <small class="text-muted">{{ $application->jobVacancy->category?->name }}</small>
                                </div>
                                <span class="badge bg-success">Finalizado</span>
                            </div>
                            <div class="mb-2">
                                <strong>Freelancer:</strong> {{ $application->freelancer->user->name ?? 'Freelancer' }}
                            </div>
                            <div class="mb-2">
                                <strong>Sua avaliação enviada:</strong>
                                @php $myAvg = $application->company_rating_avg ?? $application->company_rating; @endphp
                                @if($myAvg)
                                    <span class="badge bg-primary">{{ number_format((float)$myAvg, 1) }}/5</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>Nota que sua empresa recebeu:</strong>
                                @php $fromFreelancerAvg = $application->freelancer_rating_avg ?? $application->freelancer_rating; @endphp
                                @if($fromFreelancerAvg)
                                    <span class="badge bg-warning text-dark">{{ number_format((float)$fromFreelancerAvg, 1) }}/5</span>
                                @else
                                    <span class="text-muted">Ainda não avaliada pelo freelancer</span>
                                @endif
                            </div>
                            @if($application->freelancer_comment)
                                <div class="mt-2">
                                    <strong>Comentário do freelancer:</strong>
                                    <p class="text-muted mb-0">{{ $application->freelancer_comment }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <a href="{{ route('vagas.status', $application->jobVacancy->id) }}" class="btn btn-sm btn-outline-primary">Ver detalhes da vaga</a>
                            <a href="{{ route('profiles.show', $application->freelancer->user) }}" class="btn btn-sm btn-outline-secondary">Ver perfil do freelancer</a>
                            @if($application->company_rating || $application->company_rating_avg)
                                <a href="{{ route('applications.evaluate.show', $application) }}" class="btn btn-sm btn-outline-secondary">Avaliação Completa</a>
                            @elseif($application->status === 'ended')
                                <a href="{{ route('applications.evaluate.create', $application) }}" class="btn btn-sm btn-primary">Avaliar</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
