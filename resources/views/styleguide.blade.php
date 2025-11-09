@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Styleguide</h1>
@endsection

@section('content')
<style>
    /* Cores da nova identidade visual */
    :root {
        --trampix-purple: #8F3FF7;
        --trampix-green: #B9FF66;
        --trampix-black: #191A23;
        --trampix-light-gray: #F3F3F3;
        --trampix-red: #FF4C4C;
        --trampix-dark-gray: #4A4A4A;
    }

    /* Tipografia */
    .trampix-h1 {
        color: var(--trampix-purple);
        font-weight: 700;
        font-size: 2.5rem;
    }

    .trampix-h2 {
        color: var(--trampix-black);
        font-weight: 500;
        font-size: 2rem;
    }

    .trampix-h3 {
        color: var(--trampix-green);
        font-weight: 600;
        font-size: 1.5rem;
    }

    .trampix-p {
        color: var(--trampix-dark-gray);
        font-weight: 400;
    }

    /* Botões personalizados */
    .btn-trampix-primary {
        background-color: var(--trampix-purple);
        border-color: var(--trampix-purple);
        color: white;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-trampix-primary:hover {
        background-color: var(--trampix-green);
        border-color: var(--trampix-green);
        color: var(--trampix-black);
        transform: translateY(-2px);
    }

    .btn-trampix-secondary {
        background-color: var(--trampix-light-gray);
        border: 2px solid var(--trampix-purple);
        color: var(--trampix-black);
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-trampix-secondary:hover {
        background-color: var(--trampix-green);
        border-color: var(--trampix-green);
        color: var(--trampix-black);
        transform: translateY(-2px);
    }

    .btn-trampix-danger {
        background-color: var(--trampix-red);
        border-color: var(--trampix-red);
        color: white;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-trampix-danger:hover {
        background-color: #e63946;
        border-color: #e63946;
        color: white;
        transform: translateY(-2px);
    }

    /* Cards personalizados */
    .trampix-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(143, 63, 247, 0.1);
        border: none;
        padding: 24px;
        transition: all 0.3s ease;
    }

    .trampix-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(143, 63, 247, 0.15);
    }

    /* Inputs personalizados */
    .trampix-input {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: white;
    }

    .trampix-input:focus {
        border-color: var(--trampix-purple);
        box-shadow: 0 0 0 3px rgba(143, 63, 247, 0.1);
        outline: none;
    }

    /* Badges personalizados */
    .badge-trampix-success {
        background-color: var(--trampix-green);
        color: var(--trampix-black);
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 20px;
    }

    .badge-trampix-danger {
        background-color: var(--trampix-red);
        color: white;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 20px;
    }

    .badge-trampix-primary {
        background-color: var(--trampix-purple);
        color: white;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 20px;
    }

    /* Layout geral */
    .trampix-bg {
        background-color: var(--trampix-light-gray);
        min-height: 100vh;
    }

    .trampix-section {
        margin-bottom: 48px;
    }

    /* Alertas personalizados */
    .alert-trampix-success {
        background-color: rgba(185, 255, 102, 0.1);
        border: 2px solid var(--trampix-green);
        color: var(--trampix-black);
        border-radius: 12px;
        padding: 16px 20px;
    }

    .alert-trampix-danger {
        background-color: rgba(255, 76, 76, 0.1);
        border: 2px solid var(--trampix-red);
        color: var(--trampix-red);
        border-radius: 12px;
        padding: 16px 20px;
    }

    .alert-trampix-info {
        background-color: rgba(143, 63, 247, 0.1);
        border: 2px solid var(--trampix-purple);
        color: var(--trampix-purple);
        border-radius: 12px;
        padding: 16px 20px;
    }
</style>

<div class="trampix-bg">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="trampix-h1 mb-2">
                    <i class="fas fa-palette me-3"></i>Styleguide Trampix
                </h1>
                <p class="trampix-p mb-5">Nova identidade visual com cores vibrantes e design minimalista</p>
            </div>
        </div>

        {{-- Paleta de Cores --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">🎨 Paleta de Cores</h2>
            <div class="trampix-card">
                <div class="row g-4">
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-white rounded-3 mb-3" style="background-color: #8F3FF7;">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Roxo Principal</h6>
                        <small class="text-muted">#8F3FF7</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-dark rounded-3 mb-3" style="background-color: #B9FF66;">
                            <i class="fas fa-bolt fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Verde Destaque</h6>
                        <small class="text-muted">#B9FF66</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-white rounded-3 mb-3" style="background-color: #191A23;">
                            <i class="fas fa-moon fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Preto Quase Total</h6>
                        <small class="text-muted">#191A23</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-dark rounded-3 mb-3 border" style="background-color: #F3F3F3;">
                            <i class="fas fa-cloud fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Cinza Claro</h6>
                        <small class="text-muted">#F3F3F3</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-white rounded-3 mb-3" style="background-color: #FF4C4C;">
                            <i class="fas fa-exclamation fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Vermelho Suave</h6>
                        <small class="text-muted">#FF4C4C</small>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="p-4 text-white rounded-3 mb-3" style="background-color: #4A4A4A;">
                            <i class="fas fa-text-height fa-2x"></i>
                        </div>
                        <h6 class="fw-bold">Cinza Escuro</h6>
                        <small class="text-muted">#4A4A4A</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tipografia --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">✍️ Tipografia</h2>
            <div class="trampix-card">
                <h1 class="trampix-h1 mb-3">H1: Título Principal - Roxo e Forte</h1>
                <h2 class="trampix-h2 mb-3">H2: Título Secundário - Preto e Médio</h2>
                <h3 class="trampix-h3 mb-3">H3: Destaque Secundário - Verde</h3>
                <p class="trampix-p mb-3">Parágrafo: Texto padrão em cinza escuro para boa legibilidade</p>
                <small class="text-muted">Texto pequeno: Para informações secundárias</small>
            </div>
        </div>

        {{-- Botões --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">🔘 Botões</h2>
            <div class="trampix-card">
                <h3 class="trampix-h3 mb-3">Botões Primários</h3>
                <div class="mb-4">
                    <button type="button" class="btn-trampix-primary me-3">
                        <i class="fas fa-plus me-2"></i>Criar Vaga
                    </button>
                    <button type="button" class="btn-trampix-primary me-3">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                    <button type="button" class="btn-trampix-primary">
                        <i class="fas fa-check me-2"></i>Confirmar
                    </button>
                </div>

                <h3 class="trampix-h3 mb-3">Botões Secundários</h3>
                <div class="mb-4">
                    <button type="button" class="btn-trampix-secondary me-3">
                        <i class="fas fa-edit me-2"></i>Editar
                    </button>
                    <button type="button" class="btn-trampix-secondary me-3">
                        <i class="fas fa-eye me-2"></i>Visualizar
                    </button>
                    <button type="button" class="btn-trampix-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </button>
                </div>

                <h3 class="trampix-h3 mb-3">Botões de Perigo</h3>
                <div class="mb-4">
                    <button type="button" class="btn-trampix-danger me-3">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </button>
                    <button type="button" class="btn-trampix-danger me-3">
                        <i class="fas fa-times me-2"></i>Rejeitar
                    </button>
                    <button type="button" class="btn-trampix-danger">
                        <i class="fas fa-ban me-2"></i>Cancelar
                    </button>
                </div>
            </div>
        </div>

        {{-- Cards --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">🃏 Cards</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="trampix-card text-center">
                        <div class="mb-3" style="color: var(--trampix-purple);">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                        <h4 class="trampix-h3">Freelancers</h4>
                        <p class="trampix-p">Gerencie todos os freelancers cadastrados na plataforma</p>
                        <button class="btn-trampix-primary">
                            <i class="fas fa-arrow-right me-2"></i>Acessar
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="trampix-card text-center">
                        <div class="mb-3" style="color: var(--trampix-green);">
                            <i class="fas fa-building fa-3x"></i>
                        </div>
                        <h4 class="trampix-h3">Empresas</h4>
                        <p class="trampix-p">Administre as empresas e suas vagas disponíveis</p>
                        <button class="btn-trampix-secondary">
                            <i class="fas fa-arrow-right me-2"></i>Acessar
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="trampix-card text-center">
                        <div class="mb-3" style="color: var(--trampix-red);">
                            <i class="fas fa-clipboard-list fa-3x"></i>
                        </div>
                        <h4 class="trampix-h3">Candidaturas</h4>
                        <p class="trampix-p">Acompanhe todas as candidaturas do sistema</p>
                        <button class="btn-trampix-danger">
                            <i class="fas fa-arrow-right me-2"></i>Acessar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulários --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">📝 Formulários</h2>
            <div class="trampix-card">
                <form>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold trampix-p">Nome Completo</label>
                            <input type="text" class="form-control trampix-input" placeholder="Digite seu nome...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold trampix-p">Email</label>
                            <input type="email" class="form-control trampix-input" placeholder="seu@email.com">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold trampix-p">Tipo de Usuário</label>
                            <select class="form-select trampix-input">
                                <option selected>Escolha uma opção...</option>
                                <option value="freelancer">Freelancer</option>
                                <option value="company">Empresa</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold trampix-p">Descrição</label>
                            <textarea class="form-control trampix-input" rows="4" placeholder="Conte um pouco sobre você..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms">
                                <label class="form-check-label trampix-p" for="terms">
                                    Aceito os termos e condições
                                </label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn-trampix-primary me-3">
                                <i class="fas fa-save me-2"></i>Salvar
                            </button>
                            <button type="button" class="btn-trampix-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Badges e Status --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">🏷️ Badges e Status</h2>
            <div class="trampix-card">
                <h3 class="trampix-h3 mb-3">Status de Candidaturas</h3>
                <div class="mb-4">
                    <span class="badge-trampix-success me-3">
                        <i class="fas fa-check me-1"></i>Aceita
                    </span>
                    <span class="badge-trampix-danger me-3">
                        <i class="fas fa-times me-1"></i>Rejeitada
                    </span>
                    <span class="badge-trampix-primary me-3">
                        <i class="fas fa-clock me-1"></i>Pendente
                    </span>
                </div>

                <h3 class="trampix-h3 mb-3">Tipos de Usuário</h3>
                <div class="mb-4">
                    <span class="badge-trampix-danger me-3">Administrador</span>
                    <span class="badge-trampix-primary me-3">Empresa</span>
                    <span class="badge-trampix-success me-3">Freelancer</span>
                </div>
            </div>
        </div>

        {{-- Alertas --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">⚠️ Alertas</h2>
            <div class="trampix-card">
                <div class="alert-trampix-success mb-3">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Sucesso!</strong> Operação realizada com sucesso.
                </div>
                <div class="alert-trampix-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erro!</strong> Ocorreu um erro durante a operação.
                </div>
                <div class="alert-trampix-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informação:</strong> Dados atualizados recentemente.
                </div>
            </div>
        </div>

        {{-- Ícones --}}
        <div class="trampix-section">
            <h2 class="trampix-h2 mb-4">🎯 Ícones</h2>
            <div class="trampix-card">
                <div class="row g-4">
                    <div class="col-md-3">
                        <h5 class="trampix-h3 mb-3">Usuários</h5>
                        <div class="d-flex gap-3 mb-3">
                            <i class="fas fa-user fa-2x" style="color: var(--trampix-purple);"></i>
                            <i class="fas fa-users fa-2x" style="color: var(--trampix-green);"></i>
                            <i class="fas fa-building fa-2x" style="color: var(--trampix-black);"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5 class="trampix-h3 mb-3">Ações</h5>
                        <div class="d-flex gap-3 mb-3">
                            <i class="fas fa-plus fa-2x" style="color: var(--trampix-purple);"></i>
                            <i class="fas fa-edit fa-2x" style="color: var(--trampix-green);"></i>
                            <i class="fas fa-trash fa-2x" style="color: var(--trampix-red);"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5 class="trampix-h3 mb-3">Status</h5>
                        <div class="d-flex gap-3 mb-3">
                            <i class="fas fa-check fa-2x" style="color: var(--trampix-green);"></i>
                            <i class="fas fa-times fa-2x" style="color: var(--trampix-red);"></i>
                            <i class="fas fa-clock fa-2x" style="color: var(--trampix-purple);"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h5 class="trampix-h3 mb-3">Navegação</h5>
                        <div class="d-flex gap-3 mb-3">
                            <i class="fas fa-home fa-2x" style="color: var(--trampix-black);"></i>
                            <i class="fas fa-search fa-2x" style="color: var(--trampix-purple);"></i>
                            <i class="fas fa-arrow-left fa-2x" style="color: var(--trampix-green);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navegação --}}
        <div class="trampix-section">
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="btn-trampix-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection