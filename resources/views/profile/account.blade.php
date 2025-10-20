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

        {{-- Excluir Conta --}}
        <div class="trampix-card">
            <div class="p-6">
                <h2 class="text-lg font-medium text-red-600 mb-4">Excluir Conta</h2>
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>
@endsection