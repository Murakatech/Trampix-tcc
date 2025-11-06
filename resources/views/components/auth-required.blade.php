<div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/50"></div>
    <div class="relative bg-white rounded-xl shadow-2xl p-8 max-w-md w-full text-center">
        <div class="mb-4 text-red-600">
            <i class="fas fa-lock text-3xl"></i>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Você não tem permissão para acessar esta página</h2>
        <p class="text-gray-600 mb-6">Para continuar, será necessário realizar login.</p>
        <a href="{{ route('login') }}" class="btn-trampix-primary inline-flex items-center justify-center">
            <i class="fas fa-sign-in-alt mr-2"></i> Fazer Login
        </a>
    </div>
</div>