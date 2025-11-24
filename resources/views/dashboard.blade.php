@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
@php
    $activeRole = session('active_role') ?? (auth()->user()->isCompany() ? 'company' : (auth()->user()->isFreelancer() ? 'freelancer' : null));
@endphp
<div class="container mx-auto px-4 py-6">
    <!-- Header de Boas-vindas -->
    <div class="mb-8">
        <h1 class="trampix-h1 {{ $activeRole === 'company' ? 'company-title-color' : '' }}">
            Olá, 
            <span class="trampix-user-name">
                {{ auth()->user()->display_name }}
            </span>
        </h1>
        <p class="text-gray-600 mt-2">
            @if(auth()->user()->isAdmin())
                Bem-vindo ao painel administrativo do Trampix
            @elseif(auth()->user()->isCompany())
                Gerencie suas vagas e candidaturas
            @elseif(auth()->user()->isFreelancer())
                Encontre as melhores oportunidades para você
            @else
                Complete seu perfil para começar a usar o Trampix
            @endif
        </p>
    </div>

    <!-- Conteúdo Dinâmico baseado no tipo de usuário -->
    @if($activeRole === 'company')
        @include('dashboard.partials.company')
    @elseif($activeRole === 'freelancer')
        @include('dashboard.partials.freelancer')
    @else
        <!-- Usuário sem perfil definido -->
        <div class="text-center py-12">
            <div class="trampix-card max-w-md mx-auto">
                <div class="text-center">
                    <i class="fas fa-user-plus text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Complete seu perfil</h3>
                    <p class="text-gray-600 mb-6">Para começar a usar o Trampix, você precisa definir seu tipo de perfil.</p>
                    <div class="space-y-3">
                        <a href="{{ route('profile.selection') }}" class="btn-trampix-primary w-full">
                            Completar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Informações do Sistema -->
    <section class="pt-6 border-t border-gray-200 mt-8">
        <div class="text-center">
            <small class="text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Dashboard Trampix - Navegação intuitiva e dinâmica
            </small>
        </div>
    </section>
</div>
<a href="{{ route('connect.index') }}" class="rounded-full shadow-2xl btn-connect {{ $activeRole === 'company' ? 'btn-connect-company' : 'btn-connect-freelancer' }}" aria-label="Ir para Conectar"><i class="fa-solid fa-share-nodes"></i><span>Conectar</span></a>
<button type="button" class="rounded-full shadow-2xl" style="position: fixed; bottom: 24px; right: 160px; z-index: 9999; height: 48px; width: 48px; background-color: #3b82f6; border: 2px solid #3b82f6; color: #ffffff; transition: all .3s ease; display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px;" onmouseenter="this.style.backgroundColor='#2563eb'; this.style.borderColor='#2563eb'; this.style.transform='translateY(-2px)';" onmouseleave="this.style.backgroundColor='#3b82f6'; this.style.borderColor='#3b82f6'; this.style.transform='none';" onclick="(function(){var t=document.getElementById('help-toast');var email='sac@trampix.com';if(!t){t=document.createElement('div');t.id='help-toast';t.style.position='fixed';t.style.bottom='96px';t.style.right='160px';t.style.background='#111827';t.style.color='#ffffff';t.style.padding='12px 16px';t.style.borderRadius='12px';t.style.boxShadow='0 10px 25px rgba(0,0,0,.15)';t.style.zIndex='9999';t.style.fontWeight='500';t.style.transition='opacity .3s ease';t.style.maxWidth='480px';t.style.lineHeight='1.5';t.innerHTML='Dúvidas? Entre em contato em: <span id=\'help-email\' style=\'color:#93c5fd; user-select:text;\'> '+email+' </span> <button id=\'help-copy\' style=\'margin-left:10px; background:#2563eb; color:#ffffff; border:0; padding:6px 10px; border-radius:8px; cursor:pointer;\'>Copiar</button>';document.body.appendChild(t);var b=document.getElementById('help-copy');if(b&&navigator.clipboard){b.onclick=function(){navigator.clipboard.writeText(email).then(function(){var c=document.getElementById('help-copied');if(!c){c=document.createElement('div');c.id='help-copied';c.style.position='fixed';c.style.bottom='64px';c.style.right='160px';c.style.background='#111827';c.style.color='#ffffff';c.style.padding='8px 12px';c.style.borderRadius='10px';c.style.boxShadow='0 10px 25px rgba(0,0,0,.15)';c.style.zIndex='9999';c.style.fontWeight='500';c.textContent='Copiado para a área de transferência';document.body.appendChild(c);setTimeout(function(){c.style.opacity='0';setTimeout(function(){if(c&&c.parentNode){c.parentNode.removeChild(c);}},300);},2500);}});};}setTimeout(function(){t.style.opacity='0';setTimeout(function(){if(t&&t.parentNode){t.parentNode.removeChild(t);}},300);},15000);}else{t.style.opacity='1';}})();" aria-label="Ajuda"><i class="fa-solid fa-circle-question"></i></button>
<style>
    .company-title-color { color: #1ca751; }
    .btn-connect { position: fixed; bottom: 24px; right: 240px; z-index: 9999; height: 48px; padding: 0 18px; color: #ffffff; transition: all .3s ease; display: inline-flex; align-items: center; gap: 10px; border-radius: 9999px; }
    .btn-connect-company { background-color: #1ca751; border: 2px solid #1ca751; }
    .btn-connect-company:hover { background-color: var(--trampix-purple); border-color: var(--trampix-purple); transform: translateY(-2px); }
    .btn-connect-freelancer { background-color: var(--trampix-purple); border: 2px solid var(--trampix-purple); }
    .btn-connect-freelancer:hover { background-color: #1ca751; border-color: #1ca751; transform: translateY(-2px); }
</style>
@endsection
