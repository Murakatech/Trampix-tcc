<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Perfil de Freelancer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <div class="col-md-8">
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

                            @if($freelancer->phone)
                                <div class="mb-3">
                                    <strong>Telefone:</strong> {{ $freelancer->phone }}
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
                            <div class="d-grid gap-2">
                                <a href="{{ route('freelancers.edit', $freelancer) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar Perfil
                                </a>
                                
                                <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-briefcase"></i> Minhas Candidaturas
                                </a>
                                
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i> Buscar Vagas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>