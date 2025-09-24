<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $vaga->title }}
        </h2>
    </x-slot>

    <div class="p-6">
        <p><strong>Descrição:</strong> {{ $vaga->description }}</p>
        <p><strong>Requisitos:</strong> {{ $vaga->requirements }}</p>
        <p><strong>Categoria:</strong> {{ $vaga->category }}</p>
        <p><strong>Tipo de contrato:</strong> {{ $vaga->contract_type }}</p>
        <p><strong>Local:</strong> {{ $vaga->location_type }}</p>
        <p><strong>Faixa salarial:</strong> {{ $vaga->salary_range }}</p>
        <p><strong>Status:</strong> {{ $vaga->status }}</p>

        @can('isCompany')
            <a href="{{ route('vagas.edit', $vaga) }}" class="text-blue-600">Editar</a>
            <form action="{{ route('vagas.destroy', $vaga) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button class="text-red-600">Excluir</button>
            </form>
        @endcan
    </div>
</x-app-layout>
