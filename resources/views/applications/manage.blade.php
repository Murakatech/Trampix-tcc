@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-users text-blue-600 mr-2"></i>
    Gerenciar Candidaturas
</h1>
@endsection

@section('content')
<div class="space-y-8">
    {{-- Mensagens de sucesso/erro --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Estatísticas --}}
    <section>
        <h3 class="text-xl font-semibold text-gray-700 mb-6 flex items-center">
            <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
            Estatísticas de Candidaturas
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total de Candidaturas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total'] }}</div>
                <div class="text-gray-600 font-medium">Total de Candidaturas</div>
                <i class="fas fa-clipboard-list text-blue-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Pendentes -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-yellow-600 mb-2">{{ $stats['pending'] }}</div>
                <div class="text-gray-600 font-medium">Pendentes</div>
                <i class="fas fa-clock text-yellow-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Aceitas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['accepted'] }}</div>
                <div class="text-gray-600 font-medium">Aceitas</div>
                <i class="fas fa-check-circle text-green-300 text-2xl mt-2"></i>
            </div>
            
            <!-- Rejeitadas -->
            <div class="trampix-card text-center">
                <div class="text-3xl font-bold text-red-600 mb-2">{{ $stats['rejected'] }}</div>
                <div class="text-gray-600 font-medium">Rejeitadas</div>
                <i class="fas fa-times-circle text-red-300 text-2xl mt-2"></i>
            </div>
        </div>
    </section>

    {{-- Lista de candidaturas --}}
    <section>
        <div class="trampix-card">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-blue-500 mr-2"></i>
                    Todas as Candidaturas
                </h3>
            </div>
            <div class="overflow-hidden">
            @if($applications->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidato</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vaga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($applications as $application)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($application->freelancer->user->profile_photo)
                                                    <img src="{{ asset('storage/' . $application->freelancer->user->profile_photo) }}" 
                                                         alt="{{ $application->freelancer->user->name }}" 
                                                         class="h-10 w-10 rounded-full object-cover">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $application->freelancer->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $application->freelancer->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $application->jobVacancy->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($application->jobVacancy->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $application->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($application->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>Pendente
                                                </span>
                                                @break
                                            @case('accepted')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>Aceita
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times mr-1"></i>Rejeitada
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('applications.byVacancy', $application->jobVacancy->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors duration-200" 
                                               title="Ver detalhes da vaga">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($application->status === 'pending')
                                                <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900 transition-colors duration-200" 
                                                            title="Aceitar candidatura">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900 transition-colors duration-200" 
                                                            title="Rejeitar candidatura">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginação --}}
                @if($applications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $applications->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Nenhuma candidatura encontrada</h4>
                    <p class="text-gray-500 mb-6">Quando alguém se candidatar às suas vagas, aparecerá aqui.</p>
                    <a href="{{ route('job-vacancies.create') }}" class="btn-trampix-primary">
                        <i class="fas fa-plus mr-2"></i>Criar Nova Vaga
                    </a>
                </div>
            @endif
            </div>
        </div>
    </section>
</div>
@endsection