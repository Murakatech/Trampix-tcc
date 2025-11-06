<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full w-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Escolha seu Perfil</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('storage/img/logo_trampix.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen w-full m-0 p-0 overflow-y-auto bg-gray-50 font-sans antialiased">
        
        <!-- Container principal com Alpine.js -->
        <div class="flex w-full min-h-screen overflow-y-auto" x-data="{ activeProfile: null, animatingFreelancer: false, animatingCompany: false }">
            
            <!-- Logo e Nome Trampix no Centro -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20 text-center">
                <a href="{{ route('welcome') }}" class="block bg-white/90 backdrop-blur-sm rounded-2xl p-6 shadow-2xl border border-white/20 hover:bg-white/95 transition-all duration-300 transform hover:scale-105">
                    <x-application-logo class="w-16 h-16 mx-auto mb-3" />
                    <h1 class="text-3xl font-bold text-gray-800">Trampix</h1>
                    <p class="text-gray-600 text-sm">Conectando talentos</p>
                </a>
            </div>
            
            <!-- Lado Esquerdo - Freelancer -->
            <div class="w-1/2 bg-gradient-to-br from-purple-600 to-purple-800 flex flex-col justify-center items-center text-white relative border-r border-white/20 shadow-lg transition-all duration-300">
                
                <!-- Card Freelancer -->
                <div x-show="activeProfile === null || activeProfile === 'company'" 
                     :class="{ 
                         'opacity-100 z-10 pointer-events-auto': activeProfile === null,
                         'opacity-0 z-0 pointer-events-none': activeProfile === 'freelancer',
                         'opacity-100 z-10 pointer-events-auto': activeProfile === 'company'
                     }"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="text-center px-8 absolute inset-0 flex flex-col justify-center items-center transition-all duration-300 overflow-y-auto py-12">
                    
                    <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-5xl xl:text-6xl font-bold mb-8">Sou Freelancer</h2>
                    <p class="text-purple-100 mb-12 text-xl leading-relaxed max-w-lg mx-auto">
                        Ofereça seus serviços, encontre projetos incríveis e construa sua carreira como profissional independente.
                    </p>
                    
                    <ul class="text-left text-purple-100 space-y-6 mb-16 text-lg max-w-md mx-auto">
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Crie seu portfólio profissional
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Encontre projetos que combinam com você
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Receba pagamentos seguros
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-green-400 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Gerencie seus clientes
                        </li>
                    </ul>
                    
                    <button @click="animatingFreelancer = true; setTimeout(() => { activeProfile = 'freelancer'; animatingFreelancer = false; }, 150)" 
                            :disabled="animatingFreelancer"
                            :class="{ 'pointer-events-none opacity-50': animatingFreelancer }"
                            class="inline-block bg-white text-purple-700 font-bold py-6 px-12 rounded-2xl hover:bg-green-400 hover:text-gray-900 transition-all duration-300 transform hover:scale-105 shadow-xl text-xl min-w-[280px]">
                        Começar como Freelancer
                    </button>
                </div>

                <!-- Formulário Freelancer -->
                <div x-show="activeProfile === 'freelancer'" 
                     x-transition:enter="transition ease-out duration-300 delay-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="w-full max-w-md mx-auto px-8 py-6 bg-white/10 backdrop-blur-sm rounded-2xl z-20 relative max-h-[85vh] overflow-y-auto">
                    
                    <h3 class="text-3xl font-bold mb-6 text-center">Criar Perfil Freelancer</h3>
                    
                    <form method="POST" action="{{ route('freelancers.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-purple-100 mb-2">Nome Profissional *</label>
                            <input type="text" id="display_name" name="display_name" value="{{ old('display_name') }}" required class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Como você quer ser conhecido profissionalmente">
                            @error('display_name')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="bio" class="block text-sm font-medium text-purple-100 mb-2">Biografia Profissional *</label>
                            <textarea id="bio" name="bio" rows="3" required class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Descreva sua experiência, habilidades e especialidades (mínimo 30 caracteres)">{{ old('bio') }}</textarea>
                            @error('bio')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="portfolio_url" class="block text-sm font-medium text-purple-100 mb-2">URL do Portfólio</label>
                            <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="https://meuportfolio.com">
                            @error('portfolio_url')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        

                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-purple-100 mb-2">WhatsApp *</label>
                            <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required data-mask="br-phone" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Ex: (16) 99999-9999">
                            @error('whatsapp')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-purple-100 mb-2">Localização</label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="São Paulo, SP">
                            @error('location')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hourly_rate" class="block text-sm font-medium text-purple-100 mb-2">Valor por Hora (R$)</label>
                            <input type="number" step="0.01" min="0" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="50.00">
                            @error('hourly_rate')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="availability" class="block text-sm font-medium text-purple-100 mb-2">Disponibilidade</label>
                            <select id="availability" name="availability" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-white/50">
                                <option value="">Selecione sua disponibilidade</option>
                                <option value="full_time" {{ old('availability') == 'full_time' ? 'selected' : '' }}>Tempo Integral</option>
                                <option value="part_time" {{ old('availability') == 'part_time' ? 'selected' : '' }}>Meio Período</option>
                                <option value="project_based" {{ old('availability') == 'project_based' ? 'selected' : '' }}>Por Projeto</option>
                                <option value="hourly" {{ old('availability') == 'hourly' ? 'selected' : '' }}>Por Hora</option>
                                <option value="weekends" {{ old('availability') == 'weekends' ? 'selected' : '' }}>Fins de Semana</option>
                            </select>
                            @error('availability')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex space-x-4 pt-4">
                            <button type="button" 
                                    @click="animatingFreelancer = true; setTimeout(() => { activeProfile = null; animatingFreelancer = false; }, 150)" 
                                    :disabled="animatingFreelancer"
                                    :class="{ 'pointer-events-none opacity-50': animatingFreelancer }"
                                    class="flex-1 bg-white/20 text-white font-medium py-3 px-4 rounded-lg hover:bg-white/30 transition-colors duration-300">
                                Voltar
                            </button>
                            <button type="submit" class="flex-1 bg-white text-purple-700 font-bold py-3 px-4 rounded-lg hover:bg-green-400 hover:text-gray-900 transition-all duration-300">
                                Criar Perfil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Botão flutuante para alternar para Empresa -->
                <button x-show="activeProfile === 'company'" 
                        @click="animatingCompany = true; setTimeout(() => { activeProfile = null; animatingCompany = false; }, 150)"
                        :disabled="animatingCompany"
                        :class="{ 'pointer-events-none opacity-50': animatingCompany }"
                        x-transition:enter="transition ease-out duration-300 delay-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-4"
                        class="absolute top-8 left-8 bg-white/20 backdrop-blur-sm text-white p-4 rounded-full hover:bg-white/30 transition-all duration-300 shadow-lg z-30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </button>
            </div>

            <!-- Lado Direito - Empresa -->
            <div class="w-1/2 bg-gradient-to-br from-green-500 to-green-700 flex flex-col justify-center items-center text-white relative transition-all duration-300">
                
                <!-- Card Empresa -->
                <div x-show="activeProfile === null || activeProfile === 'freelancer'" 
                     :class="{ 
                         'opacity-100 z-10 pointer-events-auto': activeProfile === null,
                         'opacity-0 z-0 pointer-events-none': activeProfile === 'company',
                         'opacity-100 z-10 pointer-events-auto': activeProfile === 'freelancer'
                     }"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="text-center px-8 absolute inset-0 flex flex-col justify-center items-center transition-all duration-300 overflow-y-auto py-12">
                    
                    <div class="w-32 h-32 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-8 backdrop-blur-sm">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-5xl xl:text-6xl font-bold mb-8">Sou Empresa</h2>
                    <p class="text-green-100 mb-12 text-xl leading-relaxed max-w-lg mx-auto">
                        Encontre os melhores profissionais para seus projetos e faça sua empresa crescer com talentos qualificados.
                    </p>
                    
                    <ul class="text-left text-green-100 space-y-6 mb-16 text-lg max-w-md mx-auto">
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-purple-300 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Publique projetos e vagas
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-purple-300 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Acesse milhares de freelancers
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-purple-300 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Gerencie projetos com facilidade
                        </li>
                        <li class="flex items-center">
                            <svg class="w-6 h-6 text-purple-300 mr-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Encontre talentos especializados
                        </li>
                    </ul>
                    
                    <button @click="animatingCompany = true; setTimeout(() => { activeProfile = 'company'; animatingCompany = false; }, 150)" 
                            :disabled="animatingCompany"
                            :class="{ 'pointer-events-none opacity-50': animatingCompany }"
                            class="inline-block bg-white text-green-700 font-bold py-6 px-12 rounded-2xl hover:bg-purple-400 hover:text-white transition-all duration-300 transform hover:scale-105 shadow-xl text-xl min-w-[280px]">
                        Começar como Empresa
                    </button>
                </div>

                <!-- Formulário Empresa -->
                <div x-show="activeProfile === 'company'" 
                     x-transition:enter="transition ease-out duration-300 delay-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="w-full max-w-md mx-auto px-8 py-6 bg-white/10 backdrop-blur-sm rounded-2xl z-20 relative max-h-[85vh] overflow-y-auto">
                    
                    <h3 class="text-3xl font-bold mb-6 text-center">Criar Perfil Empresa</h3>
                    
                    <form method="POST" action="{{ route('companies.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-green-100 mb-2">Nome da Empresa *</label>
                            <input type="text" id="display_name" name="display_name" value="{{ old('display_name') }}" required class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Como sua empresa será exibida">
                            @error('display_name')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-green-100 mb-2">Descrição *</label>
                            <textarea id="description" name="description" rows="3" required class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Descreva sua empresa e suas atividades (mínimo 30 caracteres)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="website" class="block text-sm font-medium text-green-100 mb-2">Website</label>
                            <input type="url" id="website" name="website" value="{{ old('website') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="https://minhaempresa.com">
                            @error('website')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-green-100 mb-2">Telefone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="(11) 3333-4444">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="employees_count" class="block text-sm font-medium text-green-100 mb-2">Número de Funcionários</label>
                            <input type="number" min="1" id="employees_count" name="employees_count" value="{{ old('employees_count') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="10">
                            @error('employees_count')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categorias de Serviços -->
                        <div>
                            <label class="block text-sm font-medium text-green-100 mb-3">Áreas de Atuação</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto">
                                @foreach(\App\Models\ServiceCategory::where('is_active', true)->orderBy('name')->get() as $category)
                                    <label class="flex items-center p-3 bg-white/10 border border-white/20 rounded-lg hover:bg-white/20 cursor-pointer transition-colors duration-200">
                                        <input type="checkbox" 
                                               name="service_categories[]" 
                                               value="{{ $category->id }}"
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-white/30 rounded bg-white/20"
                                               {{ in_array($category->id, old('service_categories', [])) ? 'checked' : '' }}>
                                        <div class="ml-3 flex items-center text-white">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} text-green-200 mr-2"></i>
                                            @endif
                                            <span class="text-sm">{{ $category->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-green-200 mt-2">Selecione as áreas em que sua empresa atua</p>
                            @error('service_categories')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex space-x-4 pt-4">
                            <button type="button" 
                                    @click="animatingCompany = true; setTimeout(() => { activeProfile = null; animatingCompany = false; }, 150)" 
                                    :disabled="animatingCompany"
                                    :class="{ 'pointer-events-none opacity-50': animatingCompany }"
                                    class="flex-1 bg-white/20 text-white font-medium py-3 px-4 rounded-lg hover:bg-white/30 transition-colors duration-300">
                                Voltar
                            </button>
                            <button type="submit" class="flex-1 bg-white text-green-700 font-bold py-3 px-4 rounded-lg hover:bg-purple-400 hover:text-white transition-all duration-300">
                                Criar Perfil
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Botão flutuante para alternar para Freelancer -->
                <button x-show="activeProfile === 'freelancer'" 
                        @click="animatingFreelancer = true; setTimeout(() => { activeProfile = null; animatingFreelancer = false; }, 150)"
                        :disabled="animatingFreelancer"
                        :class="{ 'pointer-events-none opacity-50': animatingFreelancer }"
                        x-transition:enter="transition ease-out duration-300 delay-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-4"
                        class="absolute top-8 right-8 bg-white/20 backdrop-blur-sm text-white p-4 rounded-full hover:bg-white/30 transition-all duration-300 shadow-lg z-30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </button>
            </div>
        </div>

    </body>
</html>