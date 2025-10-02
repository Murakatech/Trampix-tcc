@extends('layouts.app')

@section('header')
<div class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3 class="h5 fw-bold mb-4">Bem-vindo, {{ auth()->user()->name }}!</h3>
                    
                    @can('isAdmin')
                        <div class="mb-4">
                            <h4 class="h6 text-muted mb-3">
                                <i class="fas fa-cog me-2"></i>Área Administrativa
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="{{ route('admin.freelancers') }}" 
                                       class="card text-decoration-none border-primary">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-users text-primary fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-primary">Freelancers</div>
                                                <div class="small text-muted">Gerenciar freelancers</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-md-4">
                                    <a href="{{ route('admin.companies') }}" 
                                       class="card text-decoration-none border-success">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-building text-success fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-success">Empresas</div>
                                                <div class="small text-muted">Gerenciar empresas</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-md-4">
                                    <a href="{{ route('admin.applications') }}" 
                                       class="card text-decoration-none border-info">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-clipboard-list text-info fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-info">Candidaturas</div>
                                                <div class="small text-muted">Ver todas candidaturas</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    
                    @can('isCompany')
                        <div class="mb-4">
                            <h4 class="h6 text-muted mb-3">
                                <i class="fas fa-briefcase me-2"></i>Área da Empresa
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('vagas.create') }}" 
                                       class="card text-decoration-none border-primary">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-plus text-primary fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-primary">Nova Vaga</div>
                                                <div class="small text-muted">Criar nova vaga</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-md-6">
                                    <a href="{{ route('home') }}" 
                                       class="card text-decoration-none border-success">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-list text-success fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-success">Minhas Vagas</div>
                                                <div class="small text-muted">Gerenciar vagas</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    
                    @can('isFreelancer')
                        <div class="mb-4">
                            <h4 class="h6 text-muted mb-3">
                                <i class="fas fa-user me-2"></i>Área do Freelancer
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('applications.index') }}" 
                                       class="card text-decoration-none border-primary">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-clipboard-list text-primary fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-primary">Minhas Candidaturas</div>
                                                <div class="small text-muted">Ver candidaturas</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="col-md-6">
                                    <a href="{{ route('home') }}" 
                                       class="card text-decoration-none border-success">
                                        <div class="card-body d-flex align-items-center">
                                            <i class="fas fa-search text-success fs-4 me-3"></i>
                                            <div>
                                                <div class="fw-bold text-success">Buscar Vagas</div>
                                                <div class="small text-muted">Encontrar oportunidades</div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endcan
                    
                    {{-- Link para Styleguide (desenvolvimento) --}}
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Ferramentas de Desenvolvimento</small>
                            <a href="{{ route('styleguide') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-palette me-1"></i>Ver Styleguide
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
