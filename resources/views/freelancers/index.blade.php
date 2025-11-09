@extends('layouts.dashboard')

@section('header')
    <h2 class="h4 mb-0">
        {{ __('Gerenciar Freelancers') }}
    </h2>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Gerenciar Freelancers
                    </h4>
                    <span class="badge bg-success">{{ $freelancers->total() }} freelancers</span>
                </div>

                <div class="card-body">
                    @if($freelancers->count() > 0)
                        <!-- Estatísticas -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $freelancers->total() }}</h3>
                                        <p class="mb-0">Total de Freelancers</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $freelancers->where('cv_url', '!=', null)->count() }}</h3>
                                        <p class="mb-0">Com CV</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $freelancers->where('linkedin_url', '!=', null)->count() }}</h3>
                                        <p class="mb-0">Com LinkedIn</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $freelancers->where('hourly_rate', '>', 0)->count() }}</h3>
                                        <p class="mb-0">Com Valor/Hora</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tabela de Freelancers -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="trampix-table-header">
                                    <tr>
                                        <th>ID</th>
                                        <th>Freelancer</th>
                                        <th>Email</th>
                                        <th>Localização</th>
                                        <th>Valor/Hora</th>
                                        <th>CV</th>
                                        <th>LinkedIn</th>
                                        <th>Cadastro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($freelancers as $freelancer)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#{{ $freelancer->id }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($freelancer->profile_photo)
                                                        <img src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                                             alt="Foto de {{ $freelancer->user->name }}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-success rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-user text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $freelancer->user->name }}</strong>
                                                        @if($freelancer->bio)
                                                            <br><small class="text-muted">{{ Str::limit($freelancer->bio, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $freelancer->user->email }}" class="text-decoration-none">
                                                    {{ $freelancer->user->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($freelancer->location)
                                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                    {{ $freelancer->location }}
                                                @else
                                                    <span class="text-muted">Não informado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($freelancer->hourly_rate)
                                                    <span class="badge bg-success">R$ {{ number_format($freelancer->hourly_rate, 2, ',', '.') }}/h</span>
                                                @else
                                                    <span class="text-muted">Não informado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($freelancer->cv_url)
                                                    <a href="{{ route('freelancers.download-cv', $freelancer) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Baixar CV">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sem CV</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($freelancer->linkedin_url)
                                                    <a href="{{ $freelancer->linkedin_url }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Ver LinkedIn">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sem LinkedIn</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $freelancer->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('freelancers.show', $freelancer) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Ver Perfil">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="mailto:{{ $freelancer->user->email }}" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       title="Enviar Email">
                                                        <i class="fas fa-envelope"></i>
                                                    </a>
                                                    @if($freelancer->whatsapp)
                                                        <a href="{{ 'https://wa.me/55' . preg_replace('/\D+/', '', $freelancer->whatsapp) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-success"
                                                           title="Abrir no WhatsApp">
                    <i class="fab fa-whatsapp me-2"></i>
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
                            {{ $freelancers->links() }}
                        </div>
                    @else
                        <!-- Estado vazio -->
                        <div class="text-center py-5">
                            <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3 text-muted">Nenhum freelancer encontrado</h4>
                            <p class="text-muted">Ainda não há freelancers cadastrados no sistema.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection