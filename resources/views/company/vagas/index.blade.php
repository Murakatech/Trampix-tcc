@extends('layouts.dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="trampix-h1">Minhas Vagas</h1>
    <a href="{{ route('vagas.create') }}" class="btn-trampix-company btn-glow">
        <i class="fas fa-plus me-2"></i>Nova Vaga
    </a>
</div>
@endsection

@section('content')
<div class="container mt-4">
    {{-- Alerts de sessão --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Estatísticas --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-briefcase text-primary fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total de Vagas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['active'] }}</h3>
                    <p class="text-muted mb-0">Vagas Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-times-circle text-danger fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['closed'] }}</h3>
                    <p class="text-muted mb-0">Vagas Fechadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="trampix-card text-center">
                <div class="card-body">
                    <i class="fas fa-users text-info fs-2 mb-2"></i>
                    <h3 class="trampix-h3 mb-1">{{ $stats['total_applications'] }}</h3>
                    <p class="text-muted mb-0">Total de Candidaturas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="trampix-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('company.vagas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Categoria</label>
                    <select name="category" id="category" class="trampix-input">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="trampix-input">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Encerrada</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="contract_type" class="form-label">Tipo de Contrato</label>
                    <select name="contract_type" id="contract_type" class="trampix-input">
                        <option value="">Todos os tipos</option>
                        <option value="CLT" {{ request('contract_type') == 'CLT' ? 'selected' : '' }}>CLT</option>
                        <option value="PJ" {{ request('contract_type') == 'PJ' ? 'selected' : '' }}>PJ</option>
                        <option value="Freelance" {{ request('contract_type') == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                        <option value="Estágio" {{ request('contract_type') == 'Estágio' ? 'selected' : '' }}>Estágio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="location_type" class="form-label">Modalidade</label>
                    <select name="location_type" id="location_type" class="trampix-input">
                        <option value="">Todas as modalidades</option>
                        <option value="Presencial" {{ request('location_type') == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                        <option value="Remoto" {{ request('location_type') == 'Remoto' ? 'selected' : '' }}>Remoto</option>
                        <option value="Híbrido" {{ request('location_type') == 'Híbrido' ? 'selected' : '' }}>Híbrido</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn-trampix-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('company.vagas.index') }}" class="btn-trampix-secondary">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de Vagas --}}
    @if ($vagas->isEmpty())
        <div class="trampix-card text-center py-5">
            <div class="card-body">
                <i class="fas fa-briefcase text-muted" style="font-size: 4rem;"></i>
                <h3 class="trampix-h3 mt-3 mb-2">Nenhuma vaga encontrada</h3>
                @if(request()->hasAny(['category', 'status', 'contract_type', 'location_type']))
                    <p class="text-muted mb-3">Tente ajustar os filtros ou</p>
                    <a href="{{ route('company.vagas.index') }}" class="btn-trampix-secondary me-2">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </a>
                @else
                    <p class="text-muted mb-3">Comece criando sua primeira vaga de emprego</p>
                @endif
                <a href="{{ route('vagas.create') }}" class="btn-trampix-company">
                    <i class="fas fa-plus me-1"></i>Criar Nova Vaga
                </a>
            </div>
        </div>
    @else
        <div class="trampix-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="trampix-table-header">
                            <tr>
                                <th>Título</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Modalidade</th>
                                <th>Status</th>
                                <th>Candidaturas</th>
                                <th>Criada em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vagas as $vaga)
                                <tr>
                                    <td>
                                        <a href="{{ route('company.vagas.show', $vaga->id) }}" class="text-decoration-none fw-bold">
                                            {{ $vaga->title }}
                                        </a>
                                        @if($vaga->description)
                                            <br><small class="text-muted">{{ Str::limit($vaga->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->category)
                                            <span class="badge bg-secondary">{{ $vaga->category }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->contract_type)
                                            <span class="badge bg-info">{{ $vaga->contract_type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->location_type)
                                            <span class="badge bg-success">{{ $vaga->location_type }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vaga->status === 'active')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Ativa
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Fechada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $vaga->applications->count() }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $vaga->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('company.vagas.show', $vaga->id) }}" 
                                               class="btn-trampix-secondary btn-glow px-3 py-2 text-sm"
                                               title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="{{ route('vagas.edit', $vaga->id) }}" 
                                               class="btn-trampix-primary btn-glow px-3 py-2 text-sm"
                                               title="Editar vaga">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('company.vagas.toggle-status', $vaga->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn-trampix-warning btn-glow px-3 py-2 text-sm"
                                                        title="{{ $vaga->status === 'active' ? 'Encerrar vaga' : 'Reativar vaga' }}">
                                                    <i class="fas fa-{{ $vaga->status === 'active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('vagas.destroy', $vaga->id) }}" 
                                                  method="POST" class="d-inline"
                                                  id="deleteForm-{{ $vaga->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        class="btn-trampix-danger btn-glow px-3 py-2 text-sm" 
                                                        onclick="showDeleteConfirmation({{ $vaga->id }}, '{{ $vaga->title }}', '{{ $vaga->empresa->nome ?? 'Empresa' }}')"
                                                        title="Excluir vaga">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginação --}}
                @if ($vagas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $vagas->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Componente de Confirmação --}}
<x-action-confirmation 
    actionType="exclusao" 
    modalId="deleteConfirmationModal" />

@push('scripts')
<script>
    // Função para excluir vaga
    function showDeleteConfirmation(vagaId, jobTitle, companyName) {
        showActionModal('deleteConfirmationModal', {
            actionType: 'exclusao',
            message: `🗑️ Tem certeza que deseja excluir a vaga "${jobTitle}" da empresa ${companyName}?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão perdidos.`,
            onConfirm: () => {
                const form = document.getElementById(`deleteForm-${vagaId}`);
                showNotification('Excluindo vaga...', 'warning');
                form.submit();
            },
            onCancel: () => {
                showNotification('Exclusão cancelada.', 'info');
            }
        });
    }
</script>
@endpush

@endsection