@extends('layouts.dashboard')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Editar Perfil</h1>
        <p class="mt-2 text-sm text-gray-800 font-semibold">
            @if(session('active_role') === 'freelancer')
                Perfil Freelancer
            @else
                Perfil Empresa
            @endif
        </p>
        <p class="mt-1 text-sm text-gray-600">
            Perfil ativo: 
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                @if(session('active_role') === 'freelancer')
                    Freelancer
                @else
                    Empresa
                @endif
            </span>
        </p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <!-- Mensagens de Feedback -->
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-red-800 font-semibold">Ocorreram erros ao salvar seu perfil:</p>
                    <ul class="mt-2 list-disc list-inside text-red-700 text-sm">
                        @foreach ($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Navegação por Tabs -->
    <div class="mb-8">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="showTab('profile')" id="tab-profile" class="tab-button active whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Perfil Profissional
            </button>
            <button onclick="showTab('account')" id="tab-account" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Conta
            </button>
        </nav>
    </div>

    <!-- Conteúdo das Tabs -->
    
    <!-- Tab: Informações do Perfil -->
    <div id="content-profile" class="tab-content">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if(session('active_role') === 'freelancer')
                            Informações do Freelancer
                        @else
                            Informações da Empresa
                        @endif
                    </h3>
                    
                    <!-- Botão Dinâmico para Criar/Trocar Perfil -->
                    @if(session('active_role') === 'freelancer')
                        @if(!$user->company)
                            <button type="button" onclick="openModal('createCompanyModal')" class="btn-trampix-primary">
                                Criar Perfil de Empresa
                            </button>
                        @else
                            <!-- Dropdown para Trocar Perfil -->
                            <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="btn-trampix-primary flex items-center gap-2">
                                    Mudar para perfil Empresa
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute right-0 mt-2 w-64 trampix-card shadow-lg z-50">
                                    
                                    <!-- Opção ativa (desabilitada) -->
                                    <div class="px-4 py-3 text-sm border-b border-gray-200 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 text-purple-700">🧑‍💻</span>
                                            <span class="text-gray-700 font-medium">Perfil atual: Freelancer</span>
                                        </div>
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    
                                    <!-- Opção para trocar -->
                                    <form method="post" action="{{ route('profile.switch-role') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="company">
                                        <button type="submit" class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3 rounded-lg hover:bg-green-50">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">🏢</span>
                                            <span class="text-gray-700">Mudar para perfil Empresa</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @else
                        @if(!$user->freelancer)
                            <button type="button" onclick="openModal('createFreelancerModal')" class="btn-trampix-primary">
                                Criar Perfil Freelancer
                            </button>
                        @else
                            <!-- Dropdown para Trocar Perfil -->
                            <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" class="btn-trampix-company flex items-center gap-2">
                                    Mudar para perfil Freelancer
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="absolute right-0 mt-2 w-64 trampix-card shadow-lg z-50">
                                    
                                    <!-- Opção ativa (desabilitada) -->
                                    <div class="px-4 py-3 text-sm border-b border-gray-200 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700">🏢</span>
                                            <span class="text-gray-700 font-medium">Perfil atual: Empresa</span>
                                        </div>
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    
                                    <!-- Opção para trocar -->
                                    <form method="post" action="{{ route('profile.switch-role') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="freelancer">
                                        <button type="submit" class="w-full px-4 py-3 text-left text-sm transition-colors duration-200 flex items-center gap-3 rounded-lg hover:bg-purple-50">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-100 text-purple-700">🧑‍💻</span>
                                            <span class="text-gray-700">Mudar para perfil Freelancer</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>


            <!-- Seção de Imagem de Perfil -->
            <div id="profile-info" class="p-6 border-b border-gray-200">
                <div class="flex items-start space-x-6">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            @if(session('active_role') === 'freelancer')
                                @if(isset($freelancer) && $freelancer->profile_photo)
                                    <div x-data="{ open: false }">
                                        <!-- Miniatura circular com foto real -->
                                        <div @click="open = true" class="cursor-pointer h-24 w-24 rounded-full overflow-hidden shadow-lg">
                                            <img src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                                 alt="Foto de perfil" 
                                                 class="h-full w-full object-cover object-center">
                                        </div>
                                        
                                        <!-- Preview ampliado -->
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             @click.away="open = false" 
                                             @keydown.escape.window="open = false" 
                                             class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
                                             style="display: none;">
                                            
                                            <!-- Container da imagem -->
                                            <div class="relative max-w-full max-h-full">
                                                <!-- Botão de fechar -->
                                                <button @click="open = false" 
                                                        class="absolute -top-12 right-0 bg-white hover:bg-gray-100 text-gray-700 hover:text-gray-900 rounded-full p-2 shadow-lg transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 z-10">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span class="sr-only">Fechar visualização</span>
                                                </button>
                                                
                                                <!-- Imagem -->
                                                <img src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                                     alt="Foto de perfil em tela cheia"
                                                     class="max-h-[85vh] max-w-[85vw] rounded-lg shadow-2xl object-contain transition-transform duration-300"
                                                     @click.stop>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-24 w-24 rounded-full overflow-hidden shadow bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                @if(isset($company) && $company->profile_photo)
                                    <div x-data="{ open: false }">
                                        <!-- Miniatura retangular com logo real -->
                                        <div @click="open = true" class="cursor-pointer h-24 w-24 rounded-lg overflow-hidden shadow-lg">
                                            <img src="{{ asset('storage/' . $company->profile_photo) }}" 
                                                 alt="Logo da empresa" 
                                                 class="h-full w-full object-cover object-center">
                                        </div>
                                        
                                        <!-- Preview ampliado -->
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             @click.away="open = false" 
                                             @keydown.escape.window="open = false" 
                                             class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4"
                                             style="display: none;">
                                            
                                            <!-- Container da imagem -->
                                            <div class="relative max-w-full max-h-full">
                                                <!-- Botão de fechar -->
                                                <button @click="open = false" 
                                                        class="absolute -top-12 right-0 bg-white hover:bg-gray-100 text-gray-700 hover:text-gray-900 rounded-full p-2 shadow-lg transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 z-10">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                    <span class="sr-only">Fechar visualização</span>
                                                </button>
                                                
                                                <!-- Imagem -->
                                                <img src="{{ asset('storage/' . $company->profile_photo) }}" 
                                                     alt="Logo da empresa em tela cheia"
                                                     class="max-h-[85vh] max-w-[85vw] rounded-lg shadow-2xl object-contain transition-transform duration-300"
                                                     @click.stop>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-24 w-24 rounded-lg overflow-hidden shadow bg-gray-200 flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 10h10M7 13h10"></path>
                                        </svg>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <h4 class="text-lg font-medium text-gray-900 mb-2">
                            @if(session('active_role') === 'freelancer')
                                Foto do Perfil
                            @else
                                Logo da Empresa
                            @endif
                        </h4>
                        <p class="text-sm text-gray-600 mb-4">
                            @if(session('active_role') === 'freelancer')
                                Adicione uma foto profissional para seu perfil de freelancer.
                            @else
                                Adicione o logo da sua empresa para maior credibilidade.
                            @endif
                        </p>
                        
                        <!-- Controles de Imagem -->
                        <div class="space-y-4">
                            <button type="button" 
                                    onclick="openPhotoEditor('{{ (session('active_role') === 'freelancer' && isset($freelancer) && $freelancer->profile_photo) ? asset('storage/' . $freelancer->profile_photo) : ((session('active_role') === 'company' && isset($company) && $company->profile_photo) ? asset('storage/' . $company->profile_photo) : '') }}')"
                                    class="{{ session('active_role') === 'company' ? 'btn-trampix-company' : 'btn-trampix-primary' }}">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                @if((session('active_role') === 'freelancer' && isset($freelancer) && $freelancer->profile_photo) || 
                                    (session('active_role') === 'company' && isset($company) && $company->profile_photo))
                                    Editar {{ session('active_role') === 'freelancer' ? 'Foto' : 'Logo' }}
                                @else
                                    Adicionar {{ session('active_role') === 'freelancer' ? 'Foto' : 'Logo' }}
                                @endif
                            </button>
                            
                            @if((session('active_role') === 'freelancer' && isset($freelancer) && $freelancer->profile_photo) || 
                                (session('active_role') === 'company' && isset($company) && $company->profile_photo))
                                <form method="POST" action="{{ route('profile.photo.delete') }}" id="removePhotoForm">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="profile_type" value="{{ session('active_role') }}">
                                    <button type="button" 
                                            class="mt-2 text-sm text-red-600 hover:text-red-700 hover:underline transition"
                                            onclick="showRemovePhotoConfirmation('{{ session('active_role') === 'freelancer' ? 'foto' : 'logo' }}')">
                                        Remover {{ session('active_role') === 'freelancer' ? 'Foto' : 'Logo' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        @error('profile_image')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <form id="profileMainForm" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('patch')
                <input type="hidden" name="section" value="{{ isset($freelancer) ? 'freelancer' : (isset($company) ? 'company' : session('active_role')) }}">

                @if(session('active_role') === 'freelancer')
                    <!-- Formulário Freelancer -->
                    <div class="space-y-6">
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-gray-700">Nome de Exibição Profissional</label>
                            <input type="text" id="display_name" name="display_name" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('display_name') border-red-500 @enderror" 
                                   value="{{ old('display_name', $freelancer->display_name ?? '') }}"
                                   placeholder="Como você gostaria de ser chamado profissionalmente">
                            <p class="mt-1 text-sm text-gray-500">Este nome será exibido em seu perfil profissional e candidaturas.</p>
                            @error('display_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biografia</label>
                            <textarea id="bio" name="bio" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror" 
                                      placeholder="Conte sobre sua experiência e especialidades...">{{ old('bio', $freelancer->bio ?? '') }}</textarea>
                            @error('bio')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Removido: segmento principal único do freelancer -->

                        <!-- Segmentos de Atuação -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Segmentos de Atuação</label>
                            <p class="text-sm text-gray-600 mb-4">Selecione os segmentos econômicos em que você atua. Usamos isso para conectar você a vagas e empresas relevantes.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach(($segments ?? \App\Models\Segment::where('active', true)->orderBy('name')->get()) as $segment)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                        <input type="checkbox"
                                               name="segments[]"
                                               value="{{ $segment->id }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ in_array($segment->id, old('segments', isset($freelancer) ? ($freelancer->segments->pluck('id')->toArray() ?? []) : [])) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $segment->name }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('segments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="linkedin_url" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                                <input type="url" id="linkedin_url" name="linkedin_url" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('linkedin_url') border-red-500 @enderror" 
                                       value="{{ old('linkedin_url', $freelancer->linkedin_url ?? '') }}"
                                       placeholder="https://www.linkedin.com/in/seu-perfil">
                                @error('linkedin_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                                <input type="text" id="whatsapp" name="whatsapp" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('whatsapp') border-red-500 @enderror" 
                                       value="{{ old('whatsapp', $freelancer->whatsapp ?? '') }}"
                                       placeholder="Ex: (16) 99999-9999" data-mask="br-phone">
                                @error('whatsapp')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="portfolio_url" class="block text-sm font-medium text-gray-700">Portfólio</label>
                                <input type="url" id="portfolio_url" name="portfolio_url"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('portfolio_url') border-red-500 @enderror"
                                       value="{{ old('portfolio_url', $freelancer->portfolio_url ?? '') }}"
                                       placeholder="https://seu-portfolio.com">
                                @error('portfolio_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Localização</label>
                                <input type="text" id="location" name="location" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                                       value="{{ old('location', $freelancer->location ?? '') }}"
                                       placeholder="São Paulo, SP">
                                @error('location')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Valor por Hora (R$)</label>
                                <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('hourly_rate') border-red-500 @enderror" 
                                       value="{{ old('hourly_rate', $freelancer->hourly_rate ?? '') }}"
                                       placeholder="50.00">
                                @error('hourly_rate')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="availability" class="block text-sm font-medium text-gray-700">Disponibilidade</label>
                                <select id="availability" name="availability" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('availability') border-red-500 @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="full_time" {{ old('availability', $freelancer->availability ?? '') == 'full_time' ? 'selected' : '' }}>Tempo Integral</option>
                                    <option value="part_time" {{ old('availability', $freelancer->availability ?? '') == 'part_time' ? 'selected' : '' }}>Meio Período</option>
                                    <option value="project_based" {{ old('availability', $freelancer->availability ?? '') == 'project_based' ? 'selected' : '' }}>Por Projeto</option>
                                </select>
                                @error('availability')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Upload de Currículo -->
                        <div class="col-span-2">
                            <x-cv-uploader :currentCv="isset($freelancer) && $freelancer->cv_path ? $freelancer->cv_path : null" />
                        </div>
                    </div>

                @else
                    <!-- Formulário Empresa -->
                    <div class="space-y-6">
                        <div>
                            <label for="display_name" class="block text-sm font-medium text-gray-700">Nome da Empresa</label>
                            <input type="text" id="display_name" name="display_name" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('display_name') border-red-500 @enderror" 
                                   value="{{ old('display_name', $company->display_name ?? '') }}"
                                   placeholder="Como sua empresa gostaria de ser chamada profissionalmente">
                            <p class="mt-1 text-sm text-gray-500">Este nome será exibido no perfil da empresa e nas vagas publicadas.</p>
                            @error('display_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição da Empresa</label>
                            <textarea id="description" name="description" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                                      placeholder="Descreva sua empresa, missão e valores...">{{ old('description', $company->description ?? '') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                <input type="url" id="website" name="website" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('website') border-red-500 @enderror" 
                                       value="{{ old('website', $company->website ?? '') }}"
                                       placeholder="https://minhaempresa.com">
                                @error('website')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" 
                                       value="{{ old('phone', $company->phone ?? '') }}"
                                       placeholder="(11) 3333-4444">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ *</label>
                                <input type="text" id="cnpj" name="cnpj"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('cnpj') border-red-500 @enderror"
                                       value="{{ old('cnpj', $company->cnpj ?? '') }}"
                                       placeholder="00.000.000/0000-00" data-mask="br-cnpj">
                                @error('cnpj')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_linkedin_url" class="block text-sm font-medium text-gray-700">LinkedIn</label>
                                <input type="url" id="company_linkedin_url" name="linkedin_url"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('linkedin_url') border-red-500 @enderror"
                                       value="{{ old('linkedin_url', $company->linkedin_url ?? '') }}"
                                       placeholder="https://www.linkedin.com/company/seu-perfil">
                                @error('linkedin_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700">Endereço</label>
                                <input type="text" id="address" name="address" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror" 
                                       value="{{ old('address', $company->address ?? '') }}"
                                       placeholder="Rua das Empresas, 123">
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="industry" class="block text-sm font-medium text-gray-700">Setor</label>
                                <input type="text" id="industry" name="industry" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('industry') border-red-500 @enderror" 
                                       value="{{ old('industry', $company->industry ?? '') }}"
                                       placeholder="Tecnologia">
                                @error('industry')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="company_size" class="block text-sm font-medium text-gray-700">Tamanho da Empresa</label>
                            <select id="company_size" name="company_size" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('company_size') border-red-500 @enderror">
                                <option value="">Selecione...</option>
                                <option value="1-10" {{ old('company_size', $company->company_size ?? '') == '1-10' ? 'selected' : '' }}>1-10 funcionários</option>
                                <option value="11-50" {{ old('company_size', $company->company_size ?? '') == '11-50' ? 'selected' : '' }}>11-50 funcionários</option>
                                <option value="51-200" {{ old('company_size', $company->company_size ?? '') == '51-200' ? 'selected' : '' }}>51-200 funcionários</option>
                                <option value="201-500" {{ old('company_size', $company->company_size ?? '') == '201-500' ? 'selected' : '' }}>201-500 funcionários</option>
                                <option value="500+" {{ old('company_size', $company->company_size ?? '') == '500+' ? 'selected' : '' }}>500+ funcionários</option>
                            </select>
                            @error('company_size')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Segmentos de Atuação da Empresa (até 3) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Segmentos de Atuação</label>
                            <p class="text-sm text-gray-600 mb-4">Selecione até 3 segmentos econômicos que representam sua empresa.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @php
                                    $selectedCompanySegments = old('segments', isset($company) ? ($company->segments->pluck('id')->toArray() ?? []) : []);
                                @endphp
                                @foreach(($segments ?? \App\Models\Segment::where('active', true)->orderBy('name')->get()) as $segment)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                        <input type="checkbox"
                                               name="segments[]"
                                               value="{{ $segment->id }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               {{ in_array($segment->id, $selectedCompanySegments) ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $segment->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('segments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Removido: Áreas de Atuação (Categorias de Serviços) da Empresa -->
                    </div>
                @endif

                <div class="pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Campos alterados:</span> <span id="saveInfoCount">0</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" class="btn-trampix-secondary" onclick="resetFormToOriginal('profileMainForm')">
                                Cancelar Alterações
                            </button>
                            <button type="submit" form="profileMainForm" class="{{ session('active_role') === 'company' ? 'btn-trampix-company' : 'btn-trampix-primary' }}">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Tab: Conta -->
    <div id="content-account" class="tab-content hidden">
        <div class="space-y-6">
            <!-- Informações da Conta -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informações da Conta</h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Alterar Senha -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Alterar Senha</h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Excluir Conta -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Excluir Conta</h3>
                </div>
                <div class="p-6">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modais -->
{{-- Modais de criação removidos desta página para evitar exibir campos do outro perfil.
     Utilize a página de seleção de perfil para criar novos perfis. --}}

<script>
// Sistema de Tabs
function showTab(tabName) {
    // Esconder todos os conteúdos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover classe active de todos os botões
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });
    
    // Mostrar conteúdo ativo
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Ativar botão correspondente
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active');
    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    activeButton.classList.add('border-blue-500', 'text-blue-600');
}

// Funções dos Modais
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    // Focar primeiro campo do formulário para acessibilidade
    const firstInput = modal.querySelector('input, select, textarea, button');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 50);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Inicializar primeira tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('profile');
    
    // Adicionar validação frontend para formulários
    initializeFormValidation();
    
    // Funcionalidade de Drag & Drop
    const dropZone = document.getElementById('dropZone');
    const dragOverlay = document.getElementById('dragOverlay');
    const fileInput = document.getElementById('profile_image');
    const form = document.getElementById('imageUploadForm');
    
    if (dropZone && fileInput) {
        // Prevenir comportamento padrão para todos os eventos de drag
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });
        
        // Destacar área de drop quando arrastar sobre ela
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        // Lidar com arquivos soltos
        dropZone.addEventListener('drop', handleDrop, false);
        
        // Lidar com seleção de arquivo via input
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                form.submit();
            }
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function highlight(e) {
            dropZone.classList.add('border-purple-400', 'bg-purple-50');
            dragOverlay.classList.remove('hidden');
            dragOverlay.classList.add('flex');
        }
        
        function unhighlight(e) {
            dropZone.classList.remove('border-purple-400', 'bg-purple-50');
            dragOverlay.classList.add('hidden');
            dragOverlay.classList.remove('flex');
        }
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                const file = files[0];
                
                // Verificar se é uma imagem
                if (file.type.startsWith('image/')) {
                    // Criar um novo FileList e atribuir ao input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                    
                    // Submeter o formulário
                    form.submit();
                } else {
                    alert('Por favor, selecione apenas arquivos de imagem (PNG, JPG, GIF).');
                }
            }
        }
    }
});
</script>

