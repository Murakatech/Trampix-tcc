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
        <meta name="old-segments" content='@json(old("segments", []))'>

        <!-- Estilos locais: esconder scrollbar mantendo rolagem -->
        <style>
            .no-scrollbar {
                -ms-overflow-style: none; /* IE e Edge */
                scrollbar-width: none; /* Firefox */
            }
            .no-scrollbar::-webkit-scrollbar {
                display: none; /* Chrome, Safari */
            }
            /* Select com texto branco no estado fechado e opções pretas no dropdown */
            .select-contrast { color: #fff; }
            .select-contrast option { color: #000; background-color: #fff; }
            .select-contrast optgroup { color: #000; }
            /* Autocomplete dropdown */
            .autocomplete-list { position: absolute; top: 100%; left: 0; right: 0; z-index: 50; background: rgba(255,255,255,0.95); backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.3); border-radius: 0.5rem; margin-top: 0.25rem; max-height: 200px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
            .autocomplete-item { display: block; width: 100%; padding: 0.5rem 0.75rem; text-align: left; color: #111827; background: transparent; border: none; cursor: pointer; }
            .autocomplete-item:hover { background-color: rgba(243,244,246,0.9); }
            /* Chips com ícone de remover visível no hover */
            .chip { display: inline-flex; align-items: center; gap: 6px; position: relative; }
            .chip .chip-close { opacity: 0; transition: opacity 150ms ease; color: #4b5563; font-weight: 700; line-height: 1; }
            .chip:hover .chip-close { opacity: 1; }
            .chip .chip-close:hover { color: #ef4444; }
        </style>
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
                     class="text-center px-8 absolute inset-0 flex flex-col justify-center items-center transition-all duration-300 overflow-y-auto no-scrollbar py-12">
                    
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
                            <label for="whatsapp" class="block text-sm font-medium text-purple-100 mb-2">WhatsApp *</label>
                            <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" required data-mask="br-phone" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Ex: (16) 99999-9999">
                            @error('whatsapp')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- E-mail (Freelancer) -->
                        <div>
                            <label for="freelancer_email" class="block text-sm font-medium text-purple-100 mb-2">E-mail</label>
                            <input type="email" id="freelancer_email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="seu@email.com">
                            @error('email')
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
                            <label for="location" class="block text-sm font-medium text-purple-100 mb-2">Localização</label>
                            <input type="text" id="location" name="location" value="{{ old('location') }}" data-autocomplete="city" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Cidade, Estado, País">
                            @error('location')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Segmentos (Freelancer) -->
                        <label class="block text-sm font-medium text-purple-100 mb-2">Segmento (Max 3)</label>
                        <div id="freelancerSegmentsPicker" class="space-y-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto no-scrollbar">
                                @foreach(\App\Models\Segment::orderBy('name')->get() as $segment)
                                    <button type="button"
                                            class="segment-option flex items-center p-3 bg-white/10 border border-white/20 rounded-lg hover:bg-white/20 cursor-pointer transition-colors duration-200 text-white"
                                            data-id="{{ $segment->id }}" data-name="{{ $segment->name }}">
                                        <span class="text-sm">{{ $segment->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <div id="freelancerSelectedChips" class="flex flex-wrap gap-2"></div>
                            <div id="freelancerSelectedInputs"></div>
                            @error('segments')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hourly_rate" class="block text-sm font-medium text-purple-100 mb-2">Valor por Hora (R$)</label>
                            <input type="text" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate') }}" data-mask="br-currency" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="R$ 50,00">
                            @error('hourly_rate')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="availability" class="block text-sm font-medium text-purple-100 mb-2">Disponibilidade</label>
                            <select id="availability" name="availability" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white select-contrast focus:outline-none focus:ring-2 focus:ring-white/50">
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
                     class="text-center px-8 absolute inset-0 flex flex-col justify-center items-center transition-all duration-300 overflow-y-auto no-scrollbar py-12">
                    
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
                            <label for="email" class="block text-sm font-medium text-green-100 mb-2">E-mail *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="empresa@dominio.com">
                            @error('email')
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
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" data-mask="br-phone" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="(11) 3333-4444">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Localização (Empresa) -->
                        <div>
                            <label for="company_location" class="block text-sm font-medium text-green-100 mb-2">Localização</label>
                            <input type="text" id="company_location" name="location" value="{{ old('location') }}" data-autocomplete="city" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-white/50" placeholder="Cidade, Estado, País">
                            @error('location')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Segmentos (Empresa) -->
                        <label class="block text-sm font-medium text-green-100 mb-2">Segmento (Max 3)</label>
                        <div id="companySegmentsPicker" class="space-y-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto no-scrollbar">
                                @foreach(\App\Models\Segment::orderBy('name')->get() as $segment)
                                    <button type="button"
                                            class="segment-option flex items-center p-3 bg-white/10 border border-white/20 rounded-lg hover:bg-white/20 cursor-pointer transition-colors duration-200 text-white"
                                            data-id="{{ $segment->id }}" data-name="{{ $segment->name }}">
                                        <span class="text-sm">{{ $segment->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <div id="companySelectedChips" class="flex flex-wrap gap-2"></div>
                            <div id="companySelectedInputs"></div>
                            @error('segments')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="company_size" class="block text-sm font-medium text-green-100 mb-2">Número de Funcionários</label>
                            <select id="company_size" name="company_size" class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white select-contrast focus:outline-none focus:ring-2 focus:ring-white/50">
                                <option value="">Selecione...</option>
                                <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1-10 funcionários</option>
                                <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11-50 funcionários</option>
                                <option value="51-200" {{ old('company_size') == '51-200' ? 'selected' : '' }}>51-200 funcionários</option>
                                <option value="201-500" {{ old('company_size') == '201-500' ? 'selected' : '' }}>201-500 funcionários</option>
                                <option value="500+" {{ old('company_size') == '500+' ? 'selected' : '' }}>500+ funcionários</option>
                            </select>
                            @error('company_size')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Categorias de Serviços (seleção via select múltiplo já acima) -->
                        
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
<script>
// Picker de segmentos com chips e limite de seleção
document.addEventListener('DOMContentLoaded', function () {
    const setupPicker = (rootId, chipsId, inputsId, limit, preselected = []) => {
        const root = document.getElementById(rootId);
        if (!root) return;
        const chips = document.getElementById(chipsId);
        const inputs = document.getElementById(inputsId);
        const options = root.querySelectorAll('.segment-option');

        const selectedSet = new Set(preselected.map(id => String(id)));

        const renderChip = (id, name) => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'chip px-3 py-1 bg-white text-purple-700 rounded-full text-sm shadow hover:bg-purple-200 transition';
            chip.dataset.id = id;
            chip.setAttribute('aria-label', `Remover segmento ${name}`);
            chip.addEventListener('click', () => toggle(id, name));

            const labelSpan = document.createElement('span');
            labelSpan.textContent = name;

            const closeSpan = document.createElement('span');
            closeSpan.className = 'chip-close';
            closeSpan.textContent = '×';

            chip.appendChild(labelSpan);
            chip.appendChild(closeSpan);
            chips.appendChild(chip);
        };

        const addHiddenInput = (id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'segments[]';
            input.value = id;
            input.dataset.id = id;
            inputs.appendChild(input);
        };

        const removeChip = (id) => {
            chips.querySelectorAll(`[data-id="${id}"]`).forEach(el => el.remove());
        };

        const removeInput = (id) => {
            inputs.querySelectorAll(`[data-id="${id}"]`).forEach(el => el.remove());
        };

        const markOption = (id, on) => {
            options.forEach(opt => {
                if (opt.dataset.id === String(id)) {
                    opt.classList.toggle('ring-2', on);
                    opt.classList.toggle('ring-white', on);
                    opt.classList.toggle('bg-white/20', on);
                }
            });
        };

        const toggle = (id, name) => {
            const idStr = String(id);
            if (selectedSet.has(idStr)) {
                selectedSet.delete(idStr);
                removeChip(idStr);
                removeInput(idStr);
                markOption(idStr, false);
            } else {
                if (selectedSet.size >= limit) {
                    alert(`Você pode selecionar no máximo ${limit} segmentos.`);
                    return;
                }
                selectedSet.add(idStr);
                renderChip(idStr, name);
                addHiddenInput(idStr);
                markOption(idStr, true);
            }
        };

        // Inicializar opções clique
        options.forEach(opt => {
            opt.addEventListener('click', () => toggle(opt.dataset.id, opt.dataset.name));
        });

        // Preselecionar
        options.forEach(opt => {
            if (selectedSet.has(opt.dataset.id)) {
                renderChip(opt.dataset.id, opt.dataset.name);
                addHiddenInput(opt.dataset.id);
                markOption(opt.dataset.id, true);
            }
        });
    };

    const oldSelected = (function(){
        try { return JSON.parse(document.querySelector('meta[name="old-segments"]')?.content || '[]'); } catch(e) { return []; }
    })();

    setupPicker('freelancerSegmentsPicker', 'freelancerSelectedChips', 'freelancerSelectedInputs', 3, oldSelected);
    setupPicker('companySegmentsPicker', 'companySelectedChips', 'companySelectedInputs', 3, oldSelected);
});
</script>
<script>
// Autocomplete de Localização usando Nominatim (OpenStreetMap)
document.addEventListener('DOMContentLoaded', function() {
  const initCityAutocomplete = (input) => {
    let timer;
    // criar container de lista se não existir
    let list = input.parentNode.querySelector('.autocomplete-list');
    input.parentNode.classList.add('relative');
    if (!list) {
      list = document.createElement('div');
      list.className = 'autocomplete-list hidden';
      input.parentNode.appendChild(list);
    }

    const formatResult = (item) => {
      const a = item.address || {};
      const city = a.city || a.town || a.village || a.municipality || a.suburb || '';
      const state = a.state || a.region || '';
      const country = a.country || '';
      return [city, state, country].filter(Boolean).join(', ') || item.display_name;
    };

    const fetchResults = async (q) => {
      if (!q || q.length < 2) { list.innerHTML = ''; list.classList.add('hidden'); return; }
      try {
        const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=8&q=${encodeURIComponent(q)}`;
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await resp.json();
        list.innerHTML = '';
        data.forEach(item => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'autocomplete-item';
          const label = formatResult(item);
          btn.textContent = label;
          btn.addEventListener('click', () => {
            input.value = label;
            list.classList.add('hidden');
            list.innerHTML = '';
          });
          list.appendChild(btn);
        });
        list.classList.toggle('hidden', data.length === 0);
      } catch (e) {
        // Falha silenciosa; poderia logar se necessário
      }
    };

    input.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => fetchResults(input.value.trim()), 250);
    });
    input.addEventListener('blur', () => {
      setTimeout(() => list.classList.add('hidden'), 150);
    });
    input.addEventListener('focus', () => {
      if (list.innerHTML.trim() !== '') list.classList.remove('hidden');
    });
  };

  document.querySelectorAll('input[data-autocomplete="city"]').forEach(initCityAutocomplete);
});
</script>