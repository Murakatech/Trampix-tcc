@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
  <h1 class="text-2xl font-semibold text-gray-800">Conectar</h1>
  <div class="text-sm text-gray-500">
    Novas: —
  </div>
</div>
@endsection

@section('content')
<div class="space-y-6" x-data="connectModule({{ isset($selectedJob) && $selectedJob ? $selectedJob->id : 'null' }}, {{ isset($matchNotice) ? count($matchNotice) : 0 }})">
  <!-- Fluxo de empresa: selecionar uma vaga antes de ver cards -->
  @if(auth()->user()?->isCompany() && isset($companyVacancies) && $companyVacancies && (!isset($selectedJob) || !$selectedJob))
  <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold text-gray-800">Selecione uma vaga para conectar com freelancers</h3>
      <span class="text-xs text-gray-500">Empresa</span>
    </div>
    @if($companyVacancies->count() === 0)
      <p class="text-sm text-gray-600">Nenhuma vaga ativa encontrada. Crie uma vaga para começar.</p>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($companyVacancies as $job)
          <div class="border border-gray-200 rounded-md p-4">
            <div class="font-medium text-gray-800">{{ $job->title }}</div>
            <div class="text-sm text-gray-600">
              <span class="mr-2">Local:</span>{{ $job->company?->location ?? '-' }}
              <span class="mx-2">•</span>{{ $job->location_type ?? '-' }}
            </div>
            <div class="mt-3">
              <a href="{{ route('connect.index', ['job_id' => $job->id]) }}" class="inline-flex items-center px-3 py-2 rounded-md shadow text-black hover:opacity-90" style="background-color:#b8fc64;">Selecionar</a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
  @endif

  <!-- Ver Matches centralizado -->
  @php $isFreelancer = auth()->user()?->isFreelancer(); @endphp
  <div class="flex justify-center">
    <button @click="openMatchesList()" class="px-8 py-3 rounded-full font-semibold shadow-md hover:opacity-90 {{ $isFreelancer ? 'text-white' : 'text-black' }}" style="background-color: {{ $isFreelancer ? '#8F3FF7' : '#b8fc64' }};">Ver Matches</button>
  </div>

  <!-- Notificação de Match -->
  @if(isset($matchNotice) && count($matchNotice))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
      <div class="font-medium text-green-800 mb-2">Novo(s) match(es):</div>
      <ul class="list-disc ml-5">
        @foreach($matchNotice as $n)
          <li class="text-sm"><a href="{{ $n['url'] }}" class="text-green-700 hover:text-green-800">{{ $n['text'] }}</a></li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Estilos auxiliares -->
  <style>
    @keyframes fall { 0% { transform: translateY(-20px) rotate(0deg); } 100% { transform: translateY(100vh) rotate(720deg); opacity: 0; } }
  </style>
  <!-- Conteúdo principal -->
  <div class="flex flex-col items-center gap-6">
    <!-- Card -->
    <div class="max-w-2xl w-full mx-auto">
      <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-5">
          @if(isset($selectedJob) && $selectedJob)
          <div class="mb-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
              <span class="font-medium">Vaga selecionada:</span> {{ $selectedJob->title }}
              <span class="mx-2">•</span> {{ $selectedJob->company?->display_name ?? $selectedJob->company?->name ?? 'Empresa' }}
            </div>
            <a href="{{ route('connect.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md border-2 border-indigo-600 bg-white text-black shadow">
              <i class="fas fa-sync-alt"></i> Trocar vaga
            </a>
          </div>
          @endif
          <div class="flex items-center justify-between mb-2">
            <h2 class="text-xl font-semibold text-gray-800" x-text="cardTitle()">Card</h2>
            <span class="text-xs px-2 py-1 rounded-full bg-indigo-50 text-indigo-600" x-text="cardType()"></span>
          </div>
          <div class="text-gray-600 mb-4">
            <span class="mr-2 font-medium">Local:</span>
            <span x-text="card?.payload?.location || '-'">-</span>
            <span class="mx-2">•</span>
            <span x-text="card?.payload?.mode || '-'">-</span>
            <span class="mx-2">•</span>
            <span x-text="card?.payload?.range || '-'">-</span>
          </div>

          <!-- Skills -->
          <div class="flex flex-wrap gap-2 mb-4">
            <template x-for="skill in (card?.payload?.skills || [])" :key="skill">
              <span class="inline-flex items-center px-2 py-1 rounded-md text-xs bg-gray-100 text-gray-700" x-text="skill"></span>
            </template>
          </div>

          <!-- Summary -->
          <p class="text-gray-700" x-text="card?.payload?.summary || '—'">—</p>
        </div>
        <div class="px-5 pb-5">
          <div class="flex items-center justify-between">
            <button :disabled="disabled" @click="decide('rejected')" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50">Rejeitar (A)</button>
            <button :disabled="disabled" @click="decide('liked')" class="px-5 py-2 rounded-md hover:opacity-90 disabled:opacity-50 {{ $isFreelancer ? 'text-white' : 'text-black' }}" style="background-color: {{ $isFreelancer ? '#8F3FF7' : '#b8fc64' }};">Curtir (D)</button>
          </div>
          <div class="mt-3 text-right text-sm text-gray-500">Score: <span x-text="card?.score || '-'">-</span></div>
          <!-- Empty-state: nenhuma opção disponível -->
          <div x-show="disabled && !card" class="mt-4 p-3 rounded-md bg-indigo-50 text-indigo-700 text-sm">
            Acabaram as opções para mostrar no momento. Tente novamente mais tarde ou ajuste seus filtros/segmentos.
          </div>
        </div>
      </div>
    </div>

    <!-- Filtros abaixo do card -->
    <aside class="w-full max-w-2xl">
      @php $currentMode = session('connect_filter') ?? 'all'; @endphp
      @if(auth()->user()?->isFreelancer())
        <div class="text-xs text-gray-600 mb-2 text-center">Selecione o filtro de mostragem</div>
        <div class="grid grid-cols-2 gap-2">
          <a href="{{ route('connect.index', ['mode' => 'segment']) }}" class="block w-full px-3 py-2 rounded-md shadow" style="background-color:#8F3FF7;color:#fff;">Meu segmento</a>
          <a href="{{ route('connect.index', ['mode' => 'all']) }}" class="block w-full px-3 py-2 rounded-md shadow" style="background-color:#8F3FF7;color:#fff;">Geral</a>
        </div>
      @elseif(auth()->user()?->isCompany())
        @if(isset($selectedJob) && $selectedJob)
          <div class="text-xs text-gray-600 mb-2 text-center">Selecione o filtro de mostragem</div>
          <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('connect.index', ['job_id' => $selectedJob->id, 'mode' => 'segment']) }}" class="block w-full px-3 py-2 rounded-md shadow" style="background-color:#b8fc64;color:#000;">Segmento da vaga</a>
            <a href="{{ route('connect.index', ['job_id' => $selectedJob->id, 'mode' => 'all']) }}" class="block w-full px-3 py-2 rounded-md shadow" style="background-color:#b8fc64;color:#000;">Geral</a>
          </div>
        @else
          <span class="block w-full px-3 py-2 rounded-md border-2 border-gray-300 bg-white text-black">Selecione uma vaga</span>
        @endif
      @endif
    </aside>

    <!-- Matches Overlay acionado pelo botão Ver Matches -->
    <div x-show="matchesOverlay.visible" x-transition class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
      <div class="rounded-xl shadow-2xl w-full max-w-md p-6 mx-6 {{ $isFreelancer ? 'text-white' : 'text-black' }}" style="background-color: {{ $isFreelancer ? '#8F3FF7' : '#b8fc64' }};">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-2xl font-semibold">Seus Matches</h3>
          <button @click="matchesOverlay.visible = false" class="px-3 py-2 rounded-md bg-white text-black hover:bg-white/90">Fechar</button>
        </div>
        <template x-if="(userMatches || []).length === 0">
          <div class="text-sm {{ $isFreelancer ? 'text-white/90' : 'text-black/70' }}">Nenhum match encontrado ainda.</div>
        </template>
        <ul class="space-y-3" x-show="(userMatches || []).length > 0">
          <template x-for="m in userMatches" :key="m.id">
            <li class="flex items-center justify-between">
              <div>
                <div class="font-medium" x-text="m.job_title || m.freelancer_name"></div>
                <div class="text-sm text-gray-600" x-text="m.job_title ? 'Vaga' : 'Freelancer'"></div>
              </div>
              <div class="flex items-center gap-2">
                <a :href="m.job_title ? ('/vagas/' + m.job_id) : ('/profiles/' + (m.user_id || ''))" class="px-3 py-2 rounded-md bg-white text-black hover:bg-white/90 shadow" x-text="m.job_title ? 'Ir para vaga completa' : ('Ir para perfil de ' + (m.freelancer_name || 'Freelancer'))"></a>
              </div>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>

  

  <!-- Snackbar -->
  <div x-show="snackbar.visible" x-transition class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-md shadow-lg flex items-center gap-3">
    <span x-text="snackbar.message"></span>
    <template x-if="snackbar.undo">
      <button @click="performUndo()" class="px-2 py-1 rounded bg-white text-gray-900">Desfazer</button>
    </template>
  </div>
  <div x-show="matchMenu.visible" x-transition class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="w-full max-w-md rounded-2xl shadow-2xl overflow-hidden mx-6">
      <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-md bg-white/20 flex items-center justify-center">
            <i class="fas fa-heart text-white"></i>
          </div>
          <div class="text-2xl font-bold" x-text="matchMenu.title"></div>
        </div>
        <button @click="matchMenu.visible = false" class="px-3 py-2 rounded-md bg-white/20 hover:bg-white/30 text-white">Fechar</button>
      </div>
      <div class="bg-white p-6">
        <div class="space-y-4">
          <template x-for="opt in matchMenu.options" :key="opt.label">
            <a :href="opt.href" class="block px-4 py-3 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 shadow" x-text="opt.label"></a>
          </template>
        </div>
        <div class="mt-6 text-sm text-gray-800">Este menu fecha automaticamente em 30 segundos.</div>
      </div>
    </div>
  </div>
  <!-- Confetti Animation -->
  <div x-show="confetti.visible" class="fixed inset-0 pointer-events-none z-50">
    <div class="absolute inset-0" id="confetti-container"></div>
  </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
  function connectModule(selectedJobId = null, newMatchesCount = 0) {
    return {
      card: null,
      disabled: false,
      cardsShown: 0,
      lastRejectedId: null,
      selectedJobId: selectedJobId,
      newMatchesCount: newMatchesCount,
      companyMustSelectFirst: "{{ auth()->user()?->isCompany() && isset($companyVacancies) && $companyVacancies && (!isset($selectedJob) || !$selectedJob) ? 'true' : 'false' }}" === "true",
      snackbar: { visible: false, message: '', undo: false, timer: null },
      matchMenu: { visible: false, title: '', options: [], timer: null },
      matchesOverlay: { visible: false },
      userMatches: [],
      confetti: { visible: false, timer: null },
      endpoints: {
        next: "{{ route('connect.next') }}",
        decide: "{{ route('connect.decide') }}",
      },
      init() {
        // Se a empresa ainda não selecionou uma vaga, não carregar cards
        if (!this.selectedJobId && this.companyMustSelectFirst) {
          this.disabled = true;
          return;
        }
        this.loadNext();
        // Atalhos de teclado
        window.addEventListener('keydown', (e) => {
          if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
          if (e.key.toLowerCase() === 'a') this.decide('rejected');
          if (e.key.toLowerCase() === 'd') this.decide('liked');
        });
        // Load matches data from server
        try {
          const el = document.getElementById('MATCHES_DATA');
          this.userMatches = el ? JSON.parse(el.textContent || '[]') : [];
        } catch (e) { this.userMatches = []; }
        if ((this.newMatchesCount || 0) > 0) { this.showConfetti(); }
      },
      async loadNext() {
        try {
          const res = await fetch(this.endpoints.next, { headers: { 'Accept': 'application/json' } });
          if (res.status === 204) {
            // vazio ou limite atingido
            this.card = null;
            this.disabled = true;
            this.snackbarShow('Fim dos cards nesta sessão ou sem recomendações no momento.', false);
            return;
          }
          this.card = await res.json();
          this.cardsShown++;
        } catch (err) {
          console.error('Erro ao carregar próximo card', err);
        }
      },
      cardTitle() {
        if (!this.card) return '—';
        const t = this.card?.payload?.title || '—';
        return `${t}`;
      },
      cardType() {
        if (!this.card) return '—';
        return this.card.type === 'job' ? 'Vaga' : 'Freelancer';
      },
      async decide(action) {
        if (!this.card) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
          const res = await fetch(this.endpoints.decide, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ recommendation_id: this.card.id, action, job_vacancy_id: this.selectedJobId }),
          });
          if (!res.ok) {
            this.snackbarShow(`Erro ao enviar decisão (status ${res.status}).`, false);
            return;
          }
          const data = await res.json();
          if (data && data.match) {
            this.showConfetti();
            this.showMatchMenu();
          } else if (action === 'rejected') {
            // habilita undo por 5s
            this.lastRejectedId = this.card.id;
            this.snackbarShow('Rejeitado. Desfazer?', true);
          } else if (action === 'liked') {
            this.snackbarShow('Curtido! Se o outro lado também curtir, vira Match.', false);
          }
        } catch (err) {
          console.error('Erro ao enviar decisão', err);
          this.snackbarShow('Falha na conexão ao enviar a decisão. Verifique sua rede e tente novamente.', false);
        } finally {
          // Puxa próximo
          this.loadNext();
        }
      },
      snackbarShow(message, undo) {
        if (this.snackbar.timer) clearTimeout(this.snackbar.timer);
        this.snackbar.message = message;
        this.snackbar.undo = !!undo;
        this.snackbar.visible = true;
        this.snackbar.timer = setTimeout(() => { this.snackbar.visible = false; this.snackbar.undo = false; }, 5000);
      },
      async performUndo() {
        if (!this.lastRejectedId) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
          await fetch(this.endpoints.decide, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ recommendation_id: this.lastRejectedId, action: 'undo' }),
          });
        } catch (e) { console.error('Falha ao desfazer', e); }
        finally {
          this.snackbar.visible = false;
          this.snackbar.undo = false;
          this.lastRejectedId = null;
        }
      },
      openMatchesList() {
        this.matchesOverlay.visible = true;
      },
      showMatchMenu() {
        this.matchMenu.visible = true; this.matchMenu.title = 'Match!';
        const opts = [];
        if (this.card?.type === 'job' && this.card?.payload?.job_url) { opts.push({ label: 'Ir para vaga completa', href: this.card.payload.job_url }); }
        if (this.card?.type === 'freelancer' && this.card?.payload?.profile_url) { opts.push({ label: 'Ir para perfil de ' + (this.card.payload.title || 'Freelancer'), href: this.card.payload.profile_url }); }
        this.matchMenu.options = opts;
        if (this.matchMenu.timer) clearTimeout(this.matchMenu.timer);
        this.matchMenu.timer = setTimeout(() => { this.matchMenu.visible = false; }, 30000);
      },
      showConfetti() {
        this.confetti.visible = true;
        const container = document.getElementById('confetti-container');
        if (!container) return;
        container.innerHTML = '';
        const colors = ['#8F3FF7','#B9FF66','#3b82f6','#22c55e','#f59e0b','#ef4444'];
        for (let i = 0; i < 80; i++) {
          const conf = document.createElement('div');
          conf.style.position = 'absolute';
          conf.style.width = Math.floor(Math.random()*8 + 6)+'px';
          conf.style.height = conf.style.width;
          conf.style.background = colors[Math.floor(Math.random()*colors.length)];
          conf.style.borderRadius = '50%';
          conf.style.left = Math.floor(Math.random()*100)+'%';
          conf.style.top = '-20px';
          conf.style.opacity = '0.9';
          conf.style.transform = 'translateY(0)';
          conf.style.animation = `fall ${Math.random()*2 + 2}s linear forwards`;
          conf.style.animationDelay = (Math.random()*0.8)+'s';
          container.appendChild(conf);
        }
        if (this.confetti.timer) clearTimeout(this.confetti.timer);
        this.confetti.timer = setTimeout(() => { this.confetti.visible = false; container.innerHTML=''; }, 3500);
      }
    }
  }
</script>
<script id="MATCHES_DATA" type="application/json">@json($userMatches ?? [])</script>
<!-- overlay removido: agora incorporado no componente principal -->
@endsection
