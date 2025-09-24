<x-app-layout>
    <div class="max-w-2xl mx-auto text-center py-20">
        <h1 class="text-4xl font-bold text-red-600 mb-6">Acesso negado</h1>
        <p class="mb-6">Você não tem permissão para acessar esta página.</p>
        <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 text-white rounded">
            Voltar para Home
        </a>
    </div>
</x-app-layout>
