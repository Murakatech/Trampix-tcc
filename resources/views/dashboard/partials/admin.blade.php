{{-- Seção ADMIN --}}
<section>
    @php
        $totalUsuarios = \App\Models\User::count();
        $totalEmpresas = \App\Models\Company::count();
        $totalFreelancers = \App\Models\Freelancer::count();
        $totalVagas = \App\Models\JobVacancy::count();
        $totalAplicacoes = \App\Models\Application::count();
        $vagasAtivas = \App\Models\JobVacancy::where('status', 'active')->count();
    @endphp

    <!-- Resumo Estatísticas do Sistema -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-users text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalUsuarios }}</h3>
            <p class="text-sm text-gray-500">Total de Usuários</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-building text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalEmpresas }}</h3>
            <p class="text-sm text-gray-500">Empresas Cadastradas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-user-tie text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalFreelancers }}</h3>
            <p class="text-sm text-gray-500">Freelancers Ativos</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-briefcase text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalVagas }}</h3>
            <p class="text-sm text-gray-500">Total de Vagas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-check-circle text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $vagasAtivas }}</h3>
            <p class="text-sm text-gray-500">Vagas Ativas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-gray-200 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-clipboard-list text-gray-800 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalAplicacoes }}</h3>
            <p class="text-sm text-gray-500">Total Aplicações</p>
        </div>
    </div>

    <!-- Ações Administrativas -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-cog text-gray-700 mr-2"></i> Área Administrativa
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="bg-gray-200 p-3 rounded-full mr-4 group-hover:bg-gray-300 transition-colors">
                        <i class="fas fa-users text-gray-800 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-black transition-colors">Freelancers</p>
                        <p class="text-sm text-gray-500">Gerenciar freelancers cadastrados</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.companies') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="bg-gray-200 p-3 rounded-full mr-4 group-hover:bg-gray-300 transition-colors">
                        <i class="fas fa-building text-gray-800 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-black transition-colors">Empresas</p>
                        <p class="text-sm text-gray-500">Gerenciar empresas cadastradas</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.applications') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="bg-gray-200 p-3 rounded-full mr-4 group-hover:bg-gray-300 transition-colors">
                        <i class="fas fa-clipboard-list text-gray-800 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-black transition-colors">Candidaturas</p>
                        <p class="text-sm text-gray-500">Monitorar todas candidaturas</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="bg-gray-200 p-3 rounded-full mr-4 group-hover:bg-gray-300 transition-colors">
                        <i class="fas fa-briefcase text-gray-800 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-black transition-colors">Todas as Vagas</p>
                        <p class="text-sm text-gray-500">Visualizar vagas do sistema</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="bg-gray-200 p-3 rounded-full mr-4 group-hover:bg-gray-300 transition-colors">
                        <i class="fas fa-user-cog text-gray-800 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-black transition-colors">Configurações</p>
                        <p class="text-sm text-gray-500">Gerenciar conta admin</p>
                    </div>
                </div>
            </a>

            <a href="#" class="trampix-card hover:scale-105 transition-all duration-300 group opacity-75">
                <div class="flex items-center">
                    <div class="bg-gray-100 p-3 rounded-full mr-4 group-hover:bg-gray-200 transition-colors">
                        <i class="fas fa-chart-bar text-gray-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-gray-600 transition-colors">Relatórios</p>
                        <p class="text-sm text-gray-500">Em breve</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Atividade Recente -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-clock text-gray-700 mr-2"></i> Atividade Recente
        </h3>
        <div class="space-y-4">
            @php
                $recentApplications = \App\Models\Application::with(['freelancer.user', 'jobVacancy'])
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp
            
            @forelse($recentApplications as $application)
            <div class="trampix-card">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">
                            Nova aplicação para "{{ $application->jobVacancy->title }}"
                        </h4>
                        <p class="text-sm text-gray-500">
                            Por {{ $application->freelancer->user->name ?? 'Freelancer' }}
                        </p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-800">
                                @switch($application->status)
                                    @case('pending') Pendente @break
                                    @case('accepted') Aceita @break
                                    @case('rejected') Rejeitada @break
                                    @default {{ ucfirst($application->status) }}
                                @endswitch
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $application->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('admin.applications') }}" class="btn-trampix-secondary text-sm">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500">Nenhuma atividade recente</p>
            </div>
            @endforelse
        </div>
    </div>
</section>