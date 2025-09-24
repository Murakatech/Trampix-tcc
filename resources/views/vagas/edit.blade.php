<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Editar Vaga
        </h2>
    </x-slot>

    <div class="p-6">
        <form action="{{ route('vagas.update', $vaga) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label>Título</label>
                <input type="text" name="title" value="{{ $vaga->title }}" class="border w-full" required>
            </div>
            <div>
                <label>Descrição</label>
                <textarea name="description" class="border w-full" required>{{ $vaga->description }}</textarea>
            </div>
            <div>
                <label>Requisitos</label>
                <textarea name="requirements" class="border w-full">{{ $vaga->requirements }}</textarea>
            </div>
            <div>
                <label>Categoria</label>
                <input type="text" name="category" value="{{ $vaga->category }}" class="border w-full">
            </div>
            <div>
                <label>Tipo de contrato</label>
                <select name="contract_type" class="border w-full">
                    <option value="">--</option>
                    @foreach(['PJ','CLT','Estágio','Freelance'] as $tipo)
                        <option value="{{ $tipo }}" {{ $vaga->contract_type == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Local</label>
                <select name="location_type" class="border w-full">
                    <option value="">--</option>
                    @foreach(['Remoto','Híbrido','Presencial'] as $loc)
                        <option value="{{ $loc }}" {{ $vaga->location_type == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Faixa salarial</label>
                <input type="text" name="salary_range" value="{{ $vaga->salary_range }}" class="border w-full">
            </div>
            <div>
                <label>Status</label>
                <select name="status" class="border w-full">
                    <option value="active" {{ $vaga->status=='active'?'selected':'' }}>Ativa</option>
                    <option value="closed" {{ $vaga->status=='closed'?'selected':'' }}>Encerrada</option>
                </select>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2">Atualizar</button>
        </form>
    </div>
</x-app-layout>
