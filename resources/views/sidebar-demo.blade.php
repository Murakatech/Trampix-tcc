<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demonstração Sidebar Minimalista</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-800 mb-4">Sidebar Minimalista</h1>
                <p class="text-xl text-gray-600">Implementação funcional com Alpine.js + TailwindCSS</p>
            </div>

            <!-- Especificações -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">✅ Especificações Atendidas</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Tecnologia</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li>✅ Alpine.js para reatividade</li>
                            <li>✅ TailwindCSS para estilização</li>
                            <li>✅ Componentes Blade reutilizáveis</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Estrutura</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li>✅ <code>&lt;aside&gt;</code> fixo à esquerda</li>
                            <li>✅ Classes: <code>fixed inset-y-0 left-0 h-screen w-64</code></li>
                            <li>✅ Background branco com sombra</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Estado Alpine.js</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li>✅ <code>x-data="{ open: true }"</code></li>
                            <li>✅ Toggle: <code>open = !open</code></li>
                            <li>✅ Controle de visibilidade</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">Animação</h3>
                        <ul class="space-y-2 text-gray-600">
                            <li>✅ <code>translate-x-0</code> (visível)</li>
                            <li>✅ <code>-translate-x-full</code> (escondida)</li>
                            <li>✅ Transição suave 300ms</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Demonstrações -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                
                <!-- Versão Completa -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Versão Completa</h3>
                        <p class="text-sm text-gray-600 mt-2">Implementação completa com overlay mobile e responsividade</p>
                    </div>
                    <a href="/sidebar-test" 
                       class="block w-full bg-blue-500 text-white text-center py-3 rounded-lg hover:bg-blue-600 transition-colors">
                        Ver Demonstração
                    </a>
                </div>

                <!-- Versão Minimalista -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Versão Minimalista</h3>
                        <p class="text-sm text-gray-600 mt-2">Implementação mais simples, focada nos requisitos essenciais</p>
                    </div>
                    <a href="/sidebar-minimal" 
                       class="block w-full bg-green-500 text-white text-center py-3 rounded-lg hover:bg-green-600 transition-colors">
                        Ver Demonstração
                    </a>
                </div>

                <!-- Versão com Componentes -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Com Componentes</h3>
                        <p class="text-sm text-gray-600 mt-2">Usando componentes Blade reutilizáveis</p>
                    </div>
                    <a href="/sidebar-components" 
                       class="block w-full bg-purple-500 text-white text-center py-3 rounded-lg hover:bg-purple-600 transition-colors">
                        Ver Demonstração
                    </a>
                </div>
            </div>

            <!-- Código de Exemplo -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">📝 Código de Exemplo</h2>
                
                <div class="bg-gray-900 rounded-lg p-6 overflow-x-auto">
                    <pre class="text-green-400 text-sm"><code>&lt;!-- Container com Alpine.js --&gt;
&lt;div x-data="{ open: true }"&gt;
    
    &lt;!-- Sidebar --&gt;
    &lt;aside 
        class="fixed inset-y-0 left-0 h-screen w-64 bg-white shadow-md 
               transform transition-transform duration-300 ease-in-out"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    &gt;
        &lt;div class="p-6"&gt;
            &lt;h2 class="text-lg font-semibold"&gt;Sidebar&lt;/h2&gt;
        &lt;/div&gt;
    &lt;/aside&gt;

    &lt;!-- Botão Toggle --&gt;
    &lt;button @click="open = !open"&gt;
        ☰ Toggle Sidebar
    &lt;/button&gt;
    
&lt;/div&gt;</code></pre>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-12">
                <p class="text-gray-600">
                    Sidebar funcional criada com ❤️ usando Alpine.js + TailwindCSS
                </p>
            </div>
        </div>
    </div>

</body>
</html>