<!-- Editor de Fotos -->
<x-photo-editor :type="session('active_role')" />

<!-- Scripts dos Componentes -->
<script>
// Editor de Foto Simplificado
let currentImage = null;
let isDragging = false;
let dragStart = { x: 0, y: 0 };
let currentPosition = { x: 0, y: 0 };
let currentScale = 1;

// Função para abrir o editor de fotos
function openPhotoEditor(currentImageUrl = null) {
    const modal = document.getElementById('photoEditorModal');
    const imagePreview = document.getElementById('imagePreview');
    const imagePlaceholder = document.getElementById('imagePlaceholder');
    const imageControls = document.getElementById('imageControls');
    const confirmButton = document.getElementById('confirmButton');
    
    if (currentImageUrl) {
        imagePreview.src = currentImageUrl;
        imagePreview.style.display = 'block';
        imagePlaceholder.style.display = 'none';
        imageControls.style.display = 'block';
        currentImage = currentImageUrl;
        
        // Habilitar botão de confirmação
        confirmButton.disabled = false;
        confirmButton.classList.remove('opacity-50', 'cursor-not-allowed');
        
        // Configurar drag and drop
        setupImageDragging();
    } else {
        imagePreview.style.display = 'none';
        imagePlaceholder.style.display = 'flex';
        imageControls.style.display = 'none';
        currentImage = null;
        
        // Desabilitar botão de confirmação
        confirmButton.disabled = true;
        confirmButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
    
    modal.classList.remove('hidden');
    resetPosition();
}

// Função para fechar o editor de fotos
function closePhotoEditor() {
    const modal = document.getElementById('photoEditorModal');
    modal.classList.add('hidden');
    
    // Reset do input de arquivo
    const input = document.getElementById('photoEditorInput');
    input.value = '';
    
    // Reset das transformações
    resetPosition();
}

// Função para fechar o modal ao clicar no backdrop
function closePhotoEditorOnBackdrop(event) {
    if (event.target === event.currentTarget) {
        closePhotoEditor();
    }
}

// Adicionar listener para ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('photoEditorModal');
        if (modal && !modal.classList.contains('hidden')) {
            closePhotoEditor();
        }
    }
});

