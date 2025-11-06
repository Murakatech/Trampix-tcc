@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Perfil do Usuário</h1>
@endsection

@section('content')
<div class="container py-4">
    @php
        // Determinar o perfil ativo baseado na sessão ou no que está disponível
        $activeRole = null;
        $activeProfile = null;
        $displayName = $user->name;
        
        if (auth()->check() && auth()->id() === $user->id) {
            // Para o próprio usuário, usar a sessão active_role
            $activeRole = session('active_role');
        } else {
            // Para visualização externa, mostrar o primeiro perfil disponível
            if ($company) {
                $activeRole = 'company';
            } elseif ($freelancer) {
                $activeRole = 'freelancer';
            }
        }
        
        // Definir perfil ativo e nome de exibição
        if ($activeRole === 'company' && $company) {
            $activeProfile = $company;
            $displayName = $company->display_name ?? $company->company_name ?? $user->name;
        } elseif ($activeRole === 'freelancer' && $freelancer) {
            $activeProfile = $freelancer;
            $displayName = $freelancer->display_name ?? $user->name;
        }
    @endphp

    {{-- Cabeçalho dinâmico --}}
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h3 mb-1">
                    {{ $displayName }}
                </h2>
                <div>
                    @if($activeRole === 'freelancer')
                        <span class="badge bg-primary">Freelancer</span>
                    @elseif($activeRole === 'company')
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
                        $photo = $activeProfile->profile_photo ?? null;
                    @endphp
                    @if($photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto/Logo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border-radius: 8px;">
                            @if($activeRole === 'company')
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

                    {{-- Informações específicas do perfil ativo --}}
                    @if($activeRole === 'freelancer' && $activeProfile)
                        @php
                            $waDigits = $activeProfile->whatsapp ? preg_replace('/\D+/', '', $activeProfile->whatsapp) : null;
                            $waLink = $waDigits ? ('https://wa.me/' . (\Illuminate\Support\Str::startsWith($waDigits, '55') ? $waDigits : ('55' . $waDigits))) : null;
                        @endphp
                        @if($activeProfile->bio)
                            <div class="mb-3">
                                <strong>Bio:</strong>
                                <p class="mb-0">{{ $activeProfile->bio }}</p>
                            </div>
                        @endif

                        <div class="row">
                            @if($activeProfile->location)
                                <div class="col-md-6 mb-2">
                                    <strong>Localização:</strong> {{ $activeProfile->location }}
                                </div>
                            @endif
                            
                            @if($activeProfile->hourly_rate)
                                <div class="col-md-6 mb-2">
                                    <strong>Valor por Hora:</strong> R$ {{ number_format($activeProfile->hourly_rate, 2, ',', '.') }}
                                </div>
                            @endif
                            @if($activeProfile->availability)
                                <div class="col-md-6 mb-2">
                                    <strong>Disponibilidade:</strong> {{ $activeProfile->availability }}
                                </div>
                            @endif
                        </div>

                        @if($activeProfile->portfolio_url)
                            <div class="mb-3">
                                <strong>Portfólio:</strong>
                                <a href="{{ $activeProfile->portfolio_url }}" target="_blank" class="text-primary">Ver Portfólio</a>
                            </div>
                        @endif

                        @if($waLink)
                            <div class="mb-3">
                                <strong>WhatsApp:</strong>
                                <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="fab fa-whatsapp"></i> Conversar no WhatsApp
                                </a>
                            </div>
                        @endif

                        @if($activeProfile->cv_url)
                            <div class="mb-3">
                                <strong>Currículo:</strong>
                                <a href="{{ route('freelancers.download-cv', $activeProfile) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Baixar CV
                                </a>
                            </div>
                        @endif
                    @elseif($activeRole === 'company' && $activeProfile)
                        <div class="row">
                            @if($activeProfile->cnpj)
                                <div class="col-md-6 mb-2">
                                    <strong>CNPJ:</strong> {{ $activeProfile->cnpj }}
                                </div>
                            @endif
                            @if($activeProfile->sector)
                                <div class="col-md-6 mb-2">
                                    <strong>Setor:</strong> {{ $activeProfile->sector }}
                                </div>
                            @endif
                            @if($activeProfile->location)
                                <div class="col-md-6 mb-2">
                                    <strong>Localização:</strong> {{ $activeProfile->location }}
                                </div>
                            @endif
                            @if($activeProfile->phone)
                                <div class="col-md-6 mb-2">
                                    <strong>Telefone:</strong> {{ $activeProfile->phone }}
                                </div>
                            @endif
                            @if($activeProfile->employees_count)
                                <div class="col-md-6 mb-2">
                                    <strong>Funcionários:</strong> {{ $activeProfile->employees_count }}
                                </div>
                            @endif
                            @if($activeProfile->founded_year)
                                <div class="col-md-6 mb-2">
                                    <strong>Fundada em:</strong> {{ $activeProfile->founded_year }}
                                </div>
                            @endif
                        </div>

                        @if($activeProfile->description)
                            <div class="mb-3">
                                <strong>Sobre a empresa:</strong>
                                <p class="mb-0">{{ $activeProfile->description }}</p>
                            </div>
                        @endif

                        @if($activeProfile->website)
                            <div class="mb-3">
                                <strong>Website:</strong> 
                                <a href="{{ $activeProfile->website }}" target="_blank" class="text-primary">{{ $activeProfile->website }}</a>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Ações contextuais --}}
                <div class="col-12 mt-3">
                    <div class="d-flex flex-wrap gap-2">
                        @auth
                            @if(auth()->id() === $user->id)
                                {{-- Botão único de configurar conta --}}
                                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                    <i class="fas fa-user-cog"></i> Configurar Conta
                                </a>
                                
                                {{-- Ações específicas do perfil ativo --}}
                                @if($activeRole === 'freelancer')
                                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-briefcase"></i> Minhas Candidaturas
                                    </a>
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i> Buscar Vagas
                                    </a>
                                @elseif($activeRole === 'company' && $activeProfile)
                                    <a href="{{ route('companies.vacancies', $activeProfile) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-briefcase"></i> Minhas Vagas
                                    </a>
                                @endif
                            @else
                                {{-- Visualização externa --}}
                                @if($activeRole === 'freelancer' && $activeProfile && auth()->user() && auth()->user()->isCompany())
                                    @if($activeProfile->cv_url)
                                        <a href="{{ route('freelancers.download-cv', $activeProfile) }}" class="btn btn-primary">
                                            <i class="fas fa-download"></i> Baixar CV
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-envelope"></i> Entrar em Contato
                                    </a>
                                    @if(isset($waLink) && $waLink)
                                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    @endif
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
    @if($activeRole === 'company' && $activeProfile && $activeProfile->relationLoaded('vacancies') && $activeProfile->vacancies->count() > 0)
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-briefcase"></i> Vagas Recentes</h4>
                <div class="row">
                    @foreach($activeProfile->vacancies as $vacancy)
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