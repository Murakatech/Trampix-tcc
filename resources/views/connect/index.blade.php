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
<div class="space-y-6" x-data="connectModule()">
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
            <button @click="decide('rejected')" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Rejeitar (A)</button>
            <button @click="decide('saved')" class="px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Salvar (S)</button>
            <button @click="decide('liked')" class="px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Dar Match (D)</button>
            <div class="ml-auto text-sm text-gray-500">Score: <span x-text="card?.score || '-'">-</span></div>
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
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
  function connectModule() {
    return {
      card: null,
      endpoints: {
        next: "{{ route('connect.next') }}",
        decide: "{{ route('connect.decide') }}",
      },
      init() {
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
          this.card = await res.json();
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
          await fetch(this.endpoints.decide, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({ recommendation_id: this.card.id, action }),
          });
        } catch (err) {
          console.error('Erro ao enviar decisão', err);
        } finally {
          // Puxa próximo
          this.loadNext();
        }
      }
    }
  }
</script>
@endsection