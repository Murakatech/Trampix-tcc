/**
 * Sistema de Atualização Dinâmica de Perfil - Trampix
 * 
 * Implementa polling inteligente para verificar mudanças na foto de perfil
 * e atualizar a navbar/sidebar em tempo real sem recarregar a página.
 * 
 * Funcionalidades:
 * - Polling com intervalo configurável (padrão: 30 segundos)
 * - Cache local para reduzir requisições desnecessárias
 * - Fallback para navegadores sem suporte a recursos modernos
 * - Tratamento robusto de erros e reconexão automática
 * - Otimização de performance com throttling
 * - Suporte responsivo para diferentes dispositivos
 */

class ProfileUpdater {
    constructor(options = {}) {
        // Configurações padrão
        this.config = {
            pollInterval: options.pollInterval || 30000, // 30 segundos
            maxRequestsPerMinute: options.maxRequestsPerMinute || 10,
            retryAttempts: options.retryAttempts || 3,
            retryDelay: options.retryDelay || 5000,
            cacheTimeout: options.cacheTimeout || 300000, // 5 minutos
            enableWebSocket: options.enableWebSocket || false,
            debug: options.debug || false,
            ...options
        };

        // Estado interno
        this.state = {
            isPolling: false,
            lastModified: null,
            requestCount: 0,
            requestWindow: Date.now(),
            retryCount: 0,
            cache: new Map(),
            connectionStatus: 'disconnected'
        };

        // Elementos DOM
        this.elements = {
            navbarAvatar: null,
            sidebarAvatar: null,
            dropdownAvatar: null,
            userInfo: null
        };

        // Bind methods
        this.handleVisibilityChange = this.handleVisibilityChange.bind(this);
        this.handleOnline = this.handleOnline.bind(this);
        this.handleOffline = this.handleOffline.bind(this);

        this.init();
    }

    /**
     * Inicializa o sistema de atualização
     */
    async init() {
        try {
            this.log('Inicializando ProfileUpdater...');
            
            // Encontrar elementos DOM
            this.findDOMElements();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            // Verificar suporte a recursos
            this.checkBrowserSupport();
            
            // Carregar dados iniciais
            await this.loadInitialData();
            
            // Iniciar polling
            this.startPolling();
            
            this.log('ProfileUpdater inicializado com sucesso');
            
        } catch (error) {
            this.handleError('Erro na inicialização', error);
        }
    }

    /**
     * Encontra elementos DOM relevantes
     */
    findDOMElements() {
        // Navbar avatars
        this.elements.navbarAvatar = document.querySelector('.trampix-avatar-img, .trampix-avatar-placeholder');
        
        // Sidebar avatars
        this.elements.sidebarAvatar = document.querySelector('aside .trampix-avatar-img, aside .trampix-avatar-placeholder, aside img[alt="Avatar"], aside .avatar-placeholder');
        
        // Dropdown avatars
        this.elements.dropdownAvatar = document.querySelector('.trampix-dropdown-avatar, .trampix-dropdown-avatar-placeholder');
        
        // User info elements
        this.elements.userInfo = {
            name: document.querySelector('.trampix-user-name, .trampix-dropdown-name'),
            role: document.querySelector('.trampix-user-role, .trampix-dropdown-role'),
            email: document.querySelector('.trampix-dropdown-email')
        };

        this.log('Elementos DOM encontrados:', this.elements);
    }

    /**
     * Configura event listeners
     */
    setupEventListeners() {
        // Visibilidade da página
        document.addEventListener('visibilitychange', this.handleVisibilityChange);
        
        // Status de conexão
        window.addEventListener('online', this.handleOnline);
        window.addEventListener('offline', this.handleOffline);
        
        // Resize para responsividade
        window.addEventListener('resize', this.debounce(() => {
            this.updateResponsiveElements();
        }, 250));

        // Eventos customizados
        document.addEventListener('profile-photo-updated', (event) => {
            this.handleProfilePhotoUpdate(event.detail);
        });
    }

    /**
     * Verifica suporte do navegador
     */
    checkBrowserSupport() {
        const support = {
            fetch: typeof fetch !== 'undefined',
            promise: typeof Promise !== 'undefined',
            localStorage: this.isLocalStorageAvailable(),
            intersectionObserver: 'IntersectionObserver' in window,
            webSocket: 'WebSocket' in window
        };

        this.browserSupport = support;
        this.log('Suporte do navegador:', support);

        // Ajustar configurações baseado no suporte
        if (!support.fetch) {
            this.config.enableWebSocket = false;
            this.log('Fetch API não suportada, usando XMLHttpRequest');
        }
    }

