{{-- Botão Toggle da Sidebar --}}
@props(['icon' => '☰', 'text' => ''])

<button 
    @click="open = !open"
    class="p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
    aria-label="Toggle Sidebar"
    {{ $attributes }}
>
    @if($icon === 'svg')
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    @else
        <span class="text-lg">{{ $icon }}</span>
        @if($text)
            <span class="ml-2">{{ $text }}</span>
        @endif
    @endif
</button>