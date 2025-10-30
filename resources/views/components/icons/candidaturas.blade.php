@props(['class' => 'w-6 h-6'])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
  <!-- Background circle with gradient -->
  <defs>
    <linearGradient id="candidaturasGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#8F3FF7;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#B9FF66;stop-opacity:1" />
    </linearGradient>
  </defs>
  
  <!-- Main document icon -->
  <path d="M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2Z" 
        fill="url(#candidaturasGradient)" 
        stroke="currentColor" 
        stroke-width="0.5" 
        opacity="0.9"/>
  
  <!-- Document fold -->
  <path d="M14 2V8H20" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="1.5" 
        stroke-linecap="round" 
        stroke-linejoin="round"/>
  
  <!-- User icon inside document -->
  <circle cx="12" cy="13" r="2.5" 
          fill="white" 
          stroke="currentColor" 
          stroke-width="0.8"/>
  
  <!-- User body -->
  <path d="M8.5 18.5C8.5 16.5 10 15 12 15C14 15 15.5 16.5 15.5 18.5" 
        fill="none" 
        stroke="white" 
        stroke-width="1.2" 
        stroke-linecap="round"/>
  
  <!-- Checkmark indicator -->
  <circle cx="18" cy="6" r="3" 
          fill="#B9FF66" 
          stroke="white" 
          stroke-width="1"/>
  
  <path d="M16.5 6L17.5 7L19.5 5" 
        fill="none" 
        stroke="white" 
        stroke-width="1.5" 
        stroke-linecap="round" 
        stroke-linejoin="round"/>
</svg>