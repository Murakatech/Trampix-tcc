@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-tags text-gray-800 mr-2"></i>
    Administração - Categorias
</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ catQuery: '', segQuery: '' }">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Categorias</h1>
            <p class="text-sm text-gray-600">Administre todas as categorias existentes e crie novas.</p>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <span class="px-3 py-1 bg-gray-100 text-black rounded-full text-sm">Total: {{ $stats['total'] }}</span>
            <span class="ml-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Ativas: {{ $stats['active_total'] ?? 0 }}</span>
            <span class="ml-2 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Inativas: {{ $stats['inactive_total'] ?? 0 }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Categorias</h2>
            </div>
            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus text-gray-800"></i>
                    Criar nova categoria
                </h2>
                @if(session('ok'))
                    <div class="mb-4 text-black bg-gray-100 border border-gray-200 rounded-md p-3">
                        <i class="fas fa-check mr-2"></i>{{ session('ok') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-600 mb-1">Nome</label>
                        <input type="text" id="name" name="name" class="trampix-input w-full" placeholder="Ex.: Marketing Digital" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="segment_id" class="block text-xs font-medium text-gray-600 mb-1">Segmento</label>
                        <select id="segment_id" name="segment_id" class="trampix-input w-full">
                            <option value="">—</option>
                            @foreach($segmentsActiveOptions as $seg)
                                <option value="{{ $seg->id }}" {{ old('segment_id') == $seg->id ? 'selected' : '' }}>{{ $seg->name }}</option>
                            @endforeach
                        </select>
                        @error('segment_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-xs font-medium text-gray-600 mb-1">Descrição (opcional)</label>
                        <textarea id="description" name="description" class="trampix-input w-full" rows="3" placeholder="Breve descrição da categoria">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-trampix-primary">
                            <i class="fas fa-save mr-2"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>

            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-gray-800"></i>
                    Categorias ativas
                </h2>
                <div class="mb-3 flex items-center gap-3">
                    <input type="text" x-model="catQuery" class="trampix-input w-full max-w-md" placeholder="Buscar por nome ou segmento">
                </div>
                <div class="overflow-x-auto max-h-[480px] overflow-y-auto rounded-md border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segmento</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categoriesActive as $cat)
                                <tr class="hover:bg-gray-50" x-data="{ name: '{{ Str::of($cat->name)->replace("'", "\u0027") }}'.toLowerCase(), seg: '{{ Str::of($cat->segment->name ?? '-') ->replace("'", "\u0027") }}'.toLowerCase() }" x-show="!catQuery || name.includes(catQuery.toLowerCase()) || seg.includes(catQuery.toLowerCase())">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $cat->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $cat->description }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">
                                        @if($cat->segment)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs">{{ $cat->segment->name }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <button type="button" class="text-black hover:text-gray-700 text-sm" onclick="document.getElementById('edit-cat-{{ $cat->id }}').classList.toggle('hidden')">
                                            <i class="fas fa-pen mr-1"></i>Editar
                                        </button>
                                    </td>
                                </tr>
                                <tr id="edit-cat-{{ $cat->id }}" class="bg-gray-100 hidden">
                                    <td colspan="4" class="px-4 py-3">
                                        <form id="edit-cat-form-{{ $cat->id }}" method="POST" action="{{ route('admin.categories.update', $cat) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nome</label>
                                                    <input type="text" name="name" value="{{ old('name', $cat->name) }}" class="trampix-input w-full" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Descrição</label>
                                                    <input type="text" name="description" value="{{ old('description', $cat->description) }}" class="trampix-input w-full">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Segmento</label>
                                                    <select name="segment_id" class="trampix-input w-full">
                                                        <option value="">—</option>
                                                         @foreach($segmentsActiveOptions as $seg)
                                                            <option value="{{ $seg->id }}" {{ (string)($cat->segment_id) === (string)($seg->id) ? 'selected' : '' }}>{{ $seg->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button type="submit" form="edit-cat-form-{{ $cat->id }}" class="btn-trampix-primary">
                                                <i class="fas fa-save mr-1"></i>Salvar
                                            </button>
                                            <form method="POST" action="{{ route('admin.categories.deactivate', $cat) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-trampix-secondary">
                                                    <i class="fas fa-ban mr-1"></i>Desativar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-600">Nenhuma categoria cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $categoriesActive->links() }}
                </div>
            </div>

            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-archive text-gray-600"></i>
                    Categorias inativas
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Segmento</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categoriesInactive as $cat)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $cat->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $cat->segment->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form method="POST" action="{{ route('admin.categories.reactivate', $cat) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-trampix-primary">
                                                <i class="fas fa-undo mr-1"></i>Reativar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-600">Nenhuma categoria inativa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $categoriesInactive->links() }}
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Segmentos</h2>
            </div>
            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-layer-group text-gray-800"></i>
                    Criar novo segmento
                </h2>
                <form method="POST" action="{{ route('admin.segments.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="segment_name" class="block text-xs font-medium text-gray-600 mb-1">Nome</label>
                        <input type="text" id="segment_name" name="name" class="trampix-input w-full" placeholder="Ex.: Tecnologia" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn-trampix-primary">
                            <i class="fas fa-save mr-2"></i>Salvar
                        </button>
                    </div>
                </form>
            </div>

            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-gray-800"></i>
                    Segmentos ativos
                    <span class="ml-auto px-3 py-1 bg-gray-100 text-black rounded-full text-sm">Total: {{ $stats['segments_total'] }}</span>
                </h2>
                <div class="mb-3 flex items-center gap-3">
                    <input type="text" x-model="segQuery" class="trampix-input w-full max-w-md" placeholder="Buscar por nome do segmento">
                </div>
                <div class="overflow-x-auto max-h-[360px] overflow-y-auto rounded-md border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($segmentsActiveList as $seg)
                                <tr class="hover:bg-gray-50" x-data="{ name: '{{ Str::of($seg->name)->replace("'", "\u0027") }}'.toLowerCase() }" x-show="!segQuery || name.includes(segQuery.toLowerCase())">
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs">{{ $seg->name }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <button type="button" class="text-black hover:text-gray-700 text-sm" onclick="document.getElementById('edit-seg-{{ $seg->id }}').classList.toggle('hidden')">
                                            <i class="fas fa-pen mr-1"></i>Editar
                                        </button>
                                    </td>
                                </tr>
                                <tr id="edit-seg-{{ $seg->id }}" class="bg-gray-100 hidden">
                                    <td colspan="2" class="px-4 py-3">
                                        <form id="edit-seg-form-{{ $seg->id }}" method="POST" action="{{ route('admin.segments.update', $seg) }}" class="space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                <div class="md:col-span-2">
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nome</label>
                                                    <input type="text" name="name" value="{{ old('name', $seg->name) }}" class="trampix-input w-full" required>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="flex items-center justify-end gap-2 mt-3">
                                            <button type="submit" form="edit-seg-form-{{ $seg->id }}" class="btn-trampix-primary">
                                                <i class="fas fa-save mr-1"></i>Salvar
                                            </button>
                                            <form method="POST" action="{{ route('admin.segments.deactivate', $seg) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-trampix-secondary">
                                                    <i class="fas fa-ban mr-1"></i>Desativar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-600">Nenhum segmento cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $segmentsActiveList->links() }}
                </div>
            </div>

            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-archive text-gray-600"></i>
                    Segmentos inativos
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($segmentsInactive as $seg)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $seg->name }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form method="POST" action="{{ route('admin.segments.reactivate', $seg) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-trampix-primary">
                                                <i class="fas fa-undo mr-1"></i>Reativar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-gray-600">Nenhum segmento inativo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $segmentsInactive->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection