@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Cabeçalho --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-0">
                @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                    Meu Perfil de Freelancer
                @else
                    Perfil de {{ $freelancer->user->name }}
                @endif
            </h2>
        </div>
    </div>

    {{-- Card do Perfil --}}
    <div class="card shadow-sm">
        <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($freelancer->profile_photo)
                                <img src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                     alt="Foto de {{ $freelancer->user->name }}" 
                                     class="img-thumbnail" 
                                     style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 200px; height: 200px; border-radius: 8px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h3>{{ $freelancer->user->name }}</h3>
                            
                            @if($freelancer->bio)
                                <div class="mb-3">
                                    <strong>Bio:</strong>
                                    <p>{{ $freelancer->bio }}</p>
                                </div>
                            @endif

                            @if($freelancer->location)
                                <div class="mb-3">
                                    <strong>Localização:</strong> {{ $freelancer->location }}
                                </div>
                            @endif

                            

                            @if($freelancer->whatsapp)
                                @php
                                    $waDigits = preg_replace('/\D+/', '', $freelancer->whatsapp);
                                    $waLink = 'https://wa.me/' . (\Illuminate\Support\Str::startsWith($waDigits, '55') ? $waDigits : ('55' . $waDigits));
                                @endphp
                                <div class="mb-3 d-flex align-items-center gap-2">
                                    <strong>WhatsApp:</strong>
                                    <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fab fa-whatsapp"></i> Conversar no WhatsApp
                                    </a>
                                </div>
                            @endif

                            @if($freelancer->hourly_rate)
                                <div class="mb-3">
                                    <strong>Valor por Hora:</strong> R$ {{ number_format($freelancer->hourly_rate, 2, ',', '.') }}
                                </div>
                            @endif

                            @if($freelancer->availability)
                                <div class="mb-3">
                                    <strong>Disponibilidade:</strong> {{ $freelancer->availability }}
                                </div>
                            @endif

                            @if($freelancer->portfolio_url)
                                <div class="mb-3">
                                    <strong>Portfólio:</strong> 
                                    <a href="{{ $freelancer->portfolio_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        Ver Portfólio
                                    </a>
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
                        </div>

                        <div class="col-md-4">
                            {{-- Botões de ação apenas para o próprio freelancer --}}
                            @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                                <div class="d-grid gap-2">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                                        <i class="fas fa-user-cog"></i> Configurar Conta
                                    </a>
                                    
                                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-briefcase"></i> Minhas Candidaturas
                                    </a>
                                    
                                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-search"></i> Buscar Vagas
                                    </a>
                                </div>
                            @else
                                {{-- Botões para empresas visualizando o perfil --}}
                                <div class="d-grid gap-2">
                                    @if($freelancer->cv_path)
                                        <a href="{{ route('freelancers.download-cv', $freelancer) }}" class="btn btn-primary">
                                            <i class="fas fa-download"></i> Baixar CV
                                        </a>
                                    @endif
                                    
                                    <a href="mailto:{{ $freelancer->user->email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-envelope"></i> Entrar em Contato
                                    </a>
                                    @if($freelancer->whatsapp)
                                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    @endif
                                    
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Voltar
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection