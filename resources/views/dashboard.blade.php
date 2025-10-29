@php
    use Illuminate\Support\Facades\Gate;
@endphp

@extends('layouts.dashboard')

@section('header')
@php
    $userName = auth()->user()->name;
    if (auth()->user()->isCompany() && auth()->user()->company) {
        $userName = auth()->user()->company->name;
    } elseif (auth()->user()->isFreelancer() && auth()->user()->freelancer) {
        $userName = auth()->user()->name;
    }
@endphp
<h1 class="text-2xl font-bold text-gray-900 text-center mb-8">Bem vindo, {{ $userName }}!</h1>
@endsection

@section('content')


<div class="space-y-8">

    {{-- Seção ADMIN --}}
    @if(Gate::allows('isAdmin'))
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-cog text-purple-500 mr-2"></i> Área Administrativa
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Freelancers</p>
                            <p class="text-sm text-gray-500">Gerenciar freelancers cadastrados</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.companies') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-building text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Empresas</p>
                            <p class="text-sm text-gray-500">Gerenciar empresas cadastradas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.applications') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Candidaturas</p>
                            <p class="text-sm text-gray-500">Monitorar todas candidaturas</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    @endif

    {{-- Seção EMPRESA --}}
    @if(Gate::allows('isCompany'))
        <section>
            @php
                $company = auth()->user()->company;
                $totalVagas = $company ? $company->vacancies()->count() : 0;
                $vagasAtivas = $company ? $company->vacancies()->where('status', 'active')->count() : 0;
                $totalAplicacoes = $company ? \App\Models\Application::whereIn('job_vacancy_id', $company->vacancies->pluck('id'))->count() : 0;
                $aplicacoesPendentes = $company ? \App\Models\Application::whereIn('job_vacancy_id', $company->vacancies->pluck('id'))->where('status', 'pending')->count() : 0;
            @endphp

            <!-- Resumo Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="trampix-card text-center">
                    <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-briefcase text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalVagas }}</h3>
                    <p class="text-sm text-gray-500">Total de Vagas</p>
                </div>

                <div class="trampix-card text-center">
                    <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $vagasAtivas }}</h3>
                    <p class="text-sm text-gray-500">Vagas Ativas</p>
                </div>

                <div class="trampix-card text-center">
                    <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalAplicacoes }}</h3>
                    <p class="text-sm text-gray-500">Total Aplicações</p>
                </div>

                <div class="trampix-card text-center">
                    <div class="bg-orange-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $aplicacoesPendentes }}</h3>
                    <p class="text-sm text-gray-500">Pendentes</p>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('vagas.create') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Nova Vaga</p>
                            <p class="text-sm text-gray-500">Criar nova oportunidade</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('company.vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Minhas Vagas</p>
                            <p class="text-sm text-gray-500">Gerenciar vagas publicadas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Ver Aplicações</p>
                            <p class="text-sm text-gray-500">Candidatos às suas vagas</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Vagas Recentes -->
            @if($company && $company->vacancies()->exists())
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-clock text-blue-500 mr-2"></i> Vagas Recentes
                </h3>
                <div class="space-y-4">
                    @foreach($company->vacancies()->latest()->take(3)->get() as $vaga)
                    <div class="trampix-card">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $vaga->title }}</h4>
                                <p class="text-sm text-gray-500">{{ Str::limit($vaga->description, 100) }}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-xs px-2 py-1 rounded-full {{ $vaga->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($vaga->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $vaga->applications()->count() }} candidatos
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('vagas.show', $vaga) }}" class="btn-trampix-secondary text-sm">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </section>
    @endif

    {{-- Seção FREELANCER --}}
    @if(Gate::allows('isFreelancer'))
        <section>
            <h2 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
                <i class="fas fa-user text-blue-500 mr-2"></i> Área do Freelancer
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Minhas Candidaturas</p>
                            <p class="text-sm text-gray-500">Acompanhar candidaturas</p>
                        </div>
                    </div>
                </a>

                @can('isCompany')
                    <a href="{{ route('company.vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-briefcase text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Minhas Vagas</p>
                                <p class="text-sm text-gray-500">Gerenciar vagas da empresa</p>
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-search text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Buscar Vagas</p>
                                <p class="text-sm text-gray-500">Encontrar oportunidades</p>
                            </div>
                        </div>
                    </a>
                @endcan
            </div>
        </section>
    @endif

    {{-- Informações do Sistema --}}
    <section class="pt-6 border-t border-gray-200">
        <div class="text-center">
            <small class="text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Dashboard Trampix - Navegação intuitiva sem botões tradicionais
            </small>
        </div>
    </section>

</div>
@endsection
