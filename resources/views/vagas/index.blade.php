<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Lista de Vagas
        </h2>
    </x-slot>

    <div class="p-6">
        @foreach($vagas as $vaga)
            <div class="border p-4 mb-3 rounded">
                <h3 class="text-lg font-bold">
                    <a href="{{ route('vagas.show', $vaga) }}">{{ $vaga->title }}</a>
                </h3>
                <p>{{ Str::limit($vaga->description, 100) }}</p>
                <span class="text-sm text-gray-600">{{ $vaga->status }}</span>
            </div>
        @endforeach

        {{ $vagas->links() }}
    </div>
</x-app-layout>
