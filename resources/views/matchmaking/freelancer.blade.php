@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Matchmaking</h1>
        <p class="mt-2 text-sm text-gray-600">Vagas compatíveis com seus segmentos</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jobs as $job)
            <div class="trampix-card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $job->title }}</h3>
                    <button type="button" class="text-gray-400 hover:text-purple-600" onclick="toggleLike(this)">❤</button>
                </div>
                <p class="text-sm text-gray-700 mb-2">{{ $job->company?->display_name ?? $job->company?->name ?? 'Empresa' }}</p>
                <p class="text-sm text-gray-600 mb-4">{{ \Illuminate\Support\Str::limit($job->description, 160) }}</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('vagas.show', $job) }}" class="btn-trampix-secondary">Ver mais</a>
                    <button type="button" class="btn-trampix-primary" onclick="saveJob({{ $job->id }}, this)">Salvar</button>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="bg-white border rounded-lg p-6 text-center">
                    <p class="text-gray-600">Nenhuma vaga compatível encontrada. Atualize seus segmentos no perfil.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
</div>

<script>
function toggleLike(btn){
    btn.classList.toggle('text-purple-600');
}
function saveJob(jobId, btn){
    btn.disabled = true;
    fetch('{{ route('matchmaking.save') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: new URLSearchParams({ job_id: jobId })
    }).then(r => r.json()).then(data => {
        btn.disabled = false;
        if (data && data.success){
            btn.textContent = 'Salvo';
        } else {
            alert('Não foi possível salvar a vaga.');
        }
    }).catch(() => { btn.disabled = false; alert('Falha ao salvar.'); });
}
</script>
@endsection

