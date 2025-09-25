<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ $vaga->title }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-4">
        {{-- Alerts --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ session('error') }}</div>
        @endif

        {{-- Erros de validação --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-2">
            <p><strong>Empresa:</strong> {{ $vaga->company->name ?? '-' }}</p>
            <p><strong>Descrição:</strong> {{ $vaga->description ?? '-' }}</p>
            <p><strong>Requisitos:</strong> {{ $vaga->requirements ?? '-' }}</p>
            <p><strong>Categoria:</strong> {{ $vaga->category ?? '-' }}</p>
            <p><strong>Tipo de contrato:</strong> {{ $vaga->contract_type ?? '-' }}</p>
            <p><strong>Local:</strong> {{ $vaga->location_type ?? '-' }}</p>
            <p><strong>Faixa salarial:</strong> {{ $vaga->salary_range ?? '-' }}</p>
            <p><strong>Status:</strong> {{ $vaga->status ?? 'active' }}</p>
        </div>

        {{-- Aplicar (freelancer logado) --}}
        @auth
            @can('isFreelancer')
                @php
                    $freelancerId = auth()->user()->freelancer->id ?? null;
                    $alreadyApplied = $freelancerId
                        ? \App\Models\Application::where('job_vacancy_id', $vaga->id)
                            ->where('freelancer_id', $freelancerId)
                            ->exists()
                        : false;
                @endphp

                @if ($alreadyApplied)
                    <p class="text-green-700">Você já se candidatou a esta vaga.</p>
                @else
                    <form method="POST" action="{{ route('applications.store', $vaga->id) }}" class="mt-2 space-y-2">
                        @csrf
                        <textarea
                            name="cover_letter"
                            rows="3"
                            placeholder="Mensagem opcional ao recrutador"
                            class="w-full border rounded p-2"
                        >{{ old('cover_letter') }}</textarea>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Aplicar
                        </button>
                    </form>
                @endif
            @endcan
        @endauth

        {{-- Ações da empresa dona --}}
        @can('isCompany')
            @if(($vaga->company?->user_id) === auth()->id())
                <div class="flex items-center gap-3 pt-4">
                    <a href="{{ route('applications.byVacancy', $vaga->id) }}" class="text-blue-600">Ver candidatos</a>
                    <a href="{{ route('vagas.edit', $vaga) }}" class="text-blue-600">Editar</a>
                    <form action="{{ route('vagas.destroy', $vaga) }}" method="POST" onsubmit="return confirm('Excluir esta vaga?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Excluir</button>
                    </form>
                </div>
            @endif
        @endcan
    </div>
</x-app-layout>
