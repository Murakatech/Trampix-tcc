{{-- Seção EMPRESA --}}
<section>
    @php
        $company = auth()->user()->company;
        $totalVagas = $company ? $company->vacancies()->count() : 0;
        $vagasAtivas = $company ? $company->vacancies()->where('status', 'active')->count() : 0;
        $totalAplicacoes = $company ? \App\Models\Application::whereIn('job_vacancy_id', $company->vacancies->pluck('id'))->count() : 0;
        $aplicacoesPendentes = $company ? \App\Models\Application::whereIn('job_vacancy_id', $company->vacancies->pluck('id'))->where('status', 'pending')->count() : 0;
    @endphp

    <!-- Resumo Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="trampix-card text-center">
            <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-briefcase text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalVagas }}</h3>
            <p class="text-sm text-gray-500">Total de Vagas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $vagasAtivas }}</h3>
            <p class="text-sm text-gray-500">Vagas Ativas</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $totalAplicacoes }}</h3>
            <p class="text-sm text-gray-500">Total Aplicações</p>
        </div>

        <div class="trampix-card text-center">
            <div class="bg-orange-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                <i class="fas fa-clock text-orange-600 text-2xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $aplicacoesPendentes }}</h3>
            <p class="text-sm text-gray-500">Pendentes</p>
        </div>
    </div>

    <!-- Grid com os dois novos menus -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Menu de Aplicações Recentes -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-user-check text-blue-500 mr-2"></i> Aplicações Recentes
                </h3>
                <a href="{{ route('applications.manage') }}" class="btn-trampix-company text-sm">
                    <i class="fas fa-list mr-1"></i> Ver todas
                </a>
            </div>
            
            @php
                $recentApplications = $company ? \App\Models\Application::whereIn('job_vacancy_id', $company->vacancies->pluck('id'))
                    ->with(['freelancer.user', 'jobVacancy'])
                    ->latest()
                    ->take(4)
                    ->get() : collect();
            @endphp
            
            <div class="space-y-3">
                @forelse($recentApplications as $application)
                <div class="trampix-card p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $application->freelancer->user->name ?? $application->freelancer->display_name ?? 'Freelancer' }}</h4>
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
                    <i class="fas fa-user-check text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Nenhuma aplicação ainda</p>
                    <a href="{{ route('vagas.create') }}" class="btn-trampix-company text-sm mt-3">
                        Criar Primeira Vaga
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Menu de Vagas Ativas -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-briefcase text-green-500 mr-2"></i> Minhas Vagas Ativas
                </h3>
                <a href="{{ route('company.vagas.index') }}" class="btn-trampix-company text-sm">
                    <i class="fas fa-list mr-1"></i> Ver todas
                </a>
            </div>
            
            @php
                $activeJobs = $company ? $company->vacancies()
                    ->where('status', 'active')
                    ->withCount('applications')
                    ->latest()
                    ->take(4)
                    ->get() : collect();
            @endphp
            
            <div class="space-y-3">
                @forelse($activeJobs as $job)
                <div class="trampix-card p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $job->title }}</h4>
                            <p class="text-xs text-gray-600 mt-1">{{ Str::limit($job->description, 60) }}</p>
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
                            <a href="{{ route('vagas.show', $job) }}" class="btn-trampix-company text-xs px-3 py-1">
                                Ver
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-briefcase text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Nenhuma vaga ativa</p>
                    <a href="{{ route('vagas.create') }}" class="btn-trampix-company text-sm mt-3">
                        Criar Primeira Vaga
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>