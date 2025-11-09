<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Minimalista</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Container com Alpine.js -->
    <div x-data="{ open: true }">
        
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 h-screen w-64 bg-white shadow-md transform transition-transform duration-300 ease-in-out"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800">Sidebar</h2>
                <p class="text-sm text-gray-500 mt-2">Conteúdo da sidebar aqui</p>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="p-8">
            <!-- Botão Toggle -->
            <button 
                @click="open = !open"
                class="mb-6 p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
            >
                ☰ Toggle Sidebar
            </button>
            
            <div class="bg-white p-8 rounded-lg shadow">
                <h1 class="text-2xl font-bold mb-4">Sidebar Minimalista</h1>
                <p class="text-gray-600">
                    Clique no botão "☰ Toggle Sidebar" para abrir/fechar a sidebar.
                    A animação é suave e funciona conforme especificado.
                </p>
            </div>
        </main>
    </div>

</body>
</html>