    /**
     * Carrega dados iniciais do perfil
     */
    async loadInitialData() {
        try {
            const data = await this.fetchProfileData();
            if (data.success) {
                this.updateProfileElements(data.data);
                this.state.lastModified = data.timestamp || Date.now();
            }
        } catch (error) {
            this.handleError('Erro ao carregar dados iniciais', error);
        }
    }

    /**
     * Inicia o sistema de polling
     */
    startPolling() {
        if (this.state.isPolling) {
            this.log('Polling já está ativo');
            return;
        }

        this.state.isPolling = true;
        this.state.connectionStatus = 'connected';
        this.scheduleNextPoll();
        
        this.log(`Polling iniciado com intervalo de ${this.config.pollInterval}ms`);
    }

    /**
     * Para o sistema de polling
     */
    stopPolling() {
        if (!this.state.isPolling) {
            return;
        }

        this.state.isPolling = false;
        this.state.connectionStatus = 'disconnected';
        
        if (this.pollTimeout) {
            clearTimeout(this.pollTimeout);
            this.pollTimeout = null;
        }
        
        this.log('Polling parado');
    }

    /**
     * Agenda próxima verificação
     */
    scheduleNextPoll() {
        if (!this.state.isPolling) {
            return;
        }

        // Verificar rate limiting
        if (!this.canMakeRequest()) {
            this.log('Rate limit atingido, aguardando...');
            this.pollTimeout = setTimeout(() => this.scheduleNextPoll(), 60000);
            return;
        }

        const interval = this.getAdaptiveInterval();
        
        this.pollTimeout = setTimeout(async () => {
            try {
                await this.checkForUpdates();
                this.scheduleNextPoll();
            } catch (error) {
                this.handlePollingError(error);
            }
        }, interval);
    }

    /**
     * Verifica se pode fazer requisição (rate limiting)
     */
    canMakeRequest() {
        const now = Date.now();
        const windowDuration = 60000; // 1 minuto
        
        // Reset contador se passou da janela de tempo
        if (now - this.state.requestWindow > windowDuration) {
            this.state.requestCount = 0;
            this.state.requestWindow = now;
        }
        
        return this.state.requestCount < this.config.maxRequestsPerMinute;
    }

    /**
     * Calcula intervalo adaptivo baseado na atividade
     */
    getAdaptiveInterval() {
        let interval = this.config.pollInterval;
        
        // Aumentar intervalo se página não está visível
        if (document.hidden) {
            interval *= 3;
        }
        
        // Aumentar intervalo se houve erros recentes
        if (this.state.retryCount > 0) {
            interval *= (1 + this.state.retryCount * 0.5);
        }
        
        return Math.min(interval, 300000); // Máximo 5 minutos
    }

    /**
     * Verifica atualizações no servidor
     */
    async checkForUpdates() {
        try {
            this.state.requestCount++;
            
            const headers = {};
            if (this.state.lastModified) {
                headers['If-Modified-Since'] = new Date(this.state.lastModified).toUTCString();
            }

            const response = await this.makeRequest('/api/profile/check-updates', {
                method: 'GET',
                headers
            });

            if (response.status === 304) {
                // Não modificado
                this.log('Nenhuma atualização detectada');
                this.state.retryCount = 0;
                return;
            }

            const data = await response.json();
            
            if (data.success && data.changed) {
                this.log('Atualização detectada, atualizando interface...');
                this.updateProfileElements(data.data);
                this.state.lastModified = data.timestamp;
                this.state.retryCount = 0;
                
                // Disparar evento customizado
                this.dispatchUpdateEvent(data.data);
            }

        } catch (error) {
            throw error;
        }
    }

    /**
     * Busca dados completos do perfil
     */
    async fetchProfileData() {
        const response = await this.makeRequest('/api/profile/data', {
            method: 'GET'
        });
        
        return await response.json();
    }

    /**
     * Faz requisição HTTP com fallbacks
     */
    async makeRequest(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            credentials: 'same-origin'
        };

        const finalOptions = { ...defaultOptions, ...options };
        finalOptions.headers = { ...defaultOptions.headers, ...options.headers };

