<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Trampix') }} - Conectando Talentos e Oportunidades</title>
    <meta name="description" content="A plataforma que conecta freelancers talentosos com empresas inovadoras. Encontre projetos incríveis ou contrate os melhores profissionais.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Favicon (Trampix Logo) -->
    <link rel="icon" type="image/png" href="{{ asset('storage/img/logo_trampix.png') }}">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="trampix-bg">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-purple-100 transition-all duration-300" id="header">
        <div class="container mx-auto px-6 py-4">
            <nav class="flex items-center justify-between">
                <a href="{{ route('welcome') }}" class="flex items-center space-x-2 text-xl font-bold text-purple-600">
                    <img src="{{ asset('storage/img/logo_trampix.png') }}" alt="Trampix Logo" class="h-10 object-contain">
                    <span>Trampix</span>
                </a>

                <ul class="hidden md:flex items-center space-x-8">
                    <li><a href="#features" class="text-gray-700 hover:text-purple-600 transition-colors duration-300">Recursos</a></li>
                    <li><a href="#featured" class="text-gray-700 hover:text-purple-600 transition-colors duration-300">Vagas</a></li>
                    <li><a href="#cta" class="text-gray-700 hover:text-purple-600 transition-colors duration-300">Começar</a></li>
                </ul>

                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-trampix-secondary px-4 py-2 text-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-trampix-secondary px-4 py-2 text-sm">Entrar</a>
                        <a href="{{ route('register') }}" class="btn-trampix-primary px-4 py-2 text-sm">Cadastrar</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-purple-50 to-green-50 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%238F3FF7&quot; fill-opacity=&quot;0.03&quot;%3E%3Ccircle cx=&quot;30&quot; cy=&quot;30&quot; r=&quot;4&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="trampix-h1 mb-6">
                    Conectando <span class="text-purple-600">Talentos</span> e
                    <span class="text-green-500">Oportunidades</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                    A plataforma que une freelancers talentosos com empresas inovadoras.
                    Encontre projetos incríveis ou contrate os melhores profissionais do mercado.
                </p>

                <!-- Botão principal para vagas - visível para todos -->
                <div class="mb-6">
                    <a href="{{ route('vagas.index') }}" class="btn-trampix-primary flex items-center space-x-2 px-10 py-5 text-xl font-bold mx-auto w-fit shadow-2xl hover:shadow-purple-500/25 transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-briefcase text-2xl"></i>
                        <span>Ver Vagas Disponíveis</span>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn-trampix-secondary flex items-center space-x-2 px-8 py-4 text-lg">
                            <i class="fas fa-rocket"></i>
                            <span>Começar Agora</span>
                        </a>
                        <a href="#features" class="text-gray-600 hover:text-purple-600 flex items-center space-x-2 px-8 py-4 text-lg transition-colors duration-300">
                            <i class="fas fa-play"></i>
                            <span>Saiba Mais</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-trampix-secondary flex items-center space-x-2 px-8 py-4 text-lg">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Ir para Dashboard</span>
                        </a>
                        <a href="#features" class="text-gray-600 hover:text-purple-600 flex items-center space-x-2 px-8 py-4 text-lg transition-colors duration-300">
                            <i class="fas fa-info-circle"></i>
                            <span>Sobre a Plataforma</span>
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="trampix-section bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="trampix-h2 mb-4">Por que escolher o Trampix?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Uma plataforma completa que facilita a conexão entre profissionais e empresas,
                    com ferramentas modernas e processos otimizados.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Comunidade Ativa</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Conecte-se com freelancers e empresas em uma comunidade colaborativa.
                    </p>
                </div>

                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Segurança</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Verificação de perfis, sistema de avaliações e proteção para as transações.
                    </p>
                </div>

                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Processo Ágil</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Matching rápido e ferramentas de comunicação eficientes.
                    </p>
                </div>

                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Crescimento Profissional</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Projetos desafiadores, feedback e networking qualificado.
                    </p>
                </div>

                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Acesso Mobile</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Plataforma responsiva e otimizada para dispositivos móveis.
                    </p>
                </div>

                <div class="trampix-card text-center p-8 hover:transform hover:scale-105 transition-all duration-500">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-headset text-white text-2xl"></i>
                    </div>
                    <h3 class="trampix-h3 mb-4">Suporte</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Equipe disponível para ajudar a aproveitar todas as funcionalidades.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <section id="featured" class="trampix-section bg-gradient-to-r from-purple-50 to-green-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="trampix-h2 mb-4">Oportunidades em Destaque</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Descubra as melhores vagas disponíveis agora e dê o próximo passo na sua carreira.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <!-- Vaga 1 -->
                <div class="trampix-card p-6 hover:transform hover:scale-105 transition-all duration-300 border-l-4 border-purple-600">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-code text-purple-600"></i>
                        </div>
                        <span class="badge-trampix-success">Remoto</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Desenvolvedor Full Stack</h3>
                    <p class="text-gray-600 text-sm mb-3">React, Node.js, PostgreSQL</p>
                    <p class="text-gray-700 mb-4 line-clamp-3">
                        Buscamos desenvolvedor experiente para projeto de e-commerce inovador.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-purple-600 font-bold">R$ 8.000 - R$ 12.000</span>
                        <span class="text-sm text-gray-500">2 dias atrás</span>
                    </div>
                </div>

                <!-- Vaga 2 -->
                <div class="trampix-card p-6 hover:transform hover:scale-105 transition-all duration-300 border-l-4 border-green-500">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paint-brush text-green-600"></i>
                        </div>
                        <span class="badge-trampix-primary">Híbrido</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Designer UX/UI</h3>
                    <p class="text-gray-600 text-sm mb-3">Figma, Adobe XD, Prototyping</p>
                    <p class="text-gray-700 mb-4 line-clamp-3">
                        Redesign de aplicativo mobile. Experiência em design system é diferencial.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-purple-600 font-bold">R$ 5.000 - R$ 8.000</span>
                        <span class="text-sm text-gray-500">1 dia atrás</span>
                    </div>
                </div>

                <!-- Vaga 3 -->
                <div class="trampix-card p-6 hover:transform hover:scale-105 transition-all duration-300 border-l-4 border-purple-600">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                        <span class="badge-trampix-success">Remoto</span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Analista de Marketing Digital</h3>
                    <p class="text-gray-600 text-sm mb-3">Google Ads, Facebook Ads, Analytics</p>
                    <p class="text-gray-700 mb-4 line-clamp-3">
                        Gestão de performance com foco em ROI.
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-purple-600 font-bold">R$ 4.000 - R$ 7.000</span>
                        <span class="text-sm text-gray-500">3 dias atrás</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ route('vagas.index') }}" class="btn-trampix-primary inline-flex items-center space-x-2 px-8 py-4 text-lg shadow-lg hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-search"></i>
                    <span>Ver Todas as Vagas</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="trampix-section bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="trampix-h2 mb-4">Histórias de sucesso</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Experiências reais de pessoas e empresas de diferentes áreas que encontraram oportunidades no Trampix.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="trampix-card p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Mariana Costa</h4>
                            <p class="text-sm text-gray-600">Fotógrafa Freelancer</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Consegui clientes fora da minha cidade e organizei minha agenda. O Trampix abriu portas que eu nem imaginava."
                    </p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>

                <div class="trampix-card p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-building text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Restaurante Sabor & Arte</h4>
                            <p class="text-sm text-gray-600">Pequena Empresa</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Contratamos designer e fotógrafo para redes sociais de forma simples e segura. Resultados em poucos dias."
                    </p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                </div>

                <div class="trampix-card p-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Lucas Almeida</h4>
                            <p class="text-sm text-gray-600">Editor de Vídeo</p>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Fechei meus primeiros trabalhos na plataforma. Hoje tenho clientes fixos e vivo do que gosto."
                    </p>
                    <div class="flex text-yellow-400 mt-4">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i>
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta" class="trampix-section bg-gradient-to-r from-purple-600 to-purple-800 text-white">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-6">Pronto para começar sua jornada?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto opacity-90">
                Junte-se a profissionais e empresas que já estão transformando suas carreiras e negócios através do Trampix.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-600 text-black font-semibold px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Criar Conta Gratuita</span>
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white hover:bg-white hover:text-purple-600 font-semibold px-8 py-4 rounded-lg transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Já tenho conta</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-600 text-black font-semibold px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Acessar Dashboard</span>
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 text-xl font-bold text-green-500 mb-4">
                        <img src="{{ asset('storage/img/logo_trampix.png') }}" alt="Trampix Logo" class="h-10 object-contain">
                        <span>Trampix</span>
                    </div>
                    <p class="text-gray-400 leading-relaxed">
                        Conectando talentos e oportunidades para colaborações melhores.
                    </p>
                </div>

                <div>
                    <h3 class="text-green-500 font-semibold mb-4">Para Freelancers</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-green-500 transition-colors">Criar Perfil</a></li>
                        <li><a href="{{ route('vagas.index') }}" class="text-gray-400 hover:text-green-500 transition-colors">Buscar Vagas</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Como Funciona</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Dicas de Sucesso</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-green-500 font-semibold mb-4">Para Empresas</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-green-500 transition-colors">Cadastrar Empresa</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Publicar Vaga</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Encontrar Talentos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Planos e Preços</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-green-500 font-semibold mb-4">Suporte</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Central de Ajuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Contato</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Termos de Uso</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition-colors">Política de Privacidade</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} Trampix. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Smooth scrolling and header effects -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Header scroll effect
            const header = document.getElementById('header');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 100) {
                    header.classList.add('bg-white/98', 'shadow-lg');
                    header.classList.remove('bg-white/95');
                } else {
                    header.classList.add('bg-white/95');
                    header.classList.remove('bg-white/98', 'shadow-lg');
                }
            });
        });
    </script>
</body>
</html>
