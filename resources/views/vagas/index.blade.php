@extends('layouts.app')

@section('header')
<div class="bg-white shadow-sm border-bottom">
    <div class="container py-3">
        <h1 class="trampix-h2 mb-0">Vagas Disponíveis</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
    {{-- Alerts de sessão --}}
    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @if ($vagas->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nenhuma vaga encontrada.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="trampix-table-header">
                            <tr>
                                <th>Título</th>
                                <th>Empresa</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th>Local</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vagas as $vaga)
                                <tr>
                                    <td>
                                        <a href="{{ route('vagas.show', $vaga->id) }}" class="text-decoration-none fw-bold">
                                            {{ $vaga->title }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $vaga->company->name ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $vaga->category ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $vaga->contract_type ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $vaga->location_type ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('vagas.show', $vaga->id) }}" class="btn-trampix-secondary btn-glow px-3 py-2 text-sm">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                            
                                            @can('isFreelancer')
                                                @php
                                                    $hasApplied = $vaga->applications()->where('freelancer_id', auth()->user()->freelancer?->id)->exists();
                                                @endphp
                                                
                                                @if (!$hasApplied)
                                    <form action="{{ route('applications.store', $vaga->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-trampix-primary btn-glow px-3 py-2 text-sm">
                                            <i class="fas fa-paper-plane me-1"></i>Candidatar
                                        </button>
                                    </form>
                                @else
                                                    <span class="badge bg-warning">Já candidatado</span>
                                                @endif
                                            @endcan

                                            @can('update', $vaga)
                                                <a href="{{ route('vagas.edit', $vaga->id) }}" class="btn-trampix-secondary btn-glow px-3 py-2 text-sm">
                                                    <i class="fas fa-edit me-1"></i>Editar
                                                </a>
                                            @endcan

                                            @can('delete', $vaga)
                                                <form action="{{ route('vagas.destroy', $vaga->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-trampix-danger btn-glow px-3 py-2 text-sm" 
                                                            onclick="return confirm('Tem certeza que deseja excluir esta vaga?')">
                                                        <i class="fas fa-trash me-1"></i>Excluir
                                                    </button>
                                                </form>
                                            @endcan
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
                        {{ $vagas->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