        if (this.browserSupport.fetch) {
            return await fetch(url, finalOptions);
        } else {
            // Fallback para XMLHttpRequest
            return await this.makeXHRRequest(url, finalOptions);
        }
    }

    /**
     * Fallback XMLHttpRequest para navegadores antigos
     */
    makeXHRRequest(url, options) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open(options.method || 'GET', url);
            
            // Configurar headers
            Object.entries(options.headers || {}).forEach(([key, value]) => {
                if (value) xhr.setRequestHeader(key, value);
            });
            
            xhr.onload = () => {
                const response = {
                    status: xhr.status,
                    json: () => Promise.resolve(JSON.parse(xhr.responseText)),
                    text: () => Promise.resolve(xhr.responseText)
                };
                resolve(response);
            };
            
            xhr.onerror = () => reject(new Error('Erro de rede'));
            xhr.send(options.body);
        });
    }

    /**
     * Atualiza elementos da interface com novos dados
     */
    updateProfileElements(data) {
        try {
            // Atualizar avatars
            this.updateAvatarElements(data);
            
            // Atualizar informações do usuário
            this.updateUserInfo(data);
            
            // Atualizar menu se necessário
            this.updateMenuItems(data);
            
            // Aplicar responsividade
            this.updateResponsiveElements();
            
            this.log('Interface atualizada com sucesso');
            
        } catch (error) {
            this.handleError('Erro ao atualizar interface', error);
        }
    }

    /**
     * Atualiza elementos de avatar
     */
    updateAvatarElements(data) {
        const avatarElements = [
            this.elements.navbarAvatar,
            this.elements.sidebarAvatar,
            this.elements.dropdownAvatar
        ].filter(Boolean);

        avatarElements.forEach(element => {
            if (data.has_photo && data.photo_url) {
                // Tem foto - criar/atualizar img
                if (element.tagName === 'IMG') {
                    element.src = this.getCacheBustedUrl(data.photo_url);
                    element.alt = `Foto de perfil de ${data.display_name}`;
                    // garantir classe padrão
                    if (!element.classList.contains('trampix-avatar-img')) {
                        element.classList.add('trampix-avatar-img');
                    }
                } else {
                    // Converter placeholder para img
                    const img = document.createElement('img');
                    img.src = this.getCacheBustedUrl(data.photo_url);
                    img.alt = `Foto de perfil de ${data.display_name}`;
                    img.className = (element.className || '').replace('placeholder', 'img');
                    img.classList.add('trampix-avatar-img');
                    element.parentNode.replaceChild(img, element);
                }
            } else {
                // Sem foto - usar placeholder
                if (element.tagName === 'IMG') {
                    // Converter img para placeholder
                    const placeholder = document.createElement('div');
                    placeholder.className = (element.className || '').replace('img', 'placeholder');
                    placeholder.classList.add('trampix-avatar-placeholder');
                    placeholder.textContent = data.initials;
                    element.parentNode.replaceChild(placeholder, element);
                } else {
                    // Atualizar placeholder existente
                    element.textContent = data.initials;
                    if (!element.classList.contains('trampix-avatar-placeholder')) {
                        element.classList.add('trampix-avatar-placeholder');
                    }
                }
            }
        });
    }

    /**
     * Atualiza informações do usuário
     */
    updateUserInfo(data) {
        if (this.elements.userInfo.name) {
            this.elements.userInfo.name.textContent = data.display_name;
        }
        
        if (this.elements.userInfo.role) {
            const role = data.active_role || data.role;
            this.elements.userInfo.role.textContent = this.formatRole(role);
        }
        
        if (this.elements.userInfo.email) {
            this.elements.userInfo.email.textContent = data.email;
        }
    }

    /**
     * Atualiza itens do menu baseado no perfil
     */
    updateMenuItems(data) {
        if (!data.menu_items) return;

        // Implementar atualização dinâmica do menu
        // Esta funcionalidade pode ser expandida conforme necessário
        this.log('Menu items:', data.menu_items);
    }

    /**
     * Gera URL com cache busting baseado em Last-Modified
     */
    getCacheBustedUrl(url) {
        try {
            const u = new URL(url, window.location.origin);
            const ts = this.state?.lastModified || Date.now();
            u.searchParams.set('_ts', ts);
            return u.toString();
        } catch (e) {
            // Fallback seguro se URL for relativa simples
            const sep = url.includes('?') ? '&' : '?';
            const ts = this.state?.lastModified || Date.now();
            return `${url}${sep}_ts=${encodeURIComponent(ts)}`;
        }
    }

    /**
     * Aplica ajustes responsivos
     */
    updateResponsiveElements() {
        const breakpoints = {
            mobile: window.innerWidth < 768,
            tablet: window.innerWidth >= 768 && window.innerWidth < 1024,
            desktop: window.innerWidth >= 1024
        };

        // Ajustar tamanhos de avatar baseado no breakpoint
        const avatarElements = document.querySelectorAll('.trampix-avatar-img, .trampix-avatar-placeholder');
        
        avatarElements.forEach(element => {
            if (breakpoints.mobile) {
                element.style.width = '36px';
                element.style.height = '36px';
            } else if (breakpoints.tablet) {
                element.style.width = '40px';
                element.style.height = '40px';
            } else {
                element.style.width = '44px';
                element.style.height = '44px';
            }
        });
    }

    /**
     * Manipula mudanças de visibilidade da página
     */
    handleVisibilityChange() {
        if (document.hidden) {
            this.log('Página oculta, reduzindo frequência de polling');
        } else {
            this.log('Página visível, retomando polling normal');
            // Verificar imediatamente quando página volta a ficar visível
            if (this.state.isPolling) {
                this.checkForUpdates().catch(error => {
                    this.handleError('Erro na verificação após visibilidade', error);
                });
            }
        }
    }

    /**
     * Manipula evento de conexão online
     */
    handleOnline() {
        this.log('Conexão restaurada');
        this.state.connectionStatus = 'connected';
        this.state.retryCount = 0;
        
        if (!this.state.isPolling) {
            this.startPolling();
        }
    }

    /**
     * Manipula evento de conexão offline
     */
    handleOffline() {
        this.log('Conexão perdida');
        this.state.connectionStatus = 'offline';
        this.stopPolling();
    }

    /**
     * Manipula erros de polling
     */
    handlePollingError(error) {
        this.state.retryCount++;
        
        if (this.state.retryCount <= this.config.retryAttempts) {
            this.log(`Erro no polling (tentativa ${this.state.retryCount}/${this.config.retryAttempts}):`, error);
            
            // Reagendar com delay
            setTimeout(() => {
                this.scheduleNextPoll();
            }, this.config.retryDelay * this.state.retryCount);
        } else {
            this.handleError('Muitas tentativas de polling falharam', error);
            this.stopPolling();
            
            // Tentar reconectar após um tempo maior
            setTimeout(() => {
                this.state.retryCount = 0;
                this.startPolling();
            }, 60000); // 1 minuto
        }
    }

    /**
     * Manipula atualização manual da foto de perfil
     */
    handleProfilePhotoUpdate(data) {
        this.log('Atualização manual de foto detectada:', data);
        this.updateProfileElements(data);
        this.state.lastModified = Date.now();
    }

    /**
     * Dispara evento customizado de atualização
     */
    dispatchUpdateEvent(data) {
        const event = new CustomEvent('profile-updated', {
            detail: data,
            bubbles: true
        });
        document.dispatchEvent(event);
    }

    /**
     * Formata papel do usuário para exibição
     */
    formatRole(role) {
        const roleMap = {
            'freelancer': 'Freelancer',
            'company': 'Empresa',
            'admin': 'Administrador'
        };
        return roleMap[role] || role;
    }

    /**
     * Verifica disponibilidade do localStorage
     */
    isLocalStorageAvailable() {
        try {
            const test = '__localStorage_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Debounce function
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Manipula erros gerais
     */
    handleError(message, error) {
        console.error(`[ProfileUpdater] ${message}:`, error);
        
        // Disparar evento de erro para monitoramento
        const errorEvent = new CustomEvent('profile-updater-error', {
            detail: { message, error },
            bubbles: true
        });
        document.dispatchEvent(errorEvent);
    }

    /**
     * Log de debug
     */
    log(...args) {
        if (this.config.debug) {
            console.log('[ProfileUpdater]', ...args);
        }
    }

    /**
     * Destrói a instância e limpa recursos
     */
    destroy() {
        this.stopPolling();
        
        // Remover event listeners
        document.removeEventListener('visibilitychange', this.handleVisibilityChange);
        window.removeEventListener('online', this.handleOnline);
        window.removeEventListener('offline', this.handleOffline);
        
        // Limpar cache
        this.state.cache.clear();
        
        this.log('ProfileUpdater destruído');
    }
}

// Inicialização automática quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se usuário está autenticado
    if (document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'true') {
        window.profileUpdater = new ProfileUpdater({
            debug: document.querySelector('meta[name="app-debug"]')?.getAttribute('content') === 'true',
            pollInterval: 30000, // 30 segundos
            maxRequestsPerMinute: 10
        });
    }
});

// Exportar para uso em módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProfileUpdater;
}