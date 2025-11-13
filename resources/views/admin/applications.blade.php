@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-clipboard-list mr-2"></i>
    Administração - Candidaturas
</h1>
@endsection

@section('content')

    <div class="p-6">
        {{-- Estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-gray-900">
                    {{ $stats['total'] }}
                </div>
                <div class="text-sm text-gray-500">Total de Candidaturas</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-gray-900">
                    {{ $stats['pending'] }}
                </div>
                <div class="text-sm text-gray-500">Pendentes</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-gray-900">
                    {{ $stats['accepted'] }}
                </div>
                <div class="text-sm text-gray-500">Aceitas</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-2xl font-bold text-gray-900">
                    {{ $stats['rejected'] }}
                </div>
                <div class="text-sm text-gray-500">Rejeitadas</div>
            </div>
        </div>

        {{-- Lista de candidaturas --}}
        @if ($applications->isEmpty())
            <div class="bg-gray-100 border border-gray-300 text-gray-700 px-6 py-8 rounded-lg text-center">
                <div class="mb-4">
                    <i class="fas fa-clipboard-list text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma candidatura encontrada</h3>
                <p class="text-gray-600">Ainda não há candidaturas no sistema.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Todas as Candidaturas ({{ $applications->total() }})
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Freelancer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vaga
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empresa
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($applications as $application)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($application->freelancer->profile_photo)
                                                    <img class="h-10 w-10 rounded-full object-cover" 
                                                         src="{{ asset('storage/' . $application->freelancer->profile_photo) }}" 
                                                         alt="Foto">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $application->freelancer->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $application->freelancer->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $application->jobVacancy->title }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $application->jobVacancy->category?->name ?? 'Sem categoria' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $application->jobVacancy->company->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($application->status === 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">
                                                <i class="fas fa-clock mr-1 text-gray-700"></i> Pendente
                                            </span>
                                        @elseif($application->status === 'accepted')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">
                                                <i class="fas fa-check mr-1 text-gray-700"></i> Aceita
                                            </span>
                                        @elseif($application->status === 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">
                                                <i class="fas fa-times mr-1 text-gray-700"></i> Rejeitada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $application->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('vagas.show', $application->jobVacancy) }}" 
                                               class="text-gray-700 hover:text-black">
                                                <i class="fas fa-eye"></i> Ver Vaga
                                            </a>
                                            <a href="{{ route('freelancers.show', $application->freelancer) }}" 
                                               class="text-gray-700 hover:text-black">
                                                <i class="fas fa-user"></i> Ver Perfil
                                            </a>
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
            </div>
        @endif
    </div>
@endsection
