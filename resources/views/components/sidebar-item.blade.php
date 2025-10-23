@props(['href' => '#', 'icon' => null, 'active' => false])

<a href="{{ $href }}" @click="close()" 
   class="flex items-center gap-2 px-4 py-2 mx-2 mb-1 rounded-md transition 
     {{ $active ? 'border-l-4 border-[#8F3FF7] bg-[#8F3FF7]/10 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
    @if($icon)<i class="{{ $icon }} w-5"></i>@endif
    <span class="text-sm font-medium">{{ $slot }}</span>
</a>