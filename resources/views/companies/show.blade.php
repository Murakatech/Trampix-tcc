<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Perfil de Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="row">
                        <div class="col-md-8">
                            <h3>{{ $company->name }}</h3>
                            
                            @if($company->description)
                                <div class="mb-3">
                                    <strong>Descrição:</strong>
                                    <p>{{ $company->description }}</p>
                                </div>
                            @endif

                            @if($company->website)
                                <div class="mb-3">
                                    <strong>Website:</strong> 
                                    <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $company->website }}
                                    </a>
                                </div>
                            @endif

                            @if($company->phone)
                                <div class="mb-3">
                                    <strong>Telefone:</strong> {{ $company->phone }}
                                </div>
                            @endif

                            @if($company->employees_count)
                                <div class="mb-3">
                                    <strong>Número de Funcionários:</strong> {{ $company->employees_count }}
                                </div>
                            @endif

                            @if($company->founded_year)
                                <div class="mb-3">
                                    <strong>Ano de Fundação:</strong> {{ $company->founded_year }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <strong>Status:</strong> 
                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $company->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="d-grid gap-2">
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Editar Perfil
                                </a>
                                
                                <a href="{{ route('vagas.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Criar Vaga
                                </a>
                                
                                <a href="{{ route('companies.vacancies', $company) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-briefcase"></i> Minhas Vagas
                                </a>
                                
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-home"></i> Página Inicial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>