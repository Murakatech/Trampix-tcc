@extends('layouts.app')

@section('header')
<div class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="trampix-h1">Conta</h1>
    </div>
</div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        
        {{-- Informações da Conta --}}
        <div class="trampix-card">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informações da Conta</h2>
                @include('profile.partials.update-account-information-form')
            </div>
        </div>

        {{-- Alterar Senha --}}
        <div class="trampix-card">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Alterar Senha</h2>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Excluir Perfis Específicos --}}
        @if(auth()->user()->freelancer || auth()->user()->company)
            <div class="trampix-card">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-red-600 mb-4">
                        <i class="fas fa-user-times mr-2"></i>
                        Excluir Perfis
                    </h2>
                    <p class="text-sm text-gray-600 mb-6">
                        Você pode excluir perfis específicos mantendo sua conta principal. Esta ação é irreversível.
                    </p>

                    <div class="space-y-4">
                        {{-- Botão Excluir Perfil Freelancer --}}
                        @if(auth()->user()->freelancer)
                            <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                                <div class="flex items-center">
                                    <i class="fas fa-user text-red-500 text-xl mr-3"></i>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Perfil Freelancer</h3>
                                        <p class="text-sm text-gray-600">Excluir permanentemente seu perfil de freelancer</p>
                                    </div>
                                </div>
                                <button 
                                    type="button"
                                    x-data=""
                                    x-on:click="$dispatch('open-modal', 'confirm-freelancer-deletion')"
                                    class="btn-trampix-danger">
                                    <i class="fas fa-trash mr-2"></i>
                                    Excluir Perfil
                                </button>
                            </div>
                        @endif

                        {{-- Botão Excluir Perfil Empresa --}}
                        @if(auth()->user()->company)
                            <div class="flex items-center justify-between p-4 border border-red-200 rounded-lg bg-red-50">
                                <div class="flex items-center">
                                    <i class="fas fa-building text-red-500 text-xl mr-3"></i>
                                    <div>
                                        <h3 class="font-medium text-gray-900">Perfil Empresa</h3>
                                        <p class="text-sm text-gray-600">Excluir permanentemente seu perfil de empresa</p>
                                    </div>
                                </div>
                                <button 
                                    type="button"
                                    x-data=""
                                    x-on:click="$dispatch('open-modal', 'confirm-company-deletion')"
                                    class="btn-trampix-danger">
                                    <i class="fas fa-trash mr-2"></i>
                                    Excluir Perfil
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Excluir Conta --}}
        <div class="trampix-card">
            <div class="p-6">
                <h2 class="text-lg font-medium text-red-600 mb-4">Excluir Conta</h2>
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>

{{-- Modal de Confirmação - Excluir Perfil Freelancer --}}
@if(auth()->user()->freelancer)
    <x-modal name="confirm-freelancer-deletion" :show="$errors->freelancerDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.freelancer.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Tem certeza que deseja excluir seu perfil de freelancer?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Esta ação é <strong>irreversível</strong>. Todos os dados do seu perfil de freelancer, incluindo currículo, foto e histórico de candidaturas, serão permanentemente excluídos.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Senha') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Senha') }}"
                />
                <x-input-error :messages="$errors->freelancerDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    <i class="fas fa-trash mr-2"></i>
                    {{ __('Excluir Perfil Freelancer') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
@endif

{{-- Modal de Confirmação - Excluir Perfil Empresa --}}
@if(auth()->user()->company)
    <x-modal name="confirm-company-deletion" :show="$errors->companyDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.company.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                Tem certeza que deseja excluir seu perfil de empresa?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Esta ação é <strong>irreversível</strong>. Todos os dados do seu perfil de empresa, incluindo foto, vagas publicadas e histórico, serão permanentemente excluídos.
            </p>

            <div class="mt-6">
                <x-input-label for="company_password" value="{{ __('Senha') }}" class="sr-only" />
                <x-text-input
                    id="company_password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Senha') }}"
                />
                <x-input-error :messages="$errors->companyDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    <i class="fas fa-trash mr-2"></i>
                    {{ __('Excluir Perfil Empresa') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
@endif

@endsection