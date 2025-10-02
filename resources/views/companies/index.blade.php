@extends('layouts.app')

@section('header')
    <h2 class="h4 mb-0">
        {{ __('Gerenciar Empresas') }}
    </h2>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Gerenciar Empresas
                    </h4>
                    <span class="badge bg-primary">{{ $companies->total() }} empresas</span>
                </div>

                <div class="card-body">
                    @if($companies->count() > 0)
                        <!-- Estatísticas -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $companies->total() }}</h3>
                                        <p class="mb-0">Total de Empresas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $companies->where('user.email_verified_at', '!=', null)->count() }}</h3>
                                        <p class="mb-0">Verificadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $companies->sum(function($company) { return $company->vacancies_count ?? 0; }) }}</h3>
                                        <p class="mb-0">Vagas Criadas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $companies->where('user.email_verified_at', null)->count() }}</h3>
                                        <p class="mb-0">Não Verificadas</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Empresas -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Empresa</th>
                                        <th>Email</th>
                                        <th>CNPJ</th>
                                        <th>Cidade</th>
                                        <th>Status</th>
                                        <th>Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($companies as $company)
                                        <tr>
                                            <td>{{ $company->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($company->profile_photo)
                                                        <img src="{{ asset('storage/' . $company->profile_photo) }}" 
                                                             alt="Logo" class="rounded-circle me-2" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-building text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $company->name }}</strong>
                                                        @if($company->sector)
                                                            <br><small class="text-muted">{{ $company->sector }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $company->user->email }}
                                                @if($company->user->email_verified_at)
                                                    <i class="fas fa-check-circle text-success ms-1" title="Email verificado"></i>
                                                @else
                                                    <i class="fas fa-exclamation-circle text-warning ms-1" title="Email não verificado"></i>
                                                @endif
                                            </td>
                                            <td>
                                                @if($company->cnpj)
                                                    {{ $company->cnpj }}
                                                @else
                                                    <span class="text-muted">Não informado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($company->city)
                                                    {{ $company->city }}
                                                    @if($company->state)
                                                        , {{ $company->state }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">Não informado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($company->is_active)
                                                    <span class="badge bg-success">Ativa</span>
                                                @else
                                                    <span class="badge bg-danger">Inativa</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $company->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('companies.show', $company) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($company->user->email_verified_at)
                                                        <a href="mailto:{{ $company->user->email }}" 
                                                           class="btn btn-sm btn-outline-secondary" 
                                                           title="Enviar email">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $companies->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma empresa encontrada</h5>
                            <p class="text-muted">Ainda não há empresas cadastradas no sistema.</p>
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

.table th {
    border-top: none;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush