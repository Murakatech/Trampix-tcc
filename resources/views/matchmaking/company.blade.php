@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Matchmaking (Empresa)</h1>
        <p class="mt-2 text-sm text-gray-600">Selecione uma vaga para ver freelancers compatíveis</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <form method="get" action="{{ route('matchmaking.company') }}" class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Vaga</label>
        <select name="job_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            <option value="">Selecione...</option>
            @foreach($companyVacancies as $v)
                <option value="{{ $v->id }}" {{ (string)$selectedId === (string)$v->id ? 'selected' : '' }}>
                    {{ $v->title }}
                </option>
            @endforeach
        </select>
        <div class="mt-3">
            <button type="submit" class="btn-trampix-company">Buscar</button>
        </div>
    </form>

    @if($selectedId)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($freelancers as $f)
            <div class="trampix-card">
                <h3 class="text-lg font-semibold text-gray-900">{{ $f->display_name ?? $f->user->name }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ \Illuminate\Support\Str::limit($f->bio, 160) }}</p>
                <p class="text-sm text-gray-700 mb-4">{{ $f->location ?? '—' }}</p>
                <div class="flex items-center justify-between">
                    <a href="{{ route('profiles.show', $f->user) }}" class="btn-trampix-secondary">Ver perfil</a>
                    <button type="button" class="btn-trampix-primary" onclick="visualLike(this)">Curtir</button>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <div class="bg-white border rounded-lg p-6 text-center">
                    <p class="text-gray-600">Nenhum freelancer compatível encontrado.</p>
                </div>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $freelancers instanceof \Illuminate\Pagination\LengthAwarePaginator ? $freelancers->links() : '' }}</div>
    @endif
</div>

<script>
function visualLike(btn){ btn.textContent = 'Curtido'; }
</script>
@endsection

