@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Conectar</h1>
    </div>
    <div></div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    @php $isCompany = auth()->user()?->isCompany(); @endphp
    @if(!$isCompany && isset($job) && $job)
        <div class="flex items-center justify-center gap-6">
            <form method="POST" action="{{ route('connect.decide') }}">
                @csrf
                <input type="hidden" name="job_id" value="{{ $job->id }}">
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="btn-trampix-secondary">Rejeitar</button>
            </form>
            <div class="trampix-card w-full max-w-3xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        <div class="inline-block bg-purple-600 text-white rounded px-3 py-1 font-semibold mb-3">{{ $job->title }}</div>
                        <div class="space-y-2 text-sm text-gray-800">
                            <div><strong>Descrição:</strong> {{ \Illuminate\Support\Str::limit($job->description, 220) }}</div>
                            <div>
                                <strong>Valor:</strong>
                                @php
                                    $range = '-';
                                    if ($job->salary_min && $job->salary_max) {
                                        $range = 'R$ '.number_format((float)$job->salary_min, 2, ',', '.').' - R$ '.number_format((float)$job->salary_max, 2, ',', '.');
                                    } elseif ($job->salary_range) {
                                        $range = $job->salary_range;
                                    }
                                @endphp
                                {{ $range }}
                            </div>
                            <div><strong>Email da empresa:</strong> {{ $job->company?->email ?? '-' }}</div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('vagas.show', $job->id) }}" class="btn-trampix-secondary">Ver vaga completa</a>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-gray-900 font-medium mb-2">{{ $job->company?->display_name ?? $job->company?->name ?? 'Empresa' }}</div>
                        @if($job->company?->profile_photo)
                            <img src="{{ asset('storage/'.$job->company->profile_photo) }}" alt="Logo da empresa" class="mx-auto h-32 w-32 object-cover rounded-lg shadow" />
                        @else
                            <div class="mx-auto h-32 w-32 rounded-lg shadow bg-gray-200 flex items-center justify-center text-gray-600 text-xs uppercase">sem logo</div>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('companies.show', $job->company?->id) }}" class="btn-trampix-secondary">Ver perfil da empresa</a>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('connect.decide') }}">
                @csrf
                <input type="hidden" name="job_id" value="{{ $job->id }}">
                <input type="hidden" name="action" value="liked">
                <button type="submit" class="btn-trampix-primary">Curtir</button>
            </form>
        </div>
        <div class="mt-6 text-center">
            <button type="button" onclick="toggleMatchesMenu()" class="btn-trampix-secondary">
                Conexões
                @if(isset($userMatches) && $userMatches && count($userMatches))
                    <span class="ml-2 inline-flex items-center justify-center h-6 w-6 rounded-full bg-purple-100 text-purple-700 text-xs font-semibold">{{ count($userMatches) }}</span>
                @endif
            </button>
        </div>
    @elseif($isCompany && isset($companyJob) && $companyJob && isset($candidate) && $candidate)
        <div class="mb-4 text-center">
            <span class="inline-block text-sm font-medium text-gray-700">Vaga ativa: {{ $companyJob->title }}</span>
            <a href="{{ route('connect.jobs') }}" class="ml-2 inline-flex items-center px-2 py-1 rounded text-black hover:opacity-90" style="background-color: var(--trampix-green);">Trocar vaga</a>
        </div>
        <div class="flex items-center justify-center gap-6">
            <form method="POST" action="{{ route('connect.company.decide') }}">
                @csrf
                <input type="hidden" name="freelancer_id" value="{{ $candidate->id }}">
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="btn-trampix-secondary">Rejeitar</button>
            </form>
            <div class="trampix-card w-full max-w-3xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        @if($candidate->display_name || ($candidate->user && $candidate->user->name))
                            <div class="inline-block rounded px-3 py-1 font-semibold mb-3 text-black" style="background-color: var(--trampix-green);">{{ $candidate->display_name ?? $candidate->user?->name }}</div>
                        @endif
                        <div class="space-y-2 text-sm text-gray-800">
                            @if($candidate->bio)
                                <div><strong>Bio:</strong> {{ \Illuminate\Support\Str::limit($candidate->bio, 220) }}</div>
                            @endif
                            @if($candidate->linkedin_url)
                                <div><strong>LinkedIn:</strong> <a href="{{ $candidate->linkedin_url }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">{{ $candidate->linkedin_url }}</a></div>
                            @endif
                            @if($candidate->whatsapp)
                                <div><strong>WhatsApp:</strong> {{ $candidate->whatsapp }}</div>
                            @endif
                            @if($candidate->cv_url)
                                <div><strong>Currículo:</strong> <a href="{{ route('freelancers.download-cv', $candidate->id) }}" class="text-indigo-600 hover:underline">Download</a></div>
                            @endif
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ $candidate->user ? route('profiles.show', $candidate->user) : '#' }}" class="btn-trampix-secondary">Ir para perfil do Freelancer</a>
                        </div>
                    </div>
                    <div class="text-center">
                        @if($candidate->profile_photo)
                            <img src="{{ asset('storage/'.$candidate->profile_photo) }}" alt="Foto do freelancer" class="mx-auto h-32 w-32 object-cover rounded-lg shadow" />
                        @else
                            <div class="mx-auto h-32 w-32 rounded-lg shadow bg-gray-200 flex items-center justify-center text-gray-600 text-xs uppercase">sem foto</div>
                        @endif
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('connect.company.decide') }}">
                @csrf
                <input type="hidden" name="freelancer_id" value="{{ $candidate->id }}">
                <input type="hidden" name="action" value="liked">
                <button type="submit" class="btn-trampix-company" style="background-color: var(--trampix-green); border: 2px solid var(--trampix-green); color: var(--trampix-black); transition: all .3s ease;" onmouseenter="this.style.backgroundColor='var(--trampix-purple)'; this.style.borderColor='var(--trampix-purple)'; this.style.color='#ffffff'; this.style.transform='translateY(-2px)';" onmouseleave="this.style.backgroundColor='var(--trampix-green)'; this.style.borderColor='var(--trampix-green)'; this.style.color='var(--trampix-black)'; this.style.transform='none';">Curtir</button>
            </form>
        </div>
        <div class="mt-6 text-center">
            <button type="button" onclick="toggleMatchesMenu()" class="btn-trampix-secondary">
                Conexões
                @if(isset($userMatches) && $userMatches && count($userMatches))
                    <span class="ml-2 inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-700 text-xs font-semibold">{{ count($userMatches) }}</span>
                @endif
            </button>
        </div>
    @else
        <div class="flex items-center justify-center">
            <div class="trampix-card w-full max-w-md text-center">
                @php $isCompany = auth()->user()?->isCompany(); $isFreelancer = auth()->user()?->isFreelancer(); @endphp
                @if($isFreelancer && !isset($job))
                    <h3 class="text-lg font-semibold text-gray-900">Nenhuma vaga disponível</h3>
                    <p class="mt-2 text-sm text-gray-700">Sem vagas ativas ou todas já foram curtidas/rejeitadas.</p>
                    <div class="mt-3"><a href="{{ route('vagas.index') }}" class="btn-trampix-secondary">Ver todas as vagas</a></div>
                @elseif($isCompany && (!isset($companyJob) || !$companyJob))
                    <h3 class="text-lg font-semibold text-gray-900">Selecione uma vaga</h3>
                    <p class="mt-2 text-sm text-gray-700">Escolha uma vaga ativa para conectar.</p>
                    <div class="mt-3"><a href="{{ route('connect.jobs') }}" class="btn-trampix-company">Conectar</a></div>
                @elseif($isCompany && isset($companyJob) && !$candidate)
                    <h3 class="text-lg font-semibold text-gray-900">Sem candidatos disponíveis</h3>
                    <p class="mt-2 text-sm text-gray-700">Todos os freelancers desta sessão já foram curtidos ou rejeitados.</p>
                    <div class="mt-3"><a href="{{ route('connect.jobs') }}" class="btn-trampix-company">Trocar vaga</a></div>
                @else
                    <h3 class="text-lg font-semibold text-gray-900">Conectar</h3>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
    .bg-trampix-company { background-color: var(--trampix-green); }
    .bg-trampix-freelancer { background-color: #8F3FF7; }
</style>
<div id="matchesMenu" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
    @php $isCompany = auth()->user()?->isCompany(); @endphp
    <div class="w-full max-w-md mx-auto rounded-lg shadow-2xl overflow-hidden">
        <div class="px-4 py-3 flex items-center justify-between {{ $isCompany ? 'bg-trampix-company' : 'bg-trampix-freelancer' }}">
            <h3 class="text-lg font-semibold text-black">Conexões</h3>
            <button class="btn-trampix-secondary" onclick="toggleMatchesMenu()">Fechar</button>
        </div>
        <div class="bg-white p-4 space-y-3">
            @if(isset($userMatches) && $userMatches && count($userMatches))
                @foreach($userMatches as $m)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(!$isCompany)
                                @php $logo = $m->company_logo ? asset('storage/'.$m->company_logo) : null; @endphp
                                @if($logo)
                                    <img src="{{ $logo }}" class="h-8 w-8 rounded-full object-cover" alt="logo">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-[10px] uppercase">sem logo</div>
                                @endif
                                <span class="text-sm">{{ $m->company_name ?? 'Empresa' }} — {{ $m->job_title }}</span>
                            @else
                                @php $photo = $m->freelancer_photo ? asset('storage/'.$m->freelancer_photo) : null; @endphp
                                @if($photo)
                                    <img src="{{ $photo }}" class="h-8 w-8 rounded-full object-cover" alt="foto">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-[10px] uppercase">sem foto</div>
                                @endif
                                <span class="text-sm">{{ $m->freelancer_name }} — {{ $m->job_title }}</span>
                            @endif
                        </div>
                        @if(!$isCompany)
                            <a href="{{ route('companies.show', $m->company_id) }}" class="btn-trampix-secondary">Ver perfil</a>
                        @else
                            <a href="{{ route('profiles.show', $m->user_id) }}" class="btn-trampix-secondary">Ver perfil</a>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-sm text-gray-700">Sem conexões ainda.</div>
            @endif
        </div>
    </div>
</div>

@if(isset($confetti) && $confetti)
<div id="confetti-overlay" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute inset-0" id="confetti-container"></div>
    </div>
    <div class="trampix-card shadow-2xl max-w-md w-full text-center">
        <h3 class="text-xl font-bold mb-2">Match!</h3>
        @if(isset($confettiMessage) && $confettiMessage)
            <div class="text-sm text-gray-800">{{ $confettiMessage }}</div>
        @elseif(isset($newMatches) && $newMatches && count($newMatches))
            <div class="space-y-1 text-sm text-gray-800">
                @foreach($newMatches as $msg)
                    <div>{{ $msg }}</div>
                @endforeach
            </div>
        @else
            <div class="text-sm text-gray-800">Você se conectou!</div>
        @endif
        <div class="mt-4">
            <button class="btn-trampix-secondary" onclick="dismissConfetti()">Fechar</button>
        </div>
    </div>
</div>
<script>
(function(){
    const container = document.getElementById('confetti-container');
    if (!container) return;
    container.innerHTML = '';
    const colors = ['#8F3FF7','#B9FF66','#3b82f6','#22c55e','#f59e0b','#ef4444'];
    for (let i = 0; i < 100; i++) {
      const conf = document.createElement('div');
      conf.style.position = 'absolute';
      conf.style.width = Math.floor(Math.random()*8 + 6)+'px';
      conf.style.height = conf.style.width;
      conf.style.background = colors[Math.floor(Math.random()*colors.length)];
      conf.style.borderRadius = '50%';
      conf.style.left = Math.floor(Math.random()*100)+'%';
      conf.style.top = '-20px';
      conf.style.opacity = '0.9';
      container.appendChild(conf);
      const duration = Math.random()*2000 + 2500;
      const translateY = window.innerHeight + 100;
      conf.animate([
        { transform: 'translateY(0)' },
        { transform: 'translateY('+translateY+'px)' }
      ], { duration, easing: 'ease-out' });
    }
    setTimeout(()=>{ const overlay=document.getElementById('confetti-overlay'); if(overlay){ overlay.style.display='none'; } }, 3500);
})();
function dismissConfetti(){
  const overlay = document.getElementById('confetti-overlay');
  if (overlay) overlay.style.display='none';
}
</script>
@endif

<script>
function toggleMatchesMenu(){
  const el = document.getElementById('matchesMenu');
  if (!el) return;
  el.style.display = (el.style.display === 'none' || !el.style.display) ? 'block' : 'none';
}
</script>
@endsection
