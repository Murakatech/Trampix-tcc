@extends('layouts.app')

@section('header')
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Perfil</h1>
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
                            <button onclick="openModal('createCompanyModal')" class="btn-trampix-primary">
                                Criar Perfil de Empresa
                            </button>
                        @else
                            <form method="post" action="{{ route('role.switch') }}" class="inline">
                                @csrf
                                <input type="hidden" name="role" value="company">
                                <button type="submit" class="btn-trampix-secondary">
                                    Trocar Perfil Profissional
                                </button>
                            </form>
                        @endif
                    @else
                        @if(!$user->freelancer)
                            <button onclick="openModal('createFreelancerModal')" class="btn-trampix-primary">
                                Criar Perfil Freelancer
                            </button>
                        @else
                            <form method="post" action="{{ route('role.switch') }}" class="inline">
                                @csrf
                                <input type="hidden" name="role" value="freelancer">
                                <button type="submit" class="btn-trampix-secondary">
                                    Trocar Perfil Profissional
                                </button>
                            </form>
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
                                    <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg" 
                                         src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                         alt="Foto do perfil">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                @if(isset($company) && $company->profile_photo)
                                    <img class="h-24 w-24 rounded-lg object-cover border-4 border-white shadow-lg" 
                                         src="{{ asset('storage/' . $company->profile_photo) }}" 
                                         alt="Logo da empresa">
                                @else
                                    <div class="h-24 w-24 rounded-lg bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
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
                            <div class="flex space-x-3">
                                <button type="button" 
                                        onclick="openPhotoEditor('{{ (session('active_role') === 'freelancer' && isset($freelancer) && $freelancer->profile_photo) ? asset('storage/' . $freelancer->profile_photo) : ((session('active_role') === 'company' && isset($company) && $company->profile_photo) ? asset('storage/' . $company->profile_photo) : '') }}')"
                                        class="btn-trampix-primary">
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
                                    <form method="post" action="{{ route('profile.image.delete') }}" class="inline">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="profile_type" value="{{ session('active_role') }}">
                                        <button type="submit" class="btn-trampix-secondary text-red-600 border-red-300 hover:bg-red-50" 
                                                onclick="return confirm('Tem certeza que deseja remover a imagem?')">
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        Remover Imagem
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
            
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('patch')

                @if(session('active_role') === 'freelancer')
                    <!-- Formulário Freelancer -->
                    <div class="space-y-6">
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biografia</label>
                            <textarea id="bio" name="bio" rows="4" 
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror" 
                                      placeholder="Conte sobre sua experiência e especialidades...">{{ old('bio', $freelancer->bio ?? '') }}</textarea>
                            @error('bio')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="portfolio_url" class="block text-sm font-medium text-gray-700">URL do Portfólio</label>
                                <input type="url" id="portfolio_url" name="portfolio_url" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('portfolio_url') border-red-500 @enderror" 
                                       value="{{ old('portfolio_url', $freelancer->portfolio_url ?? '') }}"
                                       placeholder="https://meuportfolio.com">
                                @error('portfolio_url')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" 
                                       value="{{ old('phone', $freelancer->phone ?? '') }}"
                                       placeholder="(11) 99999-9999">
                                @error('phone')
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
                    </div>
                @endif

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="btn-trampix-primary">
                        Salvar Alterações
                    </button>
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
@include('profile.partials.create-company-modal')
@include('profile.partials.create-freelancer-modal')

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
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Inicializar primeira tab
document.addEventListener('DOMContentLoaded', function() {
    showTab('profile');
    
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
<script src="{{ asset('js/photo-editor.js') }}"></script>
<script src="{{ asset('js/cv-uploader.js') }}"></script>

<style>
.tab-button {
    @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
}

.tab-button.active {
    @apply border-blue-500 text-blue-600;
}
</style>
@endsection
