# Componentes JavaScript - Sistema de Perfil Dinâmico

## ProfileUpdater

### Descrição
Classe responsável por gerenciar atualizações automáticas do perfil do usuário, implementando polling inteligente com otimizações de performance.

### Localização
`resources/js/profile-updater.js`

### Inicialização
```javascript
// Inicialização automática quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('[data-profile-updater]')) {
        new ProfileUpdater();
    }
});
```

### Métodos Principais

#### `constructor()`
Inicializa o ProfileUpdater com configurações padrão.

**Configurações:**
- `pollInterval`: 30000ms (30 segundos)
- `maxRequestsPerMinute`: 2
- `cacheKey`: 'trampix_profile_cache'

#### `init()`
Configura event listeners e inicia o sistema de polling.

```javascript
init() {
    this.setupEventListeners();
    this.startPolling();
    this.loadCachedData();
}
```

#### `checkForUpdates()`
Verifica se há atualizações no perfil do usuário.

**Retorno:** `Promise<boolean>`

```javascript
async checkForUpdates() {
    if (!this.canMakeRequest()) {
        return false;
    }

    try {
        const headers = this.buildHeaders();
        const response = await fetch('/api/profile/check-updates', { headers });
        
        if (response.status === 304) {
            return false; // Não modificado
        }
        
        if (response.ok) {
            const data = await response.json();
            this.updateLastModified(data.last_modified);
            return data.has_updates;
        }
    } catch (error) {
        this.handleError(error);
    }
    
    return false;
}
```

#### `updateProfile()`
Atualiza os dados do perfil na interface.

```javascript
async updateProfile() {
    try {
        const response = await fetch('/api/profile/data');
        const data = await response.json();
        
        this.updateUI(data.user);
        this.cacheData(data.user);
    } catch (error) {
        this.handleError(error);
    }
}
```

#### `updateUI(userData)`
Atualiza elementos da interface com os novos dados.

**Parâmetros:**
- `userData`: Objeto com dados do usuário

**Elementos atualizados:**
- Avatar/iniciais
- Nome do usuário
- Email
- Role
- Foto de perfil

### Rate Limiting

O sistema implementa rate limiting para evitar sobrecarga:

```javascript
canMakeRequest() {
    const now = Date.now();
    const oneMinute = 60 * 1000;
    
    // Remove requisições antigas
    this.requestTimes = this.requestTimes.filter(time => 
        now - time < oneMinute
    );
    
    // Verifica se pode fazer nova requisição
    if (this.requestTimes.length >= this.maxRequestsPerMinute) {
        return false;
    }
    
    this.requestTimes.push(now);
    return true;
}
```

### Cache Local

Utiliza localStorage para cache de dados:

```javascript
cacheData(userData) {
    const cacheData = {
        userData,
        timestamp: Date.now(),
        lastModified: this.lastModified
    };
    
    localStorage.setItem(this.cacheKey, JSON.stringify(cacheData));
}

loadCachedData() {
    try {
        const cached = localStorage.getItem(this.cacheKey);
        if (cached) {
            const data = JSON.parse(cached);
            this.updateUI(data.userData);
            this.lastModified = data.lastModified;
        }
    } catch (error) {
        console.warn('Erro ao carregar cache:', error);
    }
}
```

## SidebarUpdater

### Descrição
Classe responsável por atualizar dinamicamente as opções do sidebar baseado no role e permissões do usuário.

### Localização
`resources/js/sidebar-updater.js`

### Inicialização
```javascript
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.trampix-sidebar')) {
        new SidebarUpdater();
    }
});
```

### Estrutura de Dados

#### Menu Items por Role
```javascript
const menuItems = {
    freelancer: [
        {
            id: 'dashboard',
            title: 'Dashboard',
            url: '/dashboard',
            icon: 'fas fa-home',
            permission: null
        },
        {
            id: 'search-jobs',
            title: 'Buscar Vagas',
            url: '/vagas',
            icon: 'fas fa-search',
            permission: 'view_jobs'
        }
        // ... mais itens
    ],
    company: [
        {
            id: 'my-jobs',
            title: 'Minhas Vagas',
            url: '/empresa/vagas',
            icon: 'fas fa-briefcase',
            permission: 'manage_jobs'
        }
        // ... mais itens
    ],
    admin: [
        {
            id: 'admin-users',
            title: 'Gerenciar Usuários',
            url: '/admin/users',
            icon: 'fas fa-users',
            permission: 'admin_access'
        }
        // ... mais itens
    ]
};
```

### Métodos Principais

#### `loadUserData()`
Carrega dados do usuário a partir de meta tags ou DOM.

```javascript
loadUserData() {
    return {
        id: this.getMetaContent('user-id'),
        name: this.getMetaContent('user-name'),
        email: this.getMetaContent('user-email'),
        role: this.getMetaContent('user-role') || 'freelancer',
        permissions: this.getUserPermissions(),
        profilePhoto: this.getMetaContent('user-profile-photo')
    };
}
```

#### `updateSidebarOptions()`
Atualiza as opções do sidebar baseado no role do usuário.

