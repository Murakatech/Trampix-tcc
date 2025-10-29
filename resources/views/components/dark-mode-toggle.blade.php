{{-- Componente Toggle do Modo Escuro --}}
<div class="flex items-center space-x-3">
    <span class="text-sm font-medium text-secondary">
        <i class="fas fa-sun"></i>
    </span>
    
    <button 
        id="darkModeToggle" 
        class="dark-mode-toggle" 
        aria-label="Alternar modo escuro"
        title="Alternar entre modo claro e escuro"
    >
        <span class="sr-only">Alternar modo escuro</span>
    </button>
    
    <span class="text-sm font-medium text-secondary">
        <i class="fas fa-moon"></i>
    </span>
</div>

{{-- Script inline para inicialização rápida --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar o estado do toggle baseado na preferência salva
    const isDark = localStorage.getItem('darkMode') === 'true' || 
                   (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    const toggle = document.getElementById('darkModeToggle');
    
    if (isDark) {
        document.documentElement.classList.add('dark');
        toggle.classList.add('dark');
    }
    
    // Event listener para o toggle
    toggle.addEventListener('click', function() {
        const isDarkMode = document.documentElement.classList.contains('dark');
        
        if (isDarkMode) {
            document.documentElement.classList.remove('dark');
            toggle.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        } else {
            document.documentElement.classList.add('dark');
            toggle.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        }
    });
});
</script>