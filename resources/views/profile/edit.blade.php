@extends('layouts.app')

@section('header')
<div class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Perfil</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            {{-- Abas da página de perfil unificado --}}
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-account" data-bs-toggle="tab" data-bs-target="#pane-account" type="button" role="tab" aria-controls="pane-account" aria-selected="true">Conta</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-freelancer" data-bs-toggle="tab" data-bs-target="#pane-freelancer" type="button" role="tab" aria-controls="pane-freelancer" aria-selected="false">Freelancer</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-company" data-bs-toggle="tab" data-bs-target="#pane-company" type="button" role="tab" aria-controls="pane-company" aria-selected="false">Empresa</button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="profileTabContent">
                {{-- Aba: Conta --}}
                <div class="tab-pane fade show active" id="pane-account" role="tabpanel" aria-labelledby="tab-account">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Informações da Conta</h5>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Alterar Senha</h5>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0 text-danger">Excluir Conta</h5>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                {{-- Aba: Freelancer --}}
                <div class="tab-pane fade" id="pane-freelancer" role="tabpanel" aria-labelledby="tab-freelancer">
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Perfil de Freelancer</h5>
                            @if(!$freelancer)
                                <span class="badge bg-warning text-dark">Perfil não criado</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <input type="hidden" name="section" value="freelancer">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_bio" class="form-label">Bio</label>
                                        <textarea id="fl_bio" name="bio" class="form-control @error('bio') is-invalid @enderror" rows="4">{{ old('bio', $freelancer->bio ?? '') }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_portfolio_url" class="form-label">URL do Portfólio</label>
                                        <input id="fl_portfolio_url" name="portfolio_url" type="url" class="form-control @error('portfolio_url') is-invalid @enderror" value="{{ old('portfolio_url', $freelancer->portfolio_url ?? '') }}">
                                        @error('portfolio_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_phone" class="form-label">Telefone</label>
                                        <input id="fl_phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $freelancer->phone ?? '') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_location" class="form-label">Localização</label>
                                        <input id="fl_location" name="location" type="text" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $freelancer->location ?? '') }}">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_hourly_rate" class="form-label">Valor por Hora (R$)</label>
                                        <input id="fl_hourly_rate" name="hourly_rate" type="number" step="0.01" class="form-control @error('hourly_rate') is-invalid @enderror" value="{{ old('hourly_rate', $freelancer->hourly_rate ?? '') }}">
                                        @error('hourly_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fl_availability" class="form-label">Disponibilidade</label>
                                        <input id="fl_availability" name="availability" type="text" class="form-control @error('availability') is-invalid @enderror" value="{{ old('availability', $freelancer->availability ?? '') }}">
                                        @error('availability')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="fl_cv" class="form-label">Currículo (PDF/DOC)</label>
                                    <input id="fl_cv" name="cv" type="file" class="form-control @error('cv') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                    @error('cv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if(isset($freelancer) && $freelancer && $freelancer->cv_url)
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_cv" id="fl_remove_cv" value="1">
                                            <label class="form-check-label" for="fl_remove_cv">Remover CV existente</label>
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button type="submit" class="btn btn-success">Salvar Freelancer</button>
                                    @if(session('success'))
                                        <small class="text-success">{{ session('success') }}</small>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Upload de foto para freelancer --}}
                    @if(isset($freelancer) && $freelancer)
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Foto de Perfil (Freelancer)</h6>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{ route('profile.photo.upload') }}" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                    @csrf
                                    <input type="hidden" name="profile_type" value="freelancer">
                                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                    <button type="submit" class="btn btn-outline-success">Enviar Foto</button>
                                </form>
                                @if($freelancer->profile_photo)
                                    <form method="post" action="{{ route('profile.photo.delete') }}" class="mt-2">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="profile_type" value="freelancer">
                                        <button type="submit" class="btn btn-outline-danger">Remover Foto</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Aba: Empresa --}}
                <div class="tab-pane fade" id="pane-company" role="tabpanel" aria-labelledby="tab-company">
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Perfil de Empresa</h5>
                            @if(!$company)
                                <span class="badge bg-warning text-dark">Perfil não criado</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <input type="hidden" name="section" value="company">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="co_name" class="form-label">Nome da Empresa</label>
                                        <input id="co_name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name ?? '') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="co_cnpj" class="form-label">CNPJ</label>
                                        <input id="co_cnpj" name="cnpj" type="text" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj', $company->cnpj ?? '') }}">
                                        @error('cnpj')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="co_sector" class="form-label">Setor</label>
                                        <input id="co_sector" name="sector" type="text" class="form-control @error('sector') is-invalid @enderror" value="{{ old('sector', $company->sector ?? '') }}">
                                        @error('sector')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="co_location" class="form-label">Localização</label>
                                        <input id="co_location" name="location" type="text" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $company->location ?? '') }}">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="co_description" class="form-label">Descrição</label>
                                    <textarea id="co_description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $company->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="co_website" class="form-label">Website</label>
                                        <input id="co_website" name="website" type="url" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $company->website ?? '') }}">
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="co_phone" class="form-label">Telefone</label>
                                        <input id="co_phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $company->phone ?? '') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="co_employees_count" class="form-label">Nº de Funcionários</label>
                                        <input id="co_employees_count" name="employees_count" type="number" min="1" class="form-control @error('employees_count') is-invalid @enderror" value="{{ old('employees_count', $company->employees_count ?? '') }}">
                                        @error('employees_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="co_founded_year" class="form-label">Ano de Fundação</label>
                                        <input id="co_founded_year" name="founded_year" type="number" min="1800" max="{{ date('Y') }}" class="form-control @error('founded_year') is-invalid @enderror" value="{{ old('founded_year', $company->founded_year ?? '') }}">
                                        @error('founded_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button type="submit" class="btn btn-success">Salvar Empresa</button>
                                    @if(session('success'))
                                        <small class="text-success">{{ session('success') }}</small>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Upload de logo para empresa --}}
                    @if(isset($company) && $company)
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Logo / Foto (Empresa)</h6>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{ route('profile.photo.upload') }}" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                    @csrf
                                    <input type="hidden" name="profile_type" value="company">
                                    <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                    <button type="submit" class="btn btn-outline-success">Enviar Logo</button>
                                </form>
                                @if($company->profile_photo)
                                    <form method="post" action="{{ route('profile.photo.delete') }}" class="mt-2">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="profile_type" value="company">
                                        <button type="submit" class="btn btn-outline-danger">Remover Logo</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
