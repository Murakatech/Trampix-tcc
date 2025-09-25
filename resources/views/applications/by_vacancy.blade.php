<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Candidatos para: {{ $vacancy->title }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-4">
        {{-- Mensagens de sucesso/erro --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Informações da vaga --}}
        <div class="bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">{{ $vacancy->title }}</h3>
            <p class="text-gray-600 mb-2">{{ $vacancy->description }}</p>
            <div class="text-sm text-gray-500">
                <p><strong>Categoria:</strong> {{ $vacancy->category }}</p>
                <p><strong>Tipo de contrato:</strong> {{ $vacancy->contract_type }}</p>
                <p><strong>Localização:</strong> {{ $vacancy->location_type }}</p>
                @if($vacancy->salary_range)
                    <p><strong>Faixa salarial:</strong> {{ $vacancy->salary_range }}</p>
                @endif
            </div>
        </div>

        {{-- Lista de candidatos --}}
        @if ($applications->isEmpty())
            <div class="bg-yellow-100 text-yellow-800 px-4 py-3 rounded">
                Nenhum candidato se inscreveu para esta vaga ainda.
            </div>
        @else
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Candidato
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Carta de Apresentação
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $application->freelancer->user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $application->freelancer->user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($application->status === 'accepted') bg-green-100 text-green-800
                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        {{ $application->cover_letter ?? 'Sem carta de apresentação' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $application->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($application->status === 'pending')
                                        <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700">
                                                Aceitar
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline ms-2">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">
                                                Rejeitar
                                            </button>
                                        </form>
                                    @elseif($application->status === 'accepted')
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                                            ✓ Aceito
                                        </span>
                                        <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline ms-2">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700">
                                                Rejeitar
                                            </button>
                                        </form>
                                    @elseif($application->status === 'rejected')
                                        <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="px-2 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700">
                                                Aceitar
                                            </button>
                                        </form>
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 ms-2">
                                            ✗ Rejeitado
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-sm text-gray-600">
                Total de candidatos: {{ $applications->count() }}
            </div>
        @endif

        {{-- Botão voltar --}}
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                ← Voltar ao Dashboard
            </a>
        </div>
    </div>
</x-app-layout>