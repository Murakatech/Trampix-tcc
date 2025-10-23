{{-- Componente Sidebar Minimalista --}}
@props(['title' => 'Sidebar', 'description' => ''])

<aside 
    class="fixed inset-y-0 left-0 h-screen w-64 bg-white shadow-md transform transition-transform duration-300 ease-in-out z-50"
    :class="open ? 'translate-x-0' : '-translate-x-full'"
    {{ $attributes }}
>
    <!-- Cabeçalho da Sidebar -->
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">{{ $title }}</h2>
        @if($description)
            <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
        @endif
    </div>
    
    <!-- Conteúdo da Sidebar -->
    <div class="p-6">
        {{ $slot }}
    </div>
</aside>