```javascript
updateSidebarOptions() {
    const userRole = this.userData.role;
    const userMenuItems = this.menuItems[userRole] || this.menuItems.freelancer;
    
    const filteredItems = userMenuItems.filter(item => 
        this.hasPermission(item.permission)
    );
    
    this.renderMenuItems(filteredItems);
}
```

#### `hasPermission(permission)`
Verifica se o usuário tem uma permissão específica.

```javascript
hasPermission(permission) {
    if (!permission) return true;
    
    // Admin tem todas as permissões
    if (this.userData.role === 'admin') return true;
    
    // Verificar permissões específicas
    return this.userData.permissions.includes(permission);
}
```

#### `renderMenuItems(items)`
Renderiza os itens de menu no sidebar.

```javascript
renderMenuItems(items) {
    const container = document.querySelector('.trampix-sidebar-menu');
    if (!container) return;
    
    container.innerHTML = '';
    
    items.forEach(item => {
        const menuElement = this.createMenuElement(item);
        container.appendChild(menuElement);
    });
}
```

### Event Listeners

#### Role Change Detection
```javascript
setupEventListeners() {
    // Detectar mudanças de role
    document.addEventListener('roleChanged', (event) => {
        this.userData.role = event.detail.newRole;
        this.updateSidebarOptions();
    });
    
    // Detectar mudanças de rota
    window.addEventListener('popstate', () => {
        this.updateActiveMenuItem();
    });
}
```

### Responsividade

O sistema adapta-se automaticamente a diferentes tamanhos de tela:

```javascript
handleResponsive() {
    const isMobile = window.innerWidth < 768;
    const sidebar = document.querySelector('.trampix-sidebar');
    
    if (isMobile) {
        sidebar.classList.add('mobile-sidebar');
        this.setupMobileInteractions();
    } else {
        sidebar.classList.remove('mobile-sidebar');
        this.setupDesktopInteractions();
    }
}
```

## Utilitários Compartilhados

### Error Handling
```javascript
class ErrorHandler {
    static handle(error, context = '') {
        console.error(`[${context}] Erro:`, error);
        
        // Log para sistema de monitoramento
        if (window.analytics) {
            window.analytics.track('error', {
                message: error.message,
                context,
                timestamp: new Date().toISOString()
            });
        }
    }
}
```

### Debug Mode
```javascript
class DebugMode {
    static isEnabled() {
        return localStorage.getItem('profile-debug') === 'true';
    }
    
    static log(message, data = null) {
        if (this.isEnabled()) {
            console.log(`[DEBUG] ${message}`, data);
        }
    }
}
```

### Performance Monitor
```javascript
class PerformanceMonitor {
    static startTimer(label) {
        if (DebugMode.isEnabled()) {
            console.time(label);
        }
    }
    
    static endTimer(label) {
        if (DebugMode.isEnabled()) {
            console.timeEnd(label);
        }
    }
}
```

## Configuração Avançada

### Custom Events
```javascript
// Disparar evento personalizado
document.dispatchEvent(new CustomEvent('profileUpdated', {
    detail: { userData: newUserData }
}));

// Escutar evento personalizado
document.addEventListener('profileUpdated', (event) => {
    console.log('Perfil atualizado:', event.detail.userData);
});
```

### Configuração de Polling
```javascript
// Configurar intervalo personalizado
const updater = new ProfileUpdater({
    pollInterval: 60000, // 1 minuto
    maxRequestsPerMinute: 5,
    enableCache: true
});
```

### Hooks de Lifecycle
```javascript
class ProfileUpdater {
    constructor(options = {}) {
        this.hooks = {
            beforeUpdate: options.beforeUpdate || (() => {}),
            afterUpdate: options.afterUpdate || (() => {}),
            onError: options.onError || (() => {})
        };
    }
    
    async updateProfile() {
        this.hooks.beforeUpdate();
        
        try {
            // ... lógica de atualização
            this.hooks.afterUpdate(userData);
        } catch (error) {
            this.hooks.onError(error);
        }
    }
}
```

## Testes

### Estrutura de Testes
```javascript
// Mock para fetch
global.fetch = jest.fn();

// Mock para localStorage
const localStorageMock = {
    getItem: jest.fn(),
    setItem: jest.fn(),
    removeItem: jest.fn(),
    clear: jest.fn()
};
global.localStorage = localStorageMock;

// Teste de inicialização
describe('ProfileUpdater', () => {
    test('deve inicializar corretamente', () => {
        const updater = new ProfileUpdater();
        expect(updater.pollInterval).toBe(30000);
        expect(updater.maxRequestsPerMinute).toBe(2);
    });
});
```

## Melhores Práticas

### 1. Performance
- Use debouncing para eventos frequentes
- Implemente lazy loading para componentes pesados
- Cache dados sempre que possível

### 2. Acessibilidade
- Adicione atributos ARIA apropriados
- Suporte navegação por teclado
- Forneça feedback visual para ações

### 3. Manutenibilidade
- Mantenha funções pequenas e focadas
- Use nomes descritivos para variáveis e métodos
- Documente comportamentos complexos

### 4. Segurança
- Valide todos os inputs
- Sanitize dados antes de renderizar
- Use HTTPS para todas as requisições