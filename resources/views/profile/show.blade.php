@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Cabeçalho dinâmico --}}
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h3 mb-1">
                    {{ $user->name }}
                </h2>
                <div>
                    @if(isset($freelancer) && $freelancer)
                        <span class="badge bg-primary">Freelancer</span>
                    @endif
                    @if(isset($company) && $company)
                        <span class="badge bg-secondary">Empresa</span>
                    @endif
                </div>
            </div>
            <div>
                @auth
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-user-cog"></i> Configurar Conta
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    {{-- Card de Perfil unificado --}}
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    @php
                        $photo = null;
                        if(isset($freelancer) && $freelancer && $freelancer->profile_photo){
                            $photo = $freelancer->profile_photo;
                        } elseif(isset($company) && $company && $company->profile_photo){
                            $photo = $company->profile_photo;
                        }
                    @endphp
                    @if($photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto/Logo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border-radius: 8px;">
                            @if(isset($company) && $company)
                                <i class="fas fa-building fa-4x text-muted"></i>
                            @else
                                <i class="fas fa-user fa-4x text-muted"></i>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-md-9">
                    {{-- Informações comuns do usuário --}}
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <span>{{ $user->email }}</span>
                    </div>

                    {{-- Bloco Freelancer --}}
                    @if(isset($freelancer) && $freelancer)
                        @if($freelancer->bio)
                            <div class="mb-3">
                                <strong>Bio:</strong>
                                <p class="mb-0">{{ $freelancer->bio }}</p>
                            </div>
                        @endif

                        <div class="row">
                            @if($freelancer->location)
                                <div class="col-md-6 mb-2">
                                    <strong>Localização:</strong> {{ $freelancer->location }}
                                </div>
                            @endif
                            @if($freelancer->phone)
                                <div class="col-md-6 mb-2">
                                    <strong>Telefone:</strong> {{ $freelancer->phone }}
                                </div>
                            @endif
                            @if($freelancer->hourly_rate)
                                <div class="col-md-6 mb-2">
                                    <strong>Valor por Hora:</strong> R$ {{ number_format($freelancer->hourly_rate, 2, ',', '.') }}
                                </div>
                            @endif
                            @if($freelancer->availability)
                                <div class="col-md-6 mb-2">
                                    <strong>Disponibilidade:</strong> {{ $freelancer->availability }}
                                </div>
                            @endif
                        </div>

                        @if($freelancer->portfolio_url)
                            <div class="mb-3">
                                <strong>Portfólio:</strong>
                                <a href="{{ $freelancer->portfolio_url }}" target="_blank" class="text-primary">Ver Portfólio</a>
                            </div>
                        @endif

                        @if($freelancer->cv_url)
                            <div class="mb-3">
                                <strong>Currículo:</strong>
                                <a href="{{ route('freelancers.download-cv', $freelancer) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Baixar CV
                                </a>
                            </div>
                        @endif
                    @endif

                    {{-- Bloco Empresa --}}
                    @if(isset($company) && $company)
                        <div class="row">
                            @if($company->cnpj)
                                <div class="col-md-6 mb-2">
                                    <strong>CNPJ:</strong> {{ $company->cnpj }}
                                </div>
                            @endif
                            @if($company->sector)
                                <div class="col-md-6 mb-2">
                                    <strong>Setor:</strong> {{ $company->sector }}
                                </div>
                            @endif
                            @if($company->location)
                                <div class="col-md-6 mb-2">
                                    <strong>Localização:</strong> {{ $company->location }}
                                </div>
                            @endif
                            @if($company->phone)
                                <div class="col-md-6 mb-2">
                                    <strong>Telefone:</strong> {{ $company->phone }}
                                </div>
                            @endif
                            @if($company->employees_count)
                                <div class="col-md-6 mb-2">
                                    <strong>Funcionários:</strong> {{ $company->employees_count }}
                                </div>
                            @endif
                            @if($company->founded_year)
                                <div class="col-md-6 mb-2">
                                    <strong>Fundada em:</strong> {{ $company->founded_year }}
                                </div>
                            @endif
                        </div>

                        @if($company->description)
                            <div class="mb-3">
                                <strong>Sobre a empresa:</strong>
                                <p class="mb-0">{{ $company->description }}</p>
                            </div>
                        @endif

                        @if($company->website)
                            <div class="mb-3">
                                <strong>Website:</strong> 
                                <a href="{{ $company->website }}" target="_blank" class="text-primary">{{ $company->website }}</a>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Ações contextuais --}}
                <div class="col-12 mt-3">
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            @if(auth()->id() === $user->id)
                                @if(isset($freelancer) && $freelancer)
                                    <a href="{{ route('freelancers.edit', $freelancer) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar Perfil
                                    </a>
                                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-briefcase"></i> Minhas Candidaturas
                                    </a>
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i> Buscar Vagas
                                    </a>
                                @endif
                                @if(isset($company) && $company)
                                    <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Editar Empresa
                                    </a>
                                    <a href="{{ route('companies.vacancies', $company) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-briefcase"></i> Minhas Vagas
                                    </a>
                                @endif
                            @else
                                {{-- Visualização de empresa sobre freelancer --}}
                                @if(isset($freelancer) && $freelancer && auth()->user() && auth()->user()->isCompany())
                                    @if($freelancer->cv_url)
                                        <a href="{{ route('freelancers.download-cv', $freelancer) }}" class="btn btn-primary">
                                            <i class="fas fa-download"></i> Baixar CV
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-envelope"></i> Entrar em Contato
                                    </a>
                                @endif
                            @endif
                        @endauth
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Vagas recentes da empresa --}}
    @if(isset($company) && $company && $company->relationLoaded('vacancies') && $company->vacancies->count() > 0)
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-briefcase"></i> Vagas Recentes</h4>
                <div class="row">
                    @foreach($company->vacancies as $vacancy)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $vacancy->title }}</h5>
                                    <p class="card-text">{{ Str::limit($vacancy->description, 100) }}</p>
                                    <div class="mb-2">
                                        @if($vacancy->contract_type)
                                            <span class="badge bg-primary me-1">{{ $vacancy->contract_type }}</span>
                                        @endif
                                        @if($vacancy->location_type)
                                            <span class="badge bg-secondary me-1">{{ $vacancy->location_type }}</span>
                                        @endif
                                        @if($vacancy->category)
                                            <span class="badge bg-info">{{ $vacancy->category }}</span>
                                        @endif
                                    </div>
                                    @if($vacancy->salary_range)
                                        <p class="text-success mb-2"><strong>{{ $vacancy->salary_range }}</strong></p>
                                    @endif
                                    <small class="text-muted">Publicada em {{ $vacancy->created_at->format('d/m/Y') }}</small>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('vagas.show', $vacancy) }}" class="btn btn-outline-primary btn-sm">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection