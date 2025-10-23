<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste Sidebar Minimalista</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Container principal com Alpine.js -->
    <div x-data="{ open: true }" class="relative">
        
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 h-screen w-64 bg-white shadow-md transform transition-transform duration-300 ease-in-out z-50"
            :class="open ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Cabeçalho da Sidebar -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Trampix</h2>
                <p class="text-sm text-gray-500 mt-1">Sidebar Minimalista</p>
            </div>
            
            <!-- Conteúdo da Sidebar -->
            <div class="p-6">
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-700">Menu Principal</h3>
                        <p class="text-sm text-gray-500 mt-1">Itens de menu serão adicionados aqui</p>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-medium text-blue-700">Configurações</h3>
                        <p class="text-sm text-blue-500 mt-1">Área de configurações</p>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-medium text-green-700">Status</h3>
                        <p class="text-sm text-green-500 mt-1">Sistema online</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay para dispositivos móveis -->
        <div 
            x-show="open" 
            @click="open = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <!-- Conteúdo Principal -->
        <main 
            class="transition-all duration-300 ease-in-out"
            :class="open ? 'lg:ml-64' : 'ml-0'"
        >
            <!-- Barra superior com botão toggle -->
            <header class="bg-white shadow-sm border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <!-- Botão Toggle da Sidebar -->
                    <button 
                        @click="open = !open"
                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        aria-label="Toggle Sidebar"
                    >
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <h1 class="text-xl font-semibold text-gray-800">Teste da Sidebar</h1>
                    
                    <div class="w-10"></div> <!-- Spacer para centralizar o título -->
                </div>
            </header>

            <!-- Área de conteúdo -->
            <div class="p-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-lg shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Sidebar Funcional Minimalista</h2>
                        
                        <div class="space-y-4 text-gray-600">
                            <p>✅ <strong>Tecnologia:</strong> Alpine.js + TailwindCSS</p>
                            <p>✅ <strong>Estrutura:</strong> &lt;aside&gt; fixo à esquerda com classes especificadas</p>
                            <p>✅ <strong>Estado:</strong> Controlado por Alpine.js com <code>x-data="{ open: true }"</code></p>
                            <p>✅ <strong>Animação:</strong> Transição suave com <code>transform transition-transform duration-300 ease-in-out</code></p>
                            <p>✅ <strong>Comportamento:</strong> 
                                <br>• <code>open = true</code> → visível (<code>translate-x-0</code>)
                                <br>• <code>open = false</code> → escondida (<code>-translate-x-full</code>)
                            </p>
                            <p>✅ <strong>Botão Toggle:</strong> Ícone de menu (☰) que alterna o estado</p>
                            <p>✅ <strong>Responsividade:</strong> Funciona em telas grandes e pequenas</p>
                        </div>

                        <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-blue-800 mb-2">Como testar:</h3>
                            <ul class="text-blue-700 space-y-1 text-sm">
                                <li>• Clique no botão ☰ no canto superior esquerdo</li>
                                <li>• A sidebar deve aparecer/desaparecer com animação suave</li>
                                <li>• Em telas pequenas, um overlay escuro aparece</li>
                                <li>• O conteúdo principal se ajusta automaticamente</li>
                            </ul>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-700">Card 1</h4>
                                <p class="text-sm text-gray-500 mt-1">Conteúdo de exemplo</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-700">Card 2</h4>
                                <p class="text-sm text-gray-500 mt-1">Conteúdo de exemplo</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-700">Card 3</h4>
                                <p class="text-sm text-gray-500 mt-1">Conteúdo de exemplo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Script adicional para demonstração -->
    <script>
        // Log para demonstrar que o Alpine.js está funcionando
        document.addEventListener('alpine:init', () => {
            console.log('✅ Alpine.js inicializado com sucesso!');
        });
    </script>
</body>
</html>