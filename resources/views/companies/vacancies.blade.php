@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Header da Empresa -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            @if($company->profile_photo)
                                <img src="{{ asset('storage/' . $company->profile_photo) }}" 
                                     alt="Logo da {{ $company->name }}" 
                                     class="rounded-circle" 
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-primary rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-building fa-2x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h3 class="mb-1">{{ $company->name }}</h3>
                            @if($company->sector)
                                <p class="text-muted mb-1">{{ $company->sector }}</p>
                            @endif
                            @if($company->location)
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $company->location }}
                                </p>
                            @endif
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user-cog me-1"></i>
                                Gerenciar Conta
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vagas da Empresa -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>
                        Minhas Vagas
                    </h4>
                    <div>
                        <span class="badge bg-primary me-2">{{ $vacancies->total() }} vagas</span>
                        <a href="{{ route('vagas.create') }}" class="btn btn-trampix-company btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Nova Vaga
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($vacancies->count() > 0)
                        <!-- Estatísticas das Vagas -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $vacancies->where('status', 'active')->count() }}</h3>
                                        <p class="mb-0">Vagas Ativas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $vacancies->where('status', 'paused')->count() }}</h3>
                                        <p class="mb-0">Pausadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $vacancies->where('status', 'closed')->count() }}</h3>
                                        <p class="mb-0">Fechadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $vacancies->sum(function($vaga) { return $vaga->applications->count(); }) }}</h3>
                                        <p class="mb-0">Candidaturas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Vagas -->
                        <div class="row">
                            @foreach($vacancies as $vaga)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $vaga->title }}</h6>
                                            @if($vaga->status === 'active')
                                                <span class="badge bg-success">Ativa</span>
                                            @elseif($vaga->status === 'paused')
                                                <span class="badge bg-warning">Pausada</span>
                                            @else
                                                <span class="badge bg-secondary">Fechada</span>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $vaga->location }}
                                            </p>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ ucfirst($vaga->type) }}
                                            </p>
                                            @if($vaga->salary_min && $vaga->salary_max)
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-dollar-sign me-1"></i>
                                                    R$ {{ number_format($vaga->salary_min, 0, ',', '.') }} - 
                                                    R$ {{ number_format($vaga->salary_max, 0, ',', '.') }}
                                                </p>
                                            @endif
                                            <p class="card-text">{{ Str::limit($vaga->description, 100) }}</p>
                                            
                                            <!-- Candidaturas -->
                                            @if($vaga->applications->count() > 0)
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $vaga->applications->count() }} candidatura(s)
                                                    </small>
                                                    <div class="mt-2">
                                                        @foreach($vaga->applications->take(3) as $application)
                                                            <span class="badge bg-light text-dark me-1" title="{{ $application->freelancer->user->name }}">
                                                                {{ Str::limit($application->freelancer->user->name, 15) }}
                                                            </span>
                                                        @endforeach
                                                        @if($vaga->applications->count() > 3)
                                                            <span class="badge bg-secondary">+{{ $vaga->applications->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <a href="{{ route('vagas.show', $vaga) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>
                                                    Ver
                                                </a>
                                                <a href="{{ route('vagas.edit', $vaga) }}" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>
                                                    Editar
                                                </a>
                                                @if($vaga->applications->count() > 0)
                                                    <a href="{{ route('applications.by_vacancy', $vaga) }}" 
                                                       class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-users me-1"></i>
                                                        Candidatos
                                                    </a>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                Criada em {{ $vaga->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $vacancies->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma vaga criada</h5>
                            <p class="text-muted">Você ainda não criou nenhuma vaga de emprego.</p>
                            <a href="{{ route('vagas.create') }}" class="btn btn-trampix-company">
                                <i class="fas fa-plus me-1"></i>
                                Criar Primeira Vaga
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group .btn {
    flex: 1;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush