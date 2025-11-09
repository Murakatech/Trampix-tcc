<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar com Componentes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Container com Alpine.js -->
    <div x-data="{ open: true }">
        
        <!-- Sidebar usando componente -->
        <x-minimal-sidebar title="Trampix" description="Sidebar com componentes">
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-700">Menu</h3>
                    <p class="text-sm text-gray-500 mt-1">Itens do menu aqui</p>
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-medium text-blue-700">Configurações</h3>
                    <p class="text-sm text-blue-500 mt-1">Área de configurações</p>
                </div>
            </div>
        </x-minimal-sidebar>

        <!-- Overlay para mobile -->
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
            class="transition-all duration-300 ease-in-out p-8"
            :class="open ? 'lg:ml-64' : 'ml-0'"
        >
            <!-- Header com botão toggle -->
            <header class="mb-8">
                <div class="flex items-center gap-4">
                    <x-sidebar-toggle icon="svg" />
                    <h1 class="text-2xl font-bold text-gray-800">Sidebar com Componentes</h1>
                </div>
            </header>
            
            <!-- Conteúdo -->
            <div class="bg-white p-8 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Demonstração dos Componentes</h2>
                
                <div class="space-y-4 text-gray-600">
                    <p>✅ <strong>Componente Sidebar:</strong> <code>&lt;x-minimal-sidebar&gt;</code></p>
                    <p>✅ <strong>Componente Toggle:</strong> <code>&lt;x-sidebar-toggle&gt;</code></p>
                    <p>✅ <strong>Alpine.js:</strong> Estado controlado com <code>x-data="{ open: true }"</code></p>
                    <p>✅ <strong>Animação:</strong> Transição suave de 300ms</p>
                    <p>✅ <strong>Responsivo:</strong> Overlay em telas pequenas</p>
                </div>

                <div class="mt-6 p-4 bg-green-50 rounded-lg">
                    <h3 class="font-semibold text-green-800 mb-2">Como usar:</h3>
                    <pre class="text-sm text-green-700 bg-green-100 p-3 rounded overflow-x-auto"><code>&lt;div x-data="{ open: true }"&gt;
    &lt;x-minimal-sidebar title="Meu App" description="Descrição"&gt;
        &lt;!-- Conteúdo da sidebar --&gt;
    &lt;/x-minimal-sidebar&gt;
    
    &lt;x-sidebar-toggle icon="svg" /&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </main>
    </div>

</body>
</html>