/**
 * Testes para o ProfileUpdater
 * @jest-environment jsdom
 */

// Mock do fetch
global.fetch = jest.fn();

// Mock do console para evitar logs durante os testes
global.console = {
    ...console,
    log: jest.fn(),
    warn: jest.fn(),
    error: jest.fn()
};

// Mock do localStorage
const localStorageMock = {
    getItem: jest.fn(),
    setItem: jest.fn(),
    removeItem: jest.fn(),
    clear: jest.fn()
};
global.localStorage = localStorageMock;

describe('ProfileUpdater', () => {
    let ProfileUpdater;
    let profileUpdater;
    let mockDocument;

    beforeEach(() => {
        // Reset mocks
        fetch.mockClear();
        localStorageMock.getItem.mockClear();
        localStorageMock.setItem.mockClear();
        
        // Setup DOM
        document.body.innerHTML = `
            <meta name="csrf-token" content="test-token">
            <meta name="user-authenticated" content="true">
            <meta name="app-debug" content="false">
            <div class="trampix-profile-btn">
                <div class="trampix-avatar">
                    <img class="trampix-avatar-img" src="/default-avatar.jpg" alt="Profile">
                    <div class="trampix-avatar-placeholder">JD</div>
                </div>
                <div class="trampix-user-info">
                    <div class="trampix-user-name">John Doe</div>
                    <div class="trampix-user-role">Freelancer</div>
                </div>
            </div>
        `;

        // Importar a classe (simulando o import)
        ProfileUpdater = class {
            constructor() {
                this.profileButton = document.querySelector('.trampix-profile-btn');
                this.avatar = document.querySelector('.trampix-avatar');
                this.avatarImg = document.querySelector('.trampix-avatar-img');
                this.avatarPlaceholder = document.querySelector('.trampix-avatar-placeholder');
                this.userName = document.querySelector('.trampix-user-name');
                this.userRole = document.querySelector('.trampix-user-role');
                
                this.isPolling = false;
                this.pollingInterval = null;
                this.lastModified = null;
                this.cache = new Map();
                this.retryCount = 0;
                this.maxRetries = 3;
                this.pollingFrequency = 30000; // 30 segundos
                this.maxRequestsPerMinute = 10;
                this.requestCount = 0;
                this.requestWindow = Date.now();
                
                this.init();
            }

            init() {
                if (!this.profileButton) {
                    console.warn('ProfileUpdater: Botão de perfil não encontrado');
                    return;
                }
                
                this.loadFromCache();
                this.startPolling();
                this.setupEventListeners();
            }

            async checkForUpdates() {
                if (!this.canMakeRequest()) {
                    return;
                }

                try {
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    };

                    if (this.lastModified) {
                        headers['If-Modified-Since'] = this.lastModified;
                    }

                    const response = await fetch('/api/profile/check-updates', {
                        method: 'GET',
                        headers: headers,
                        credentials: 'same-origin'
                    });

                    this.incrementRequestCount();

                    if (response.status === 304) {
                        // Não há atualizações
                        return;
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data.has_updates) {
                        await this.updateProfile();
                    }

                    this.lastModified = data.last_modified;
                    this.retryCount = 0;

                } catch (error) {
                    this.handleError(error);
                }
            }

            async updateProfile() {
                try {
                    const response = await fetch('/api/profile/data', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const data = await response.json();
                    this.updateUI(data.user);
                    this.saveToCache(data.user);

                } catch (error) {
                    this.handleError(error);
                }
            }

            updateUI(userData) {
                // Atualizar avatar
                if (userData.profile_photo_url) {
                    this.avatarImg.src = userData.profile_photo_url;
                    this.avatarImg.style.display = 'block';
                    this.avatarPlaceholder.style.display = 'none';
                } else {
                    this.avatarImg.style.display = 'none';
                    this.avatarPlaceholder.style.display = 'flex';
                    this.avatarPlaceholder.textContent = userData.initials || '?';
                }

                // Atualizar informações do usuário
                if (this.userName) {
                    this.userName.textContent = userData.name;
                }
                
                if (this.userRole) {
                    const roleText = this.getRoleText(userData.role);
                    this.userRole.textContent = roleText;
                }
            }

            getRoleText(role) {
                const roleMap = {
                    'freelancer': 'Freelancer',
                    'company': 'Empresa',
                    'admin': 'Administrador'
                };
                return roleMap[role] || 'Usuário';
            }

            canMakeRequest() {
                const now = Date.now();
                
                // Reset contador se passou 1 minuto
                if (now - this.requestWindow > 60000) {
                    this.requestCount = 0;
                    this.requestWindow = now;
                }
                
                return this.requestCount < this.maxRequestsPerMinute;
            }

            incrementRequestCount() {
                this.requestCount++;
            }

            handleError(error) {
                console.error('ProfileUpdater: Erro ao atualizar perfil:', error);
                
                this.retryCount++;
                
                if (this.retryCount >= this.maxRetries) {
                    this.stopPolling();
                    console.warn('ProfileUpdater: Máximo de tentativas atingido. Polling interrompido.');
                }
            }

            startPolling() {
                if (this.isPolling) return;
                
                this.isPolling = true;
                this.pollingInterval = setInterval(() => {
                    this.checkForUpdates();
                }, this.pollingFrequency);
            }

            stopPolling() {
                if (this.pollingInterval) {
                    clearInterval(this.pollingInterval);
                    this.pollingInterval = null;
                }
                this.isPolling = false;
            }

            loadFromCache() {
                try {
                    const cached = localStorage.getItem('trampix_profile_cache');
                    if (cached) {
                        const data = JSON.parse(cached);
                        this.updateUI(data);
                    }
                } catch (error) {
                    console.warn('ProfileUpdater: Erro ao carregar cache:', error);
                }
            }

            saveToCache(userData) {
                try {
                    localStorage.setItem('trampix_profile_cache', JSON.stringify(userData));
                } catch (error) {
                    console.warn('ProfileUpdater: Erro ao salvar cache:', error);
                }
            }

            setupEventListeners() {
                // Pausar polling quando a aba não está ativa
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.stopPolling();
                    } else {
                        this.startPolling();
                        this.checkForUpdates(); // Verificar imediatamente ao voltar
                    }
                });
            }
        };

        profileUpdater = new ProfileUpdater();
    });

    afterEach(() => {
        if (profileUpdater && profileUpdater.pollingInterval) {
            clearInterval(profileUpdater.pollingInterval);
        }
        document.body.innerHTML = '';
    });

    describe('Inicialização', () => {
        test('deve inicializar corretamente com elementos DOM presentes', () => {
            expect(profileUpdater.profileButton).toBeTruthy();
            expect(profileUpdater.avatar).toBeTruthy();
            expect(profileUpdater.avatarImg).toBeTruthy();
            expect(profileUpdater.isPolling).toBe(true);
        });

        test('deve configurar valores padrão corretos', () => {
            expect(profileUpdater.pollingFrequency).toBe(30000);
            expect(profileUpdater.maxRetries).toBe(3);
            expect(profileUpdater.maxRequestsPerMinute).toBe(10);
            expect(profileUpdater.retryCount).toBe(0);
        });
    });

    describe('Verificação de atualizações', () => {
        test('deve fazer requisição para check-updates', async () => {
            fetch.mockResolvedValueOnce({
                ok: true,
                status: 200,
                json: async () => ({
                    has_updates: false,
                    last_modified: 'Wed, 01 Jan 2025 12:00:00 GMT'
                })
            });

            await profileUpdater.checkForUpdates();

            expect(fetch).toHaveBeenCalledWith('/api/profile/check-updates', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': 'test-token'
                },
                credentials: 'same-origin'
            });
        });

        test('deve incluir header If-Modified-Since quando disponível', async () => {
            profileUpdater.lastModified = 'Wed, 01 Jan 2025 11:00:00 GMT';
            
            fetch.mockResolvedValueOnce({
                ok: true,
                status: 304
            });

            await profileUpdater.checkForUpdates();

            expect(fetch).toHaveBeenCalledWith('/api/profile/check-updates', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': 'test-token',
                    'If-Modified-Since': 'Wed, 01 Jan 2025 11:00:00 GMT'
                },
                credentials: 'same-origin'
            });
        });

        test('deve atualizar perfil quando há atualizações', async () => {
            fetch
                .mockResolvedValueOnce({
                    ok: true,
                    status: 200,
                    json: async () => ({
                        has_updates: true,
                        last_modified: 'Wed, 01 Jan 2025 12:00:00 GMT'
                    })
                })
                .mockResolvedValueOnce({
                    ok: true,
                    status: 200,
                    json: async () => ({
                        user: {
                            name: 'Jane Doe',
                            role: 'company',
                            profile_photo_url: '/new-photo.jpg',
                            initials: 'JD'
                        }
                    })
                });

            await profileUpdater.checkForUpdates();

            expect(fetch).toHaveBeenCalledTimes(2);
            expect(profileUpdater.avatarImg.src).toContain('/new-photo.jpg');
            expect(profileUpdater.userName.textContent).toBe('Jane Doe');
        });
    });

    describe('Atualização da UI', () => {
        test('deve atualizar avatar com foto', () => {
            const userData = {
                name: 'Test User',
                role: 'freelancer',
                profile_photo_url: '/test-photo.jpg',
                initials: 'TU'
            };

            profileUpdater.updateUI(userData);

            expect(profileUpdater.avatarImg.src).toContain('/test-photo.jpg');
            expect(profileUpdater.avatarImg.style.display).toBe('block');
            expect(profileUpdater.avatarPlaceholder.style.display).toBe('none');
        });

        test('deve mostrar iniciais quando não há foto', () => {
            const userData = {
                name: 'Test User',
                role: 'freelancer',
                profile_photo_url: null,
                initials: 'TU'
            };

            profileUpdater.updateUI(userData);

            expect(profileUpdater.avatarImg.style.display).toBe('none');
            expect(profileUpdater.avatarPlaceholder.style.display).toBe('flex');
            expect(profileUpdater.avatarPlaceholder.textContent).toBe('TU');
        });

        test('deve atualizar informações do usuário', () => {
            const userData = {
                name: 'Maria Silva',
                role: 'company',
                profile_photo_url: null,
                initials: 'MS'
            };

            profileUpdater.updateUI(userData);

            expect(profileUpdater.userName.textContent).toBe('Maria Silva');
            expect(profileUpdater.userRole.textContent).toBe('Empresa');
        });
    });

    describe('Controle de taxa de requisições', () => {
        test('deve respeitar limite de requisições por minuto', () => {
            // Simular 10 requisições
            for (let i = 0; i < 10; i++) {
                profileUpdater.incrementRequestCount();
            }

            expect(profileUpdater.canMakeRequest()).toBe(false);
        });

        test('deve resetar contador após 1 minuto', () => {
            // Simular 10 requisições
            for (let i = 0; i < 10; i++) {
                profileUpdater.incrementRequestCount();
            }

            // Simular passagem de tempo
            profileUpdater.requestWindow = Date.now() - 61000;

            expect(profileUpdater.canMakeRequest()).toBe(true);
        });
    });

    describe('Tratamento de erros', () => {
        test('deve incrementar contador de retry em caso de erro', async () => {
            fetch.mockRejectedValueOnce(new Error('Network error'));

            await profileUpdater.checkForUpdates();

            expect(profileUpdater.retryCount).toBe(1);
        });

        test('deve parar polling após máximo de tentativas', async () => {
            profileUpdater.retryCount = 2; // Já tem 2 tentativas

            fetch.mockRejectedValueOnce(new Error('Network error'));

            await profileUpdater.checkForUpdates();

            expect(profileUpdater.isPolling).toBe(false);
            expect(profileUpdater.pollingInterval).toBeNull();
        });
    });

    describe('Cache', () => {
        test('deve salvar dados no localStorage', () => {
            const userData = {
                name: 'Test User',
                role: 'freelancer',
                profile_photo_url: '/test.jpg',
                initials: 'TU'
            };

            profileUpdater.saveToCache(userData);

            expect(localStorageMock.setItem).toHaveBeenCalledWith(
                'trampix_profile_cache',
                JSON.stringify(userData)
            );
        });

        test('deve carregar dados do localStorage', () => {
            const userData = {
                name: 'Cached User',
                role: 'company',
                profile_photo_url: '/cached.jpg',
                initials: 'CU'
            };

            localStorageMock.getItem.mockReturnValueOnce(JSON.stringify(userData));

            profileUpdater.loadFromCache();

            expect(profileUpdater.userName.textContent).toBe('Cached User');
        });
    });

    describe('Polling', () => {
        test('deve iniciar polling automaticamente', () => {
            expect(profileUpdater.isPolling).toBe(true);
            expect(profileUpdater.pollingInterval).toBeTruthy();
        });

        test('deve parar polling quando solicitado', () => {
            profileUpdater.stopPolling();

            expect(profileUpdater.isPolling).toBe(false);
            expect(profileUpdater.pollingInterval).toBeNull();
        });
    });
});