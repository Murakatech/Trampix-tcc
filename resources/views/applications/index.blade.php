<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Minhas Candidaturas
        </h2>
    </x-slot>

    <div class="p-6">
        {{-- Mensagens de sucesso/erro --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Lista de candidaturas --}}
        @if ($applications->isEmpty())
            <div class="bg-gray-100 border border-gray-300 text-gray-700 px-6 py-8 rounded-lg text-center">
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Você não tem nenhuma aplicação</h3>
                <p class="text-gray-600 mb-4">Comece a se candidatar às vagas disponíveis para aparecerem aqui.</p>
                <a href="{{ route('vagas.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Ver Vagas Disponíveis
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
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
                                Data da Candidatura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($applications as $application)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $application->jobVacancy->title }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $application->jobVacancy->category }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $application->jobVacancy->company->company_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $application->jobVacancy->location_type }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($application->status === 'accepted') bg-green-100 text-green-800
                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($application->status === 'accepted')
                                            ✓ Aceito
                                        @elseif($application->status === 'rejected')
                                            ✗ Rejeitado
                                        @else
                                            ⏳ Pendente
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $application->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('vagas.show', $application->jobVacancy) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            Ver Vaga
                                        </a>
                                        
                                        @if($application->status === 'pending')
                                            <form action="{{ route('applications.cancel', $application) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Tem certeza que deseja cancelar esta candidatura?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 ml-2">
                                                    Cancelar
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

            {{-- Estatísticas --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $applications->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Total de Candidaturas</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl font-bold text-green-600">
                        {{ $applications->where('status', 'accepted')->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Aceitas</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $applications->where('status', 'pending')->count() }}
                    </div>
                    <div class="text-sm text-gray-500">Pendentes</div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>