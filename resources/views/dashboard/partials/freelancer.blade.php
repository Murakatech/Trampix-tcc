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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
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

        <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-105 transition-all duration-300 group">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-colors">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Minhas Aplicações</p>
                    <p class="text-sm text-gray-500">Acompanhar candidaturas</p>
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

    <!-- Aplicações Recentes -->
    @if($freelancer && $freelancer->applications()->exists())
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-history text-blue-500 mr-2"></i> Aplicações Recentes
        </h3>
        <div class="space-y-4">
            @foreach($freelancer->applications()->with('jobVacancy')->latest()->take(3)->get() as $application)
            <div class="trampix-card">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $application->jobVacancy->title }}</h4>
                        <p class="text-sm text-gray-500">{{ $application->jobVacancy->company->name ?? 'Empresa' }}</p>
                        <div class="flex items-center mt-2 space-x-4">
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
                                {{ $application->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('vagas.show', $application->jobVacancy) }}" class="btn-trampix-secondary text-sm">
                            Ver Vaga
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Vagas Recomendadas -->
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
            <i class="fas fa-star text-yellow-500 mr-2"></i> Vagas Recomendadas
        </h3>
        <div class="space-y-4">
            @php
                $vagasRecomendadas = \App\Models\JobVacancy::where('status', 'active')
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp
            
            @forelse($vagasRecomendadas as $vaga)
            <div class="trampix-card">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $vaga->title }}</h4>
                        <p class="text-sm text-gray-500">{{ $vaga->company->name ?? 'Empresa' }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($vaga->description, 100) }}</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                {{ $vaga->type ?? 'Remoto' }}
                            </span>
                            @if($vaga->salary)
                            <span class="text-xs text-gray-500">
                                R$ {{ number_format($vaga->salary, 2, ',', '.') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('vagas.show', $vaga) }}" class="btn-trampix-primary text-sm">
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-500">Nenhuma vaga disponível no momento</p>
                <a href="{{ route('vagas.index') }}" class="btn-trampix-primary mt-3">
                    Explorar Vagas
                </a>
            </div>
            @endforelse
        </div>
    </div>
</section>