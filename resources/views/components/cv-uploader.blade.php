{{-- Upload de Currículo --}}
<div id="cv-uploader-main" class="trampix-card">
    <div class="mb-4">
        <h3 class="trampix-h3 mb-2">Currículo</h3>
        <p class="text-sm text-gray-600">
            Faça upload do seu currículo em formato PDF ou DOCX (máximo 10MB)
        </p>
    </div>

    <!-- Área de Upload -->
    <div class="cv-drop-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center transition-all duration-300 hover:border-indigo-400 hover:bg-indigo-50/50">
        
        <!-- Estado Inicial - Sem arquivo -->
        <div class="cv-initial-state space-y-4">
            <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            
            <div>
                <p class="text-lg font-medium text-gray-700 mb-2">
                    Arraste seu currículo aqui
                </p>
                <p class="text-sm text-gray-500 mb-4">
                    ou clique para selecionar um arquivo
                </p>
                
                <button type="button" 
                        onclick="document.getElementById('cvFileInput').click()" 
                        class="btn-trampix-secondary">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Escolher Arquivo
                </button>
            </div>
            
            <div class="text-xs text-gray-400">
                Formatos aceitos: PDF, DOCX • Tamanho máximo: 10MB
            </div>
        </div>

        <!-- Estado Arquivo Selecionado -->
        <div class="cv-selected-state hidden space-y-4">
            <div class="flex items-center justify-center space-x-3">
                <div class="cv-file-icon w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="cv-file-name font-medium text-gray-900"></p>
                    <p class="cv-file-size text-sm text-gray-500"></p>
                </div>
            </div>
            
            <!-- Pré-visualização -->
            <div class="cv-preview-area hidden"></div>
            
            <!-- Botões de ação -->
            <div class="flex space-x-3">
                <button onclick="removeCvFile('cv-uploader-main')" 
                        class="btn-trampix-secondary text-sm">
                    Remover
                </button>
                <button onclick="replaceCvFile('cv-uploader-main')" 
                        class="btn-trampix-secondary text-sm">
                    Substituir
                </button>
                <button onclick="confirmCvUpload('cv-uploader-main')" 
                        class="btn-trampix-primary text-sm">
                    Confirmar Upload
                </button>
            </div>
        </div>

        <!-- Estado Uploading -->
        <div class="cv-uploading-state hidden space-y-4">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-indigo-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-700 mb-2">Enviando currículo...</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="cv-progress-bar bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Estado Sucesso -->
        <div class="cv-success-state hidden space-y-4">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-700 mb-2">Currículo enviado com sucesso!</p>
                <p class="cv-success-file-name text-sm text-gray-500"></p>
            </div>
        </div>

        <!-- Input de arquivo oculto -->
        <input type="file" 
               id="cvFileInput" 
               accept=".pdf,.docx,.doc" 
               class="cv-file-input hidden">
    </div>

    <!-- Currículo Atual (se existir) -->
    @if(isset($currentCv) && $currentCv)
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Currículo atual</p>
                    <p class="text-sm text-gray-500">{{ basename($currentCv) }}</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ Storage::url($currentCv) }}" 
                   target="_blank" 
                   class="btn-trampix-secondary text-sm">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Visualizar
                </a>
                
                <button onclick="deleteCv('cv-uploader-main')" 
                        class="btn-trampix-secondary text-red-600 border-red-300 hover:bg-red-50 text-sm">
                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Remover
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Form oculto para remoção -->
<form id="cvDeleteForm" method="post" action="{{ route('profile.cv.delete') }}" class="hidden">
    @csrf
    @method('delete')
</form>