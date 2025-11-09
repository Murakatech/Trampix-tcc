/**
 * Sistema de Atualização Dinâmica do Sidebar
 * Gerencia as opções do sidebar baseado no perfil do usuário
 */

class SidebarUpdater {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.nav = this.sidebar?.querySelector('nav');
        this.currentUserRole = null;
        this.currentUserPermissions = [];
        this.menuItems = new Map();
        
        this.init();
    }

    init() {
        if (!this.sidebar || !this.nav) {
            console.warn('SidebarUpdater: Sidebar não encontrado');
            return;
        }

        this.loadUserData();
        this.setupEventListeners();
        this.updateSidebarOptions();
        
        console.log('SidebarUpdater: Sistema inicializado');
    }

    /**
     * Carrega dados do usuário atual
     */
    loadUserData() {
        try {
            // Buscar dados do usuário do meta tag ou localStorage
            const userDataMeta = document.querySelector('meta[name="user-data"]');
            if (userDataMeta) {
                const userData = JSON.parse(userDataMeta.content);
                this.currentUserRole = userData.role;
                this.currentUserPermissions = userData.permissions || [];
            } else {
                // Fallback: detectar role pelos elementos existentes
                this.detectUserRoleFromDOM();
            }
        } catch (error) {
            console.warn('SidebarUpdater: Erro ao carregar dados do usuário:', error);
            this.detectUserRoleFromDOM();
        }
    }

    /**
     * Detecta o role do usuário baseado nos elementos DOM existentes
     */
    detectUserRoleFromDOM() {
        const links = this.nav.querySelectorAll('a[href]');
        
        // Verificar se há links específicos de cada role
        const hasFreelancerLinks = Array.from(links).some(link => 
            link.href.includes('applications.index') || 
            link.textContent.includes('Minhas Candidaturas')
        );
        
        const hasCompanyLinks = Array.from(links).some(link => 
            link.href.includes('company.vagas') || 
            link.textContent.includes('Minhas Vagas')
        );
        
        const hasAdminLinks = Array.from(links).some(link => 
            link.href.includes('admin.') || 
            link.textContent.includes('Freelancers')
        );

        if (hasAdminLinks) {
            this.currentUserRole = 'admin';
        } else if (hasCompanyLinks) {
            this.currentUserRole = 'company';
        } else if (hasFreelancerLinks) {
            this.currentUserRole = 'freelancer';
        } else {
            this.currentUserRole = 'guest';
        }
    }

    /**
     * Define os itens de menu para cada tipo de usuário
     */
    getMenuItemsForRole(role) {
        const baseItems = [
            {
                id: 'dashboard',
                href: '/dashboard',
                icon: 'fa-solid fa-house',
                text: 'Dashboard',
                permission: 'dashboard.view',
                priority: 1
            }
        ];

        const roleSpecificItems = {
            freelancer: [
                {
                    id: 'search-jobs',
                    href: '/vagas',
                    icon: 'fa-solid fa-magnifying-glass',
                    text: 'Buscar Vagas',
                    permission: 'jobs.search',
                    priority: 2
                },
                {
                    id: 'my-applications',
                    // Correção: rota correta é /my-applications (name: applications.index)
                    href: '/my-applications',
                    icon: 'fa-solid fa-file-lines',
                    text: 'Minhas Candidaturas',
                    permission: 'applications.view',
                    priority: 3
                },
                {
                    id: 'my-profile',
                    href: '/profile',
                    icon: 'fa-solid fa-user-edit',
                    text: 'Meu Perfil',
                    permission: 'profile.edit',
                    priority: 4
                }
            ],
            company: [
                {
                    id: 'my-jobs',
                    href: '/company/vagas',
                    icon: 'fa-solid fa-briefcase',
                    text: 'Minhas Vagas',
                    permission: 'company.jobs.manage',
                    priority: 2
                },
                {
                    id: 'manage-applications',
                    href: '/applications/manage',
                    icon: 'fa-solid fa-users',
                    text: 'Gerenciar Candidaturas',
                    permission: 'company.applications.manage',
                    priority: 3
                },
                {
                    id: 'company-profile',
                    href: '/profile',
                    icon: 'fa-solid fa-building-user',
                    text: 'Perfil da Empresa',
                    permission: 'company.profile.edit',
                    priority: 4
                }
            ],
            admin: [
                {
                    id: 'search-jobs',
                    href: '/vagas',
                    icon: 'fa-solid fa-magnifying-glass',
                    text: 'Buscar Vagas',
                    permission: 'jobs.search',
                    priority: 2
                },
                {
                    id: 'manage-freelancers',
                    href: '/admin/freelancers',
                    icon: 'fa-solid fa-id-card',
                    text: 'Freelancers',
                    permission: 'admin.freelancers.manage',
                    priority: 3
                },
                {
                    id: 'manage-companies',
                    href: '/admin/companies',
                    icon: 'fa-solid fa-building',
                    text: 'Empresas',
                    permission: 'admin.companies.manage',
                    priority: 4
                },
                {
                    id: 'manage-applications',
                    href: '/admin/applications',
                    icon: 'fa-solid fa-users',
                    text: 'Candidaturas',
                    permission: 'admin.applications.manage',
                    priority: 5
                },
                {
                    id: 'create-job',
                    href: '/admin/vagas/create',
                    icon: 'fa-solid fa-plus',
                    text: 'Criar Vaga',
                    permission: 'admin.jobs.create',
                    priority: 6
                },
                {
                    id: 'manage-jobs',
                    href: '/admin/vagas',
                    icon: 'fa-solid fa-briefcase',
                    text: 'Gerenciar Vagas',
                    permission: 'admin.jobs.manage',
                    priority: 7
                }
            ]
        };

        return [...baseItems, ...(roleSpecificItems[role] || [])];
    }

    /**
     * Verifica se o usuário tem permissão para um item
     */
    hasPermission(permission) {
        // Se não há sistema de permissões específico, permitir baseado no role
        if (this.currentUserPermissions.length === 0) {
            return true;
        }
        
        return this.currentUserPermissions.includes(permission);
    }

    /**
     * Cria elemento de menu
     */
    createMenuItem(item) {
        const isActive = window.location.pathname === item.href || 
                        window.location.pathname.startsWith(item.href + '/');
        
        const link = document.createElement('a');
        link.href = item.href;
        link.className = `flex items-center px-4 py-3 mx-2 rounded-lg text-gray-600 hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 ${isActive ? 'bg-purple-100 text-purple-600' : ''}`;
        link.setAttribute('data-menu-item', item.id);
        
        link.innerHTML = `
            <i class="${item.icon} text-lg flex-shrink-0"></i>
            <span 
                x-show="expanded"
                x-transition:enter="transition-opacity duration-300 ease-in-out delay-75"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-200 ease-in-out"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="ml-3 font-medium whitespace-nowrap overflow-hidden">${item.text}</span>
        `;

        return link;
    }

    /**
     * Atualiza as opções do sidebar
     */
    updateSidebarOptions() {
        if (!this.currentUserRole) {
            console.warn('SidebarUpdater: Role do usuário não definido');
            return;
        }

        const menuItems = this.getMenuItemsForRole(this.currentUserRole);
        const filteredItems = menuItems.filter(item => this.hasPermission(item.permission));
        
        // Encontrar container de navegação principal
        const existingItems = this.nav.querySelectorAll('a[data-menu-item]');
        existingItems.forEach(item => item.remove());

        // Encontrar ponto de inserção (antes da seção de desenvolvimento)
        const devSection = this.nav.querySelector('h3');
        const insertPoint = devSection ? devSection.parentElement : this.nav;

        // Adicionar novos itens
        filteredItems
            .sort((a, b) => a.priority - b.priority)
            .forEach(item => {
                const menuElement = this.createMenuItem(item);
                
                if (devSection) {
                    insertPoint.parentNode.insertBefore(menuElement, insertPoint);
                } else {
                    this.nav.appendChild(menuElement);
                }
            });

        console.log(`SidebarUpdater: Sidebar atualizado para role '${this.currentUserRole}' com ${filteredItems.length} itens`);
    }

    /**
     * Atualiza o role do usuário e recarrega o sidebar
     */
    updateUserRole(newRole, permissions = []) {
        this.currentUserRole = newRole;
        this.currentUserPermissions = permissions;
        this.updateSidebarOptions();
        
        console.log(`SidebarUpdater: Role atualizado para '${newRole}'`);
    }

    /**
     * Configura event listeners
     */
    setupEventListeners() {
        // Escutar mudanças de role via eventos customizados
        document.addEventListener('userRoleChanged', (event) => {
            const { role, permissions } = event.detail;
            this.updateUserRole(role, permissions);
        });

        // Escutar mudanças de rota para atualizar estados ativos
        window.addEventListener('popstate', () => {
            this.updateActiveStates();
        });

        // Interceptar cliques em links para atualizar estados
        this.nav.addEventListener('click', (event) => {
            if (event.target.closest('a[data-menu-item]')) {
                setTimeout(() => this.updateActiveStates(), 100);
            }
        });
    }

    /**
     * Atualiza estados ativos dos itens de menu
     */
    updateActiveStates() {
        const menuItems = this.nav.querySelectorAll('a[data-menu-item]');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            const isActive = window.location.pathname === href || 
                            window.location.pathname.startsWith(href + '/');
            
            if (isActive) {
                item.classList.add('bg-purple-100', 'text-purple-600');
                item.classList.remove('text-gray-600');
            } else {
                item.classList.remove('bg-purple-100', 'text-purple-600');
                item.classList.add('text-gray-600');
            }
        });
    }

    /**
     * Método público para forçar atualização
     */
    refresh() {
        this.loadUserData();
        this.updateSidebarOptions();
        this.updateActiveStates();
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('meta[name="user-authenticated"]')?.content === 'true') {
        window.sidebarUpdater = new SidebarUpdater();
    }
});

// Exportar para uso global
window.SidebarUpdater = SidebarUpdater;