// Função para carregar imagem para edição
function loadImageForEditing(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const imageControls = document.getElementById('imageControls');
            const confirmButton = document.getElementById('confirmButton');
            
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            imagePlaceholder.style.display = 'none';
            imageControls.style.display = 'block';
            
            currentImage = e.target.result;
            resetPosition();
            
            // Habilitar botão de confirmação
            confirmButton.disabled = false;
            confirmButton.classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Configurar drag and drop
            setupImageDragging();
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Configurar funcionalidade de arrastar
function setupImageDragging() {
    const imagePreview = document.getElementById('imagePreview');
    
    // Mouse events
    imagePreview.addEventListener('mousedown', startDrag);
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', endDrag);
    
    // Touch events para mobile
    imagePreview.addEventListener('touchstart', startDragTouch);
    document.addEventListener('touchmove', dragTouch);
    document.addEventListener('touchend', endDrag);
}

function startDrag(e) {
    isDragging = true;
    dragStart.x = e.clientX - currentPosition.x;
    dragStart.y = e.clientY - currentPosition.y;
    e.preventDefault();
}

function startDragTouch(e) {
    isDragging = true;
    const touch = e.touches[0];
    dragStart.x = touch.clientX - currentPosition.x;
    dragStart.y = touch.clientY - currentPosition.y;
    e.preventDefault();
}

function drag(e) {
    if (!isDragging) return;
    
    currentPosition.x = e.clientX - dragStart.x;
    currentPosition.y = e.clientY - dragStart.y;
    
    // Limitar movimento dentro da área circular
    const maxMove = 50;
    currentPosition.x = Math.max(-maxMove, Math.min(maxMove, currentPosition.x));
    currentPosition.y = Math.max(-maxMove, Math.min(maxMove, currentPosition.y));
    
    updateImageTransform();
    e.preventDefault();
}

function dragTouch(e) {
    if (!isDragging) return;
    
    const touch = e.touches[0];
    currentPosition.x = touch.clientX - dragStart.x;
    currentPosition.y = touch.clientY - dragStart.y;
    
    // Limitar movimento dentro da área circular
    const maxMove = 50;
    currentPosition.x = Math.max(-maxMove, Math.min(maxMove, currentPosition.x));
    currentPosition.y = Math.max(-maxMove, Math.min(maxMove, currentPosition.y));
    
    updateImageTransform();
    e.preventDefault();
}

function endDrag() {
    isDragging = false;
}

// Função para atualizar transformações da imagem
function updateImageTransform() {
    const imagePreview = document.getElementById('imagePreview');
    if (!imagePreview || imagePreview.style.display === 'none') return;
    
    // Aplicar transformações
    const transform = `translate(${currentPosition.x}px, ${currentPosition.y}px) scale(${currentScale})`;
    imagePreview.style.transform = transform;
}

// Funções de escala
function scaleImage(delta) {
    currentScale = Math.max(0.8, Math.min(2, currentScale + delta));
    const slider = document.getElementById('scaleSlider');
    if (slider) {
        slider.value = currentScale;
    }
    updateImageTransform();
}

// Função para resetar posição
function resetPosition() {
    currentPosition = { x: 0, y: 0 };
    currentScale = 1;
    
    const slider = document.getElementById('scaleSlider');
    if (slider) {
        slider.value = 1;
    }
    
    updateImageTransform();
}

// Função para confirmar mudanças
function confirmPhotoChanges() {
    const input = document.getElementById('photoEditorInput');
    const form = document.getElementById('photoEditorForm');
    
    if (!input.files || !input.files[0]) {
        alert('Por favor, selecione uma imagem primeiro.');
        return;
    }
    
    // Preencher campos ocultos com as transformações
    document.getElementById('finalPositionX').value = currentPosition.x;
    document.getElementById('finalPositionY').value = currentPosition.y;
    document.getElementById('finalScale').value = currentScale;
    
    // Copiar arquivo para o input do formulário
    const finalInput = document.getElementById('finalImageInput');
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(input.files[0]);
    finalInput.files = dataTransfer.files;
    
    // Enviar formulário
    form.submit();
}

// Event listener para o slider de escala
document.addEventListener('DOMContentLoaded', function() {
    const scaleSlider = document.getElementById('scaleSlider');
    if (scaleSlider) {
        scaleSlider.addEventListener('input', function() {
            currentScale = parseFloat(this.value);
            updateImageTransform();
        });
    }
    
    // Inicializar detecção de mudanças nos formulários
    initializeFormChangeDetection();
});

// Função para detectar mudanças nos campos de formulário
function initializeFormChangeDetection() {
    const formInputs = document.querySelectorAll('form input, form textarea, form select');
    const originalValues = new Map();
    
    // Armazenar valores originais
    formInputs.forEach(input => {
        if (input.type === 'checkbox') {
            originalValues.set(input.name, input.checked);
        } else if (input.type === 'radio') {
            if (input.checked) {
                originalValues.set(input.name, input.value);
            }
        } else {
            originalValues.set(input.name, input.value);
        }
    });
    // Tornar acessível globalmente para reset
    window.ORIGINAL_FORM_VALUES = originalValues;
    
    // Adicionar listeners para detectar mudanças
    formInputs.forEach(input => {
        const events = ['input', 'change', 'keyup'];
        
        events.forEach(eventType => {
            input.addEventListener(eventType, function() {
                checkFieldChanges(this, originalValues);
            });
        });
    });
}

// Função para resetar valores do formulário para o estado original
function resetFormToOriginal(formId) {
    // Para garantir reset completo e consistente (inclui arrays como segments[]), recarregar a página
    window.location.reload();
}

// Função para verificar mudanças em um campo específico
function checkFieldChanges(field, originalValues) {
    let currentValue;
    let originalValue = originalValues.get(field.name);
    
    if (field.type === 'checkbox') {
        currentValue = field.checked;
    } else if (field.type === 'radio') {
        currentValue = field.checked ? field.value : originalValue;
    } else {
        currentValue = field.value;
    }
    
    const hasChanged = currentValue !== originalValue;
    
    // Adicionar/remover indicador visual de mudança
    toggleChangeIndicator(field, hasChanged);
    
    // Atualizar contador de mudanças
    updateChangeCounter();
}

// Função para adicionar/remover indicador visual de mudança
function toggleChangeIndicator(field, hasChanged) {
    const fieldContainer = field.closest('div');
    let changeIndicator = fieldContainer.querySelector('.change-indicator');
    
    if (hasChanged) {
        // Adicionar indicador se não existir
        if (!changeIndicator) {
            changeIndicator = document.createElement('div');
            changeIndicator.className = 'change-indicator absolute -top-1 -right-1 w-3 h-3 bg-orange-500 rounded-full border-2 border-white shadow-sm';
            changeIndicator.innerHTML = '<span class="sr-only">Campo alterado</span>';
            
            // Posicionar o indicador
            fieldContainer.style.position = 'relative';
            fieldContainer.appendChild(changeIndicator);
        }
        
        // Adicionar classe de campo alterado
        field.classList.add('border-orange-300', 'bg-orange-50');
        field.classList.remove('border-gray-300');
        
        // Animação de entrada
        changeIndicator.style.animation = 'pulse 0.5s ease-in-out';
        
    } else {
        // Remover indicador se existir
        if (changeIndicator) {
            changeIndicator.remove();
        }
        
        // Remover classe de campo alterado
        field.classList.remove('border-orange-300', 'bg-orange-50');
        field.classList.add('border-gray-300');
    }
}

// Função para atualizar contador de mudanças
function updateChangeCounter() {
    const changedFields = document.querySelectorAll('.change-indicator');
    let changeCounter = document.getElementById('changeCounter');
    const saveInfoCount = document.getElementById('saveInfoCount');
    
    if (changedFields.length > 0) {
        // Criar contador se não existir
        if (!changeCounter) {
            changeCounter = document.createElement('div');
            changeCounter.id = 'changeCounter';
            changeCounter.className = 'fixed bottom-4 right-4 bg-orange-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300';
            document.body.appendChild(changeCounter);
        }
        
        changeCounter.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>${changedFields.length} campo${changedFields.length > 1 ? 's' : ''} alterado${changedFields.length > 1 ? 's' : ''}</span>
            </div>
        `;
        
        changeCounter.style.display = 'block';
        changeCounter.style.opacity = '1';
        if (saveInfoCount) {
            saveInfoCount.textContent = String(changedFields.length);
        }
        
    } else {
        // Ocultar contador se não há mudanças
        if (changeCounter) {
            changeCounter.style.opacity = '0';
            setTimeout(() => {
                if (changeCounter && changeCounter.style.opacity === '0') {
                    changeCounter.style.display = 'none';
                }
            }, 300);
        }
        if (saveInfoCount) {
            saveInfoCount.textContent = '0';
        }
    }
}

// Função para inicializar validação frontend
function initializeFormValidation() {
    // Validação do formulário principal
    const mainForm = document.getElementById('profileMainForm');
    if (mainForm) {
        mainForm.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            
            // Desabilitar botão para evitar duplo submit
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Salvando...';
                
                // Reabilitar após 5 segundos (caso algo dê errado)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Salvar Alterações';
                }, 5000);
            }
        });
    }
    
    // Validação do modal de criação de empresa
    const companyModal = document.querySelector('#createCompanyModal form');
    if (companyModal) {
        companyModal.addEventListener('submit', function(e) {
            const nameField = this.querySelector('input[name="display_name"]');
            
            if (!nameField || !nameField.value.trim()) {
                e.preventDefault();
                alert('Por favor, preencha o nome da empresa.');
                if (nameField) nameField.focus();
                return false;
            }
            
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Criando...';
            }
        });
    }
}
</script>
<script src="{{ asset('js/cv-uploader.js') }}"></script>

<style>
.tab-button {
    @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
}

.tab-button.active {
    @apply border-blue-500 text-blue-600;
}
</style>

{{-- Componente de Confirmação --}}
<x-action-confirmation 
    actionType="generic" 
    modalId="removePhotoConfirmationModal" />

@push('scripts')
<script>
    window.APP_SEGMENTS = {!! json_encode(($segments ?? \App\Models\Segment::where('active', true)->orderBy('name')->get(['id','name']))->map(function($s){ return ['id' => $s->id, 'name' => $s->name]; })) !!};

    function ensureSegmentModal() {
        if (document.getElementById('segmentSelectionModal')) return;
        const modalHtml = `
        <div id="segmentSelectionModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
                <div class="px-4 py-3 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Selecionar Segmento</h3>
                    <button type="button" class="text-gray-500 hover:text-gray-700" data-action="close">✖</button>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <div id="segmentList" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
                </div>
                <div class="px-4 py-3 border-t flex justify-end gap-2">
                    <button type="button" class="btn-trampix-secondary" data-action="close">Cancelar</button>
                </div>
            </div>
        </div>`;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = modalHtml;
        document.body.appendChild(wrapper.firstElementChild);

        const segmentList = document.getElementById('segmentList');
        window.APP_SEGMENTS.forEach(seg => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'text-left p-3 border rounded hover:bg-gray-50';
            btn.dataset.segmentId = seg.id;
            btn.dataset.segmentName = seg.name;
            btn.innerHTML = `<span class="font-medium">${seg.name}</span>`;
            segmentList.appendChild(btn);
        });

        document.querySelectorAll('#segmentSelectionModal [data-action="close"]').forEach(el => {
            el.addEventListener('click', () => {
                document.getElementById('segmentSelectionModal').classList.add('hidden');
                document.getElementById('segmentSelectionModal').classList.remove('flex');
            });
        });
    }

    function openSegmentModal(targetPrefix) {
        ensureSegmentModal();
        const modal = document.getElementById('segmentSelectionModal');
        modal.dataset.targetPrefix = targetPrefix;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    document.addEventListener('DOMContentLoaded', () => {
        ensureSegmentModal();
        const segmentList = document.getElementById('segmentList');
        segmentList.addEventListener('click', (e) => {
            const btn = e.target.closest('button');
            if (!btn) return;
            const segId = btn.dataset.segmentId;
            const segName = btn.dataset.segmentName;
            const modal = document.getElementById('segmentSelectionModal');
            const prefix = modal.dataset.targetPrefix;
            if (!prefix) return;
            const idInput = document.getElementById(prefix + '_segment_id');
            const nameInput = document.getElementById(prefix + '_segment_name');
            if (idInput) idInput.value = segId;
            if (nameInput) nameInput.value = segName;

            // Close modal
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });

        const btnFreelancer = document.getElementById('btnChooseFreelancerSegment');
        if (btnFreelancer) {
            btnFreelancer.addEventListener('click', () => openSegmentModal('freelancer'));
        }
        const btnCompany = document.getElementById('btnChooseCompanySegment');
        if (btnCompany) {
            btnCompany.addEventListener('click', () => openSegmentModal('company'));
        }

        // Limitar seleção de segmentos (freelancer) a no máximo 3
        const segmentCheckboxes = document.querySelectorAll('input[name="segments[]"]');
        if (segmentCheckboxes.length) {
            segmentCheckboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const checked = Array.from(segmentCheckboxes).filter(x => x.checked);
                    if (checked.length > 3) {
                        cb.checked = false;
                        alert('Você pode selecionar no máximo 3 segmentos.');
                    }
                });
            });
        }
    });
</script>
@endpush

@push('scripts')
<script>
    // Função para remover foto/logo
    function showRemovePhotoConfirmation(photoType) {
        showActionModal('removePhotoConfirmationModal', {
            actionType: 'generic',
            message: `🗑️ Tem certeza que deseja remover a ${photoType}?\n\nEsta ação não pode ser desfeita.`,
            onConfirm: () => {
                const form = document.getElementById('removePhotoForm');
                showNotification(`Removendo ${photoType}...`, 'warning');
                form.submit();
            },
            onCancel: () => {
                showNotification('Remoção cancelada.', 'info');
            }
        });
    }
</script>
@endpush

@endsection

{{-- Modais Inline de Criação de Perfis (Empresa e Freelancer) --}}
@include('profile.partials.create-company-modal')
@include('profile.partials.create-freelancer-modal')

@push('scripts')
<script>
    // Abrir automaticamente os modais de criação caso haja flags na URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('openCompanyCreate')) {
            openModal('createCompanyModal');
        }
        if (urlParams.has('openFreelancerCreate')) {
            openModal('createFreelancerModal');
        }
    });
</script>
@endpush
@push('scripts')
<script>
    // Fechar modais de criação com tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['createCompanyModal','createFreelancerModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(id);
                }
            });
        }
    });
</script>
@endpush
