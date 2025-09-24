<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nova Vaga
        </h2>
    </x-slot>

    <div class="p-6">
        <form action="{{ route('vagas.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label>Título</label>
                <input type="text" name="title" class="border w-full" required>
            </div>
            <div>
                <label>Descrição</label>
                <textarea name="description" class="border w-full" required></textarea>
            </div>
            <div>
                <label>Requisitos</label>
                <textarea name="requirements" class="border w-full"></textarea>
            </div>
            <div>
                <label>Categoria</label>
                <input type="text" name="category" class="border w-full">
            </div>
            <div>
                <label>Tipo de contrato</label>
                <select name="contract_type" class="border w-full">
                    <option value="">--</option>
                    <option value="PJ">PJ</option>
                    <option value="CLT">CLT</option>
                    <option value="Estágio">Estágio</option>
                    <option value="Freelance">Freelance</option>
                </select>
            </div>
            <div>
                <label>Local</label>
                <select name="location_type" class="border w-full">
                    <option value="">--</option>
                    <option value="Remoto">Remoto</option>
                    <option value="Híbrido">Híbrido</option>
                    <option value="Presencial">Presencial</option>
                </select>
            </div>
            <div>
                <label>Faixa salarial</label>
                <input type="text" name="salary_range" class="border w-full">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2">Salvar</button>
        </form>
    </div>
</x-app-layout>
