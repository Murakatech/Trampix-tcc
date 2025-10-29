// Dark Mode Manager para Trampix
class DarkModeManager {
    constructor() {
        this.darkModeKey = 'trampix-dark-mode';
        this.init();
    }

    init() {
        // Verificar preferência salva ou preferência do sistema
        const savedMode = localStorage.getItem(this.darkModeKey);
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        const shouldUseDarkMode = savedMode === 'true' || (savedMode === null && systemPrefersDark);
        
        if (shouldUseDarkMode) {
            this.enableDarkMode();
        } else {
            this.disableDarkMode();
        }

        // Escutar mudanças na preferência do sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem(this.darkModeKey) === null) {
                if (e.matches) {
                    this.enableDarkMode();
                } else {
                    this.disableDarkMode();
                }
            }
        });

        // Configurar botão de alternância
        this.setupToggleButton();
    }

    enableDarkMode() {
        document.documentElement.classList.add('dark');
        localStorage.setItem(this.darkModeKey, 'true');
        this.updateToggleButton(true);
    }

    disableDarkMode() {
        document.documentElement.classList.remove('dark');
        localStorage.setItem(this.darkModeKey, 'false');
        this.updateToggleButton(false);
    }

    toggle() {
        if (document.documentElement.classList.contains('dark')) {
            this.disableDarkMode();
        } else {
            this.enableDarkMode();
        }
    }

    setupToggleButton() {
        const toggleButton = document.getElementById('dark-mode-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('click', () => this.toggle());
        }
    }

    updateToggleButton(isDark) {
        const toggleButton = document.getElementById('dark-mode-toggle');
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');
        
        if (toggleButton && sunIcon && moonIcon) {
            if (isDark) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
                toggleButton.setAttribute('aria-label', 'Ativar modo claro');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
                toggleButton.setAttribute('aria-label', 'Ativar modo escuro');
            }
        }
    }

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.darkModeManager = new DarkModeManager();
});

// Exportar para uso global
window.DarkModeManager = DarkModeManager;