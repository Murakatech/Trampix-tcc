<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $company->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Perfil da Empresa -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($company->profile_photo)
                                <img src="{{ asset('storage/' . $company->profile_photo) }}" 
                                     alt="Logo da {{ $company->name }}" 
                                     class="img-thumbnail" 
                                     style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 200px; height: 200px; border-radius: 8px;">
                                    <i class="fas fa-building fa-4x text-muted"></i>
                                </div>
                            @endif
                            
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h3 class="mb-0">{{ $company->name }}</h3>
                                @auth
                                    @if(auth()->user()->id === $company->user_id)
                                        <div class="btn-group">
                                            <a href="{{ route('companies.edit', $company) }}" class="btn btn-trampix-company btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <a href="{{ route('companies.vacancies', $company) }}" class="btn btn-trampix-company btn-sm">
                                                <i class="fas fa-briefcase"></i> Minhas Vagas
                                            </a>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                            
                            @if($company->cnpj)
                                <div class="mb-2">
                                    <strong>CNPJ:</strong> {{ $company->cnpj }}
                                </div>
                            @endif

                            @if($company->sector)
                                <div class="mb-2">
                                    <strong>Setor:</strong> {{ $company->sector }}
                                </div>
                            @endif

                            @if($company->location)
                                <div class="mb-2">
                                    <strong>Localização:</strong> {{ $company->location }}
                                </div>
                            @endif
                            
                            @if($company->description)
                                <div class="mb-3">
                                    <strong>Sobre a empresa:</strong>
                                    <p class="mt-1">{{ $company->description }}</p>
                                </div>
                            @endif

                            <div class="row">
                                @if($company->website)
                                    <div class="col-md-6 mb-2">
                                        <strong>Website:</strong> 
                                        <a href="{{ $company->website }}" target="_blank" class="text-primary">
                                            {{ $company->website }}
                                        </a>
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
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vagas Recentes -->
            @if($company->vacancies && $company->vacancies->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h4 class="mb-4">
                            <i class="fas fa-briefcase"></i> Vagas Recentes
                        </h4>
                        
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
                                                <p class="text-success mb-2">
                                                    <strong>{{ $vacancy->salary_range }}</strong>
                                                </p>
                                            @endif
                                            
                                            <small class="text-muted">
                                                Publicada em {{ $vacancy->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('vagas.show', $vacancy) }}" class="btn btn-outline-primary btn-sm">
                                                Ver Detalhes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($company->vacancies->count() >= 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('vagas.index') }}?company={{ $company->id }}" class="btn btn-outline-secondary">
                                    Ver Todas as Vagas
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h5>Nenhuma vaga publicada</h5>
                        <p class="text-muted">Esta empresa ainda não publicou vagas.</p>
                        
                        @auth
                            @if(auth()->user()->id === $company->user_id)
                                <a href="{{ route('vagas.create') }}" class="btn btn-trampix-company">
                                    <i class="fas fa-plus"></i> Criar Primeira Vaga
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>