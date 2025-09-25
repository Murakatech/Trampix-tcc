<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Vagas Disponíveis
        </h2>
    </x-slot>

    <div class="p-6 space-y-4">
        {{-- Alerts de sessão --}}
        @if (session('ok'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('ok') }}</div>
        @endif

        @if ($vagas->isEmpty())
            <p>Nenhuma vaga encontrada.</p>
        @else
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2">Título</th>
                        <th class="border border-gray-300 px-3 py-2">Empresa</th>
                        <th class="border border-gray-300 px-3 py-2">Categoria</th>
                        <th class="border border-gray-300 px-3 py-2">Tipo</th>
                        <th class="border border-gray-300 px-3 py-2">Local</th>
                        <th class="border border-gray-300 px-3 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vagas as $vaga)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2">
                                <a href="{{ route('vagas.show', $vaga->id) }}" class="text-blue-600">
                                    {{ $vaga->title }}
                                </a>
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                {{ $vaga->company->name ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                {{ $vaga->category ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                {{ $vaga->contract_type ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                {{ $vaga->location_type ?? '-' }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <a href="{{ route('vagas.show', $vaga->id) }}" class="text-blue-600">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $vagas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
