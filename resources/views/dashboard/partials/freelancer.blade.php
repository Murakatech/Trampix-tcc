{{-- Seção FREELANCER --}}
<section>
    @php
        $freelancer = auth()->user()->freelancer;
        $totalAplicacoes = $freelancer ? $freelancer->applications()->count() : 0;
        $aplicacoesPendentes = $freelancer ? $freelancer->applications()->where('status', 'pending')->count() : 0;
        $aplicacoesAceitas = $freelancer ? $freelancer->applications()->where('status', 'accepted')->count() : 0;
        $vagasDisponiveis = \App\Models\JobVacancy::where('status', 'active')->count();
    @endphp

    <!-- Resumo Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="trampix-card text-center">
            <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-paper-plane text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalAplicacoes }}</h3>
            <p class="text-sm text-gray-500">Aplicações Enviadas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-orange-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-clock text-orange-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $aplicacoesPendentes }}</h3>
            <p class="text-sm text-gray-500">Em Análise</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $aplicacoesAceitas }}</h3>
            <p class="text-sm text-gray-500">Aceitas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-briefcase text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $vagasDisponiveis }}</h3>
            <p class="text-sm text-gray-500">Vagas Disponíveis</p>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('vagas.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-colors">
                    <i class="fas fa-search text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Buscar Vagas</p>
                    <p class="text-sm text-gray-500">Encontrar novas oportunidades</p>
                </div>
            </div>
        </a>

        <a href="{{ route('profile.edit') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-colors">
                    <i class="fas fa-user-edit text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-900 group-hover:text-green-600 transition-colors">Editar Perfil</p>
                    <p class="text-sm text-gray-500">Atualizar informações</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Grid com os dois novos menus -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Menu de Aplicações Recentes -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-paper-plane text-blue-500 mr-2"></i> Aplicações Recentes
                </h3>
                <a href="{{ route('applications.index') }}" class="btn-trampix-secondary text-sm">
                    <i class="fas fa-list mr-1"></i> Ver todas
                </a>
            </div>
            
            @php
                $recentApplications = $freelancer ? $freelancer->applications()
                    ->with(['jobVacancy.company'])
                    ->latest()
                    ->take(4)
                    ->get() : collect();
            @endphp
            
            <div class="space-y-3">
                @forelse($recentApplications as $application)
                <div class="trampix-card p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $application->jobVacancy->company->display_name ?? $application->jobVacancy->company->name ?? 'Empresa' }}</h4>
                            <p class="text-xs text-gray-600 mt-1">{{ $application->jobVacancy->title }}</p>
                            <div class="flex items-center mt-2 space-x-3">
                                <span class="text-xs px-2 py-1 rounded-full 
                                    {{ $application->status === 'pending' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    @switch($application->status)
                                        @case('pending') Em Análise @break
                                        @case('accepted') Aceita @break
                                        @case('rejected') Rejeitada @break
                                        @default {{ ucfirst($application->status) }}
                                    @endswitch
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $application->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-paper-plane text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Nenhuma aplicação ainda</p>
                    <a href="{{ route('vagas.index') }}" class="btn-trampix-primary text-sm mt-3">
                        Buscar Vagas
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Menu de Vagas Ativas -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-briefcase text-green-500 mr-2"></i> Vagas Ativas
                </h3>
                <a href="{{ route('vagas.index') }}" class="btn-trampix-secondary text-sm">
                    <i class="fas fa-list mr-1"></i> Ver todas
                </a>
            </div>
            
            @php
                $activeJobs = \App\Models\JobVacancy::where('status', 'active')
                    ->with(['company', 'applications'])
                    ->withCount('applications')
                    ->latest()
                    ->take(4)
                    ->get();
            @endphp
            
            <div class="space-y-3">
                @forelse($activeJobs as $job)
                <div class="trampix-card p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $job->title }}</h4>
                            <p class="text-xs text-gray-600 mt-1">{{ $job->company->display_name ?? $job->company->name ?? 'Empresa' }}</p>
                            <div class="flex items-center mt-2 space-x-3">
                                <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $job->applications_count }} candidato{{ $job->applications_count !== 1 ? 's' : '' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $job->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <a href="{{ route('vagas.show', $job) }}" class="btn-trampix-primary text-xs px-3 py-1">
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-briefcase text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Nenhuma vaga ativa</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>