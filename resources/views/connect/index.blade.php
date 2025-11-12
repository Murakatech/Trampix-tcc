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
<div class="space-y-6" x-data="connectModule({{ isset($selectedJob) && $selectedJob ? $selectedJob->id : 'null' }})">
  <!-- Fluxo de empresa: selecionar uma vaga antes de ver cards -->
  @if(isset($companyVacancies) && $companyVacancies && (!isset($selectedJob) || !$selectedJob))
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
              <a href="{{ route('connect.index', ['job_id' => $job->id]) }}" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Selecionar</a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
  @endif

  <!-- Filtros (placeholders) -->
  <div class="flex items-center gap-3">
    <div class="inline-flex items-center gap-2 text-sm text-gray-600">
      <span class="font-medium">Filtros:</span>
      <span class="px-2 py-1 rounded-full bg-gray-100">Todos</span>
      <span class="px-2 py-1 rounded-full bg-gray-100">Job</span>
      <span class="px-2 py-1 rounded-full bg-gray-100">Freelancer</span>
    </div>
  </div>

  <!-- Card central -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Card -->
    <div class="lg:col-span-2">
      <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-5">
          @if(isset($selectedJob) && $selectedJob)
          <div class="mb-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
              <span class="font-medium">Vaga selecionada:</span> {{ $selectedJob->title }}
              <span class="mx-2">•</span> {{ $selectedJob->company?->display_name ?? $selectedJob->company?->name ?? 'Empresa' }}
            </div>
            <a href="{{ route('connect.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Trocar vaga</a>
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
          <div class="flex items-center gap-3">
            <button :disabled="disabled" @click="decide('rejected')" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50">Rejeitar (A)</button>
            <button :disabled="disabled" @click="decide('saved')" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50">Salvar (S)</button>
            <button :disabled="disabled" @click="decide('liked')" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50">Dar Match (D)</button>
            <div class="ml-auto text-sm text-gray-500">Score: <span x-text="card?.score || '-'">-</span></div>
          </div>
          <!-- Empty-state: nenhuma opção disponível -->
          <div x-show="disabled && !card" class="mt-4 p-3 rounded-md bg-indigo-50 text-indigo-700 text-sm">
            Acabaram as opções para mostrar no momento. Tente novamente mais tarde ou ajuste seus filtros/segmentos.
          </div>
        </div>
      </div>
    </div>

    <!-- Drawer lateral (placeholder) -->
    <aside class="bg-white shadow-sm rounded-lg border border-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-lg font-semibold text-gray-800">Ver detalhes</h3>
        <span class="text-xs text-gray-500">placeholder</span>
      </div>
      <p class="text-sm text-gray-600">Em breve: detalhes completos da vaga ou do freelancer selecionado.</p>
    </aside>
  </div>

  <!-- Help -->
  <div class="text-xs text-gray-500">
    Atalhos: A = Rejeitar, S = Salvar, D = Dar Match.
  </div>

  <!-- Snackbar -->
  <div x-show="snackbar.visible" x-transition class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-4 py-2 rounded-md shadow-lg flex items-center gap-3">
    <span x-text="snackbar.message"></span>
    <template x-if="snackbar.undo">
      <button @click="performUndo()" class="px-2 py-1 rounded bg-white text-gray-900">Desfazer</button>
    </template>
  </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
  function connectModule(selectedJobId = null) {
    return {
      card: null,
      disabled: false,
      cardsShown: 0,
      lastRejectedId: null,
      selectedJobId: selectedJobId,
      snackbar: { visible: false, message: '', undo: false, timer: null },
      endpoints: {
        next: "{{ route('connect.next') }}",
        decide: "{{ route('connect.decide') }}",
      },
      init() {
        // Se a empresa ainda não selecionou uma vaga, não carregar cards
        if (!this.selectedJobId && {{ isset($companyVacancies) && $companyVacancies && (!isset($selectedJob) || !$selectedJob) ? 'true' : 'false' }}) {
          this.disabled = true;
          return;
        }
        this.loadNext();
        // Atalhos de teclado
        window.addEventListener('keydown', (e) => {
          if (e.target && (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA')) return;
          if (e.key.toLowerCase() === 'a') this.decide('rejected');
          if (e.key.toLowerCase() === 's') this.decide('saved');
          if (e.key.toLowerCase() === 'd') this.decide('liked');
        });
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
            this.snackbarShow('Match!', false);
          } else if (action === 'rejected') {
            // habilita undo por 5s
            this.lastRejectedId = this.card.id;
            this.snackbarShow('Rejeitado. Desfazer?', true);
          } else if (action === 'liked') {
            this.snackbarShow('Curtido! Se o outro lado também curtir, vira Match.', false);
          } else if (action === 'saved') {
            this.snackbarShow('Salvo!', false);
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
      }
    }
  }
</script>
@endsection