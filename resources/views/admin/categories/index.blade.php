@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">
    <i class="fas fa-tags text-purple-600 mr-2"></i>
    Administração - Categorias
</h1>
@endsection

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Categorias</h1>
            <p class="text-sm text-gray-600">Administre todas as categorias existentes e crie novas.</p>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm">Total: {{ $stats['total'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form de criação -->
        <div class="lg:col-span-1">
            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus text-purple-600"></i>
                    Criar nova categoria
                </h2>
                @if(session('ok'))
                    <div class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded-md p-3">
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
        </div>

        <!-- Lista de categorias -->
        <div class="lg:col-span-2">
            <div class="trampix-card bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-purple-600"></i>
                    Categorias existentes
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($categories as $cat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $cat->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $cat->slug }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $cat->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-gray-600">Nenhuma categoria cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection