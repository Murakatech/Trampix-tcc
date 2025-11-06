@props([
    'title' => 'Filtros',
    'action' => '#',
    'method' => 'GET',
    'applyLabel' => 'Aplicar Filtros',
    'resetHref' => null,
    'categories' => [],
    'locationTypes' => [],
    'selectedCategory' => null,
    'selectedLocationType' => null,
    'searchName' => 'search',
    'searchValue' => request('search'),
    'showHeader' => true,
    'noContainer' => false,
    'showNote' => false
])

@if(!$noContainer)
    <div {{ $attributes->merge(['class' => 'w-full bg-white rounded-xl shadow-lg border border-gray-200 p-6']) }}>
        @if($showHeader)
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-filter text-purple-600"></i>
                    {{ $title }}
                </h2>
                @if($showNote)
                    <span class="text-xs text-gray-500">Componente reutilizável</span>
                @endif
            </div>
        @endif

        <form method="{{ strtoupper($method) }}" action="{{ $action }}" class="space-y-0" data-filter-form="true" data-results-container="#resultsContainer" data-loading="#loadingIndicator">
            <div class="flex items-end gap-3 w-full overflow-x-auto">
                <div class="flex-1 min-w-0">
                    <label for="{{ $searchName }}" class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                    <div class="relative">
                        <input type="text" id="{{ $searchName }}" name="{{ $searchName }}" value="{{ $searchValue }}"
                               class="trampix-input w-full pl-9" placeholder="Título, empresa, palavras-chave" autocomplete="off" data-search-input="true" data-suggest-target="#{{ $searchName }}_suggestions">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        <div id="{{ $searchName }}_suggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden"></div>
                    </div>
                </div>

                <div class="w-48">
                    <label for="category" class="block text-xs font-medium text-gray-600 mb-1">Categoria</label>
                    <select id="category" name="category" class="trampix-input w-full">
                        <option value="">Todas</option>
                        @foreach(($categories ?: []) as $cat)
                            <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-48">
                    <label for="location_type" class="block text-xs font-medium text-gray-600 mb-1">Localização</label>
                    <select id="location_type" name="location_type" class="trampix-input w-full">
                        <option value="">Todas</option>
                        @foreach(($locationTypes ?: []) as $type)
                            <option value="{{ $type }}" {{ $selectedLocationType === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 flex-none pb-0">
                    <button type="submit" class="btn-trampix-primary">
                        <i class="fas fa-search mr-2"></i>{{ $applyLabel }}
                    </button>
                    @if($resetHref)
                        <a href="{{ $resetHref }}" class="btn-trampix-secondary">
                            <i class="fas fa-times mr-2"></i>Limpar
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
@else
    <form {{ $attributes->merge(['class' => 'space-y-0 w-full']) }} method="{{ strtoupper($method) }}" action="{{ $action }}" data-filter-form="true" data-results-container="#resultsContainer" data-loading="#loadingIndicator">
        <div class="flex items-end gap-3 w-full overflow-x-auto">
            <div class="flex-1 min-w-0">
                <label for="{{ $searchName }}" class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                <div class="relative">
                    <input type="text" id="{{ $searchName }}" name="{{ $searchName }}" value="{{ $searchValue }}"
                           class="trampix-input w-full pl-9" placeholder="Título, empresa, palavras-chave" autocomplete="off" data-search-input="true" data-suggest-target="#{{ $searchName }}_suggestions">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    <div id="{{ $searchName }}_suggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden"></div>
                </div>
            </div>

            <div class="w-48">
                <label for="category" class="block text-xs font-medium text-gray-600 mb-1">Categoria</label>
                <select id="category" name="category" class="trampix-input w-full">
                    <option value="">Todas</option>
                    @foreach(($categories ?: []) as $cat)
                        <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-48">
                <label for="location_type" class="block text-xs font-medium text-gray-600 mb-1">Localização</label>
                <select id="location_type" name="location_type" class="trampix-input w-full">
                    <option value="">Todas</option>
                    @foreach(($locationTypes ?: []) as $type)
                        <option value="{{ $type }}" {{ $selectedLocationType === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 flex-none pb-0">
                <button type="submit" class="btn-trampix-primary">
                    <i class="fas fa-search mr-2"></i>{{ $applyLabel }}
                </button>
                @if($resetHref)
                    <a href="{{ $resetHref }}" class="btn-trampix-secondary">
                        <i class="fas fa-times mr-2"></i>Limpar
                    </a>
                @endif
            </div>
        </div>
    </form>
@endif