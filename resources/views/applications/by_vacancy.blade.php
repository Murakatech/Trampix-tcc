@extends('layouts.dashboard')

@section('header')
@php
    // Normalizar variável da vaga para evitar null na view
    $jobVacancy = $jobVacancy ?? ($vacancy ?? null);
    $vacancy = $vacancy ?? $jobVacancy;
@endphp
@if($jobVacancy)
    <span class="flex items-center gap-2">
        <i class="fas fa-users"></i>
        Candidatos para: {{ $jobVacancy->title ?? 'Vaga sem título' }}
    </span>
@else
    <span class="flex items-center gap-2">
        <i class="fas fa-users"></i>
        Vaga não encontrada
    </span>
@endif
@endsection

@section('content')
<div class="container mt-4">
    @if(!$jobVacancy)
        <p class="text-red-600">Vaga não encontrada.</p>
        @php return; @endphp
    @endif
    {{-- Mensagens de sucesso/erro --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Informações da vaga --}}
    <div class="card mb-4">
        <div class="card-header company-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-briefcase me-2"></i>{{ $jobVacancy->title ?? 'Vaga sem título' }}
            </h3>
        </div>
        <div class="card-body">
            <p class="card-text mb-3">{{ $vacancy->description }}</p>
            <div class="row">
                <div class="col-md-3">
                    <strong>Categoria:</strong><br>
                    <span class="badge bg-secondary">{{ $vacancy->category?->name ?? '-' }}</span>
                </div>
                <!-- Tipo de contrato removido: todos os contratos são freelance -->
                <div class="col-md-3">
                    <strong>Modalidade:</strong><br>
                    <span class="badge bg-warning text-dark">{{ $vacancy->location_type ?? '-' }}</span>
                </div>
                @if($vacancy->salary_range)
                <div class="col-md-3">
                    <strong>Faixa salarial:</strong><br>
                    <span class="badge bg-success">{{ $vacancy->salary_range }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Lista de candidatos --}}
    @if ($applications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-slash fa-4x text-muted mb-4"></i>
                <h3 class="h5 mb-3">Nenhum candidato ainda</h3>
                <p class="text-muted mb-4">Esta vaga ainda não recebeu candidaturas.</p>
                <a href="{{ route('vagas.show', $vacancy) }}" class="btn btn-primary">
                    <i class="fas fa-eye me-2"></i>Ver Vaga Pública
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Candidatos
                </h4>
                <span class="badge bg-primary fs-6">{{ $applications->count() }} candidato(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="trampix-table-header">
                            <tr>
                                <th>
                                    <i class="fas fa-user me-1"></i>Candidato
                                </th>
                                <th>
                                    <i class="fas fa-envelope me-1"></i>Email
                                </th>
                                <th>
                                    <i class="fas fa-flag me-1"></i>Status
                                </th>
                                <th>
                                    <i class="fas fa-file-text me-1"></i>Carta de Apresentação
                                </th>
                                <th>
                                    <i class="fas fa-calendar me-1"></i>Data da Candidatura
                                </th>
                                <th>
                                    <i class="fas fa-cogs me-1"></i>Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle company me-2">
                                                {{ substr($application->freelancer->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $application->freelancer->user->name }}</div>
                                                @if($application->freelancer->profession)
                                                    <small class="text-muted">{{ $application->freelancer->profession }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $application->freelancer->user->email }}" class="text-decoration-none">
                                            {{ $application->freelancer->user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($application->status === 'pending')
                                            <span class="badge badge-soft-yellow">
                                                <i class="fas fa-clock me-1"></i>Pendente
                                            </span>
                                        @elseif($application->status === 'accepted')
                                            <span class="badge badge-soft-green">
                                                <i class="fas fa-check me-1"></i>Aceito
                                            </span>
                                        @elseif($application->status === 'rejected')
                                            <span class="badge badge-soft-red">
                                                <i class="fas fa-times me-1"></i>Rejeitado
                                            </span>
                                        @elseif($application->status === 'ended')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-flag-checkered me-1"></i>Finalizado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                @if($application->status === 'pending')
                                                    Pendente
                                                @elseif($application->status === 'accepted')
                                                    Aceito
                                                @elseif($application->status === 'rejected')
                                                    Rejeitado
                                                @else
                                                    {{ ucfirst($application->status) }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($application->cover_letter)
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#coverLetterModal{{ $application->id }}">
                                                <i class="fas fa-eye me-1"></i>Ver carta
                                            </button>
                                            
                                            <!-- Modal para carta de apresentação -->
                                            <div class="modal fade" id="coverLetterModal{{ $application->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Carta de Apresentação</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Candidato:</strong> {{ $application->freelancer->user->name }}</p>
                                                            <hr>
                                                            <p>{{ $application->cover_letter }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-1"></i>Sem carta
                                            </span>
                                        @endif
                                    </td>
                                    <td class="actions-cell text-center">
                                        <small>{{ $application->created_at->format('d/m/Y') }}</small><br>
                                        <small class="text-muted">{{ $application->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="actions-cell text-center">
                                        <div class="application-actions">
                                            @if($application->status === 'pending')
                                                <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="d-inline" id="acceptForm-{{ $application->id }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="accepted">
                                                    <button type="button" class="btn btn-company-primary btn-compact btn-glow" 
                                                            data-freelancer-name="{{ $application->freelancer->user->name }}"
                                                            data-job-title="{{ $application->jobVacancy->title }}"
                                                            data-application-id="{{ $application->id }}"
                                                            onclick="handleAcceptClick(this)">
                                                        <i class="fas fa-check me-1"></i>Aceitar
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="d-inline" id="rejectForm-{{ $application->id }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="button" class="btn btn-company-danger btn-compact" 
                                                            data-freelancer-name="{{ $application->freelancer->user->name }}"
                                                            data-job-title="{{ $application->jobVacancy->title }}"
                                                            data-application-id="{{ $application->id }}"
                                                            onclick="handleRejectClick(this)">
                                                        <i class="fas fa-times me-1"></i>Rejeitar
                                                    </button>
                                                </form>
                                                @elseif($application->status === 'accepted')
                                                    <span class="badge badge-soft-green me-2">
                                                        <i class="fas fa-check me-1"></i>Aceito
                                                    </span>
                                                    <a href="{{ route('company.vagas.show', $application->jobVacancy->id) }}" 
                                                       class="btn btn-company-primary btn-compact btn-glow me-2" 
                                                       title="Ver detalhes da vaga">
                                                        <i class="fas fa-eye me-1"></i>Detalhes da vaga
                                                    </a>
                                                    <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="d-inline" id="finalizeForm-{{ $application->id }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="ended">
                                                        <input type="hidden" name="finalize" value="1">
                                                        <button type="submit" class="btn btn-company-outline-secondary btn-compact">
                                                            <i class="fas fa-flag-checkered me-1"></i>Finalizar
                                                        </button>
                                                    </form>
                                                @elseif($application->status === 'rejected')
                                                    <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="d-inline" id="acceptForm2-{{ $application->id }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="button" class="btn btn-company-outline-primary btn-compact btn-glow" 
                                                                data-freelancer-name="{{ $application->freelancer->user->name }}"
                                                                data-job-title="{{ $application->jobVacancy->title }}"
                                                                data-application-id="{{ $application->id }}"
                                                                onclick="handleAccept2Click(this)">
                                                            <i class="fas fa-check me-1"></i>Aceitar
                                                        </button>
                                                    </form>
                                                    <span class="badge badge-soft-red ms-2">
                                                        <i class="fas fa-times me-1"></i>Rejeitado
                                                    </span>
                                                @elseif($application->status === 'ended')
                                                    <form method="POST" action="{{ route('applications.updateStatus', $application) }}" class="d-inline" id="reopenForm-{{ $application->id }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="button" class="btn btn-company-outline-primary btn-compact btn-glow" 
                                                                data-freelancer-name="{{ $application->freelancer->user->name }}"
                                                                data-job-title="{{ $application->jobVacancy->title }}"
                                                                data-application-id="{{ $application->id }}"
                                                                onclick="handleAccept2Click(this)">
                                                            <i class="fas fa-redo me-1"></i>Reabrir parceria
                                                        </button>
                                                    </form>
                                                    <span class="badge bg-secondary ms-2">
                                                        <i class="fas fa-flag-checkered me-1"></i>Finalizado
                                                    </span>
                                            @endif
                                        </div>
                                        
                                        {{-- Link para ver perfil do freelancer --}}
                                        <div class="mt-1">
                                            <a href="{{ route('freelancers.show', $application->freelancer) }}" 
                                               class="btn btn-company-outline-primary btn-compact" target="_blank">
                                                <i class="fas fa-user me-1"></i>Ver Perfil
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Botões de navegação --}}
    <div class="mt-4 d-flex gap-2">
        <a href="{{ route('dashboard') }}" class="btn btn-company-secondary">
            <i class="fas fa-arrow-left me-1"></i>Voltar ao Dashboard
        </a>
        <a href="{{ route('vagas.show', $vacancy) }}" class="btn btn-company-outline-primary">
            <i class="fas fa-eye me-1"></i>Ver Vaga Pública
        </a>
        <a href="{{ route('vagas.edit', $vacancy) }}" class="btn btn-outline-warning">
            <i class="fas fa-edit me-1"></i>Editar Vaga
        </a>
    </div>
</div>

{{-- Componentes de Confirmação --}}
<x-action-confirmation 
    actionType="generic" 
    modalId="acceptConfirmationModal" />

<x-action-confirmation 
    actionType="generic" 
    modalId="rejectConfirmationModal" />

@push('scripts')
<script>
    // Função para aceitar candidatura (primeira vez)
    function showAcceptConfirmation(applicationId, freelancerName, jobTitle) {
        showActionModal('acceptConfirmationModal', {
            actionType: 'generic',
            message: `Aceitar a candidatura de ${freelancerName} para a vaga "${jobTitle}"?`,
            onConfirm: () => {
                const form = document.getElementById(`acceptForm-${applicationId}`);
                showNotification('Aceitando candidatura...', 'success');
                form.submit();
            },
            onCancel: () => {
                showNotification('Ação cancelada.', 'info');
            }
        });
    }

    // Wrappers que leem dados do dataset
    function handleAcceptClick(btn) {
        const applicationId = btn.getAttribute('data-application-id');
        const freelancerName = btn.getAttribute('data-freelancer-name');
        const jobTitle = btn.getAttribute('data-job-title');
        showAcceptConfirmation(applicationId, freelancerName, jobTitle);
    }
    function handleAccept2Click(btn) {
        const applicationId = btn.getAttribute('data-application-id');
        const freelancerName = btn.getAttribute('data-freelancer-name');
        const jobTitle = btn.getAttribute('data-job-title');
        showAcceptConfirmation2(applicationId, freelancerName, jobTitle);
    }
    function handleRejectClick(btn) {
        const applicationId = btn.getAttribute('data-application-id');
        const freelancerName = btn.getAttribute('data-freelancer-name');
        const jobTitle = btn.getAttribute('data-job-title');
        showRejectConfirmation(applicationId, freelancerName, jobTitle);
    }

    // Função para aceitar candidatura (segunda vez - quando rejeitado)
    function showAcceptConfirmation2(applicationId, freelancerName, jobTitle) {
        showActionModal('acceptConfirmationModal', {
            actionType: 'generic',
            message: `Aceitar novamente a candidatura de ${freelancerName} para a vaga "${jobTitle}"?`,
            onConfirm: () => {
                const form = document.getElementById(`acceptForm2-${applicationId}`);
                showNotification('Aceitando candidatura...', 'success');
                form.submit();
            },
            onCancel: () => {
                showNotification('Ação cancelada.', 'info');
            }
        });
    }

    // Função para rejeitar candidatura (primeira vez)
    function showRejectConfirmation(applicationId, freelancerName, jobTitle) {
        showActionModal('rejectConfirmationModal', {
            actionType: 'generic',
            message: `⚠️ Rejeitar a candidatura de ${freelancerName} para a vaga "${jobTitle}"?\n\nEsta ação pode ser revertida posteriormente.`,
            onConfirm: () => {
                const form = document.getElementById(`rejectForm-${applicationId}`);
                showNotification('Rejeitando candidatura...', 'warning');
                form.submit();
            },
            onCancel: () => {
                showNotification('Ação cancelada.', 'info');
            }
        });
    }

    // Função para rejeitar candidatura (segunda vez - quando aceito)
    function showRejectConfirmation2(applicationId, freelancerName, jobTitle) {
        showActionModal('rejectConfirmationModal', {
            actionType: 'generic',
            message: `⚠️ Rejeitar a candidatura de ${freelancerName} para a vaga "${jobTitle}"?\n\nEsta candidatura estava aceita. Esta ação pode ser revertida posteriormente.`,
            onConfirm: () => {
                const form = document.getElementById(`rejectForm2-${applicationId}`);
                showNotification('Rejeitando candidatura...', 'warning');
                form.submit();
            },
            onCancel: () => {
                showNotification('Ação cancelada.', 'info');
            }
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Company themed header for cards */
    .company-header {
        background: linear-gradient(to bottom right, #1ca751, var(--trampix-green));
        color: var(--trampix-black);
        border-bottom: 1px solid rgba(185, 255, 102, 0.35);
    }

    /* Soft badges aligned with company palette */
    .badge-soft-green {
        background-color: rgba(185, 255, 102, 0.18);
        color: #3f3f46;
        border: 1px solid rgba(185, 255, 102, 0.35);
    }
    .badge-soft-yellow {
        background-color: rgba(255, 221, 87, 0.20);
        color: #3f3f46;
        border: 1px solid rgba(255, 221, 87, 0.35);
    }
    .badge-soft-red {
        background-color: rgba(255, 76, 76, 0.15);
        color: #3f3f46;
        border: 1px solid rgba(255, 76, 76, 0.30);
    }

    /* Avatar adapted to company theme */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
    .avatar-circle.company {
        background: linear-gradient(to bottom right, #1ca751, var(--trampix-green));
        color: var(--trampix-black);
        border: 1px solid rgba(185, 255, 102, 0.45);
    }

    /* Company themed buttons */
    .btn-company-primary {
        background-color: var(--trampix-green);
        border: 1px solid var(--trampix-green);
        color: var(--trampix-black);
        border-radius: 10px;
        padding: 6px 12px;
    }
    .btn-company-primary:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
    }
    .btn-company-outline-primary {
        background: transparent;
        border: 2px solid var(--trampix-green);
        color: var(--trampix-black);
        border-radius: 10px;
        padding: 6px 12px;
    }
    .btn-company-outline-primary:hover {
        background-color: var(--trampix-green);
        color: var(--trampix-black);
    }
    .btn-company-secondary {
        background-color: var(--trampix-light-gray);
        border: 1px solid rgba(185, 255, 102, 0.45);
        color: var(--trampix-black);
        border-radius: 10px;
        padding: 6px 12px;
    }
    .btn-company-secondary:hover {
        background-color: rgba(185, 255, 102, 0.25);
        border-color: rgba(185, 255, 102, 0.55);
        transform: translateY(-1px);
    }
    .btn-company-danger {
        background-color: var(--trampix-red);
        border: 1px solid var(--trampix-red);
        color: #fff;
        border-radius: 10px;
        padding: 6px 12px;
    }
    .btn-company-danger:hover {
        filter: brightness(0.95);
        transform: translateY(-1px);
    }
    .btn-company-outline-danger {
        background: transparent;
        border: 2px solid var(--trampix-red);
        color: var(--trampix-red);
        border-radius: 10px;
        padding: 6px 12px;
    }
    .btn-company-outline-danger:hover {
        background-color: var(--trampix-red);
        color: #fff;
    }

    /* Compact actions layout in table */
    .actions-cell { min-width: 280px; }
    .application-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    @media (min-width: 576px) {
        .application-actions {
            flex-direction: row;
            justify-content: center;
        }
    }
    .btn-compact {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 8px;
    }
</style>
@endpush
@endsection
