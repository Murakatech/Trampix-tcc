{{-- Editor de Foto de Perfil Simplificado --}}
<div id="photoEditorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Cabeçalho do Modal -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                {{ $type === 'freelancer' ? 'Foto de Perfil' : 'Logo da Empresa' }}
            </h3>
            <button onclick="closePhotoEditor()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Conteúdo do Modal -->
        <div class="p-6">
            <!-- Upload de Nova Imagem -->
            <div class="mb-6">
                <input type="file" 
                       id="photoEditorInput" 
                       accept="image/*" 
                       class="hidden"
                       onchange="loadImageForEditing(this)">
                
                <button onclick="document.getElementById('photoEditorInput').click()" 
                        class="w-full btn-trampix-secondary text-sm py-3">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Escolher Foto
                </button>
            </div>

            <!-- Área de Pré-visualização -->
            <div class="mb-6">
                <p class="text-sm text-gray-600 mb-3 text-center">Ajuste o posicionamento da sua foto:</p>
                
                <!-- Container da Imagem -->
                <div class="relative bg-gray-50 rounded-lg overflow-hidden mx-auto" 
                     style="width: 200px; height: 200px;">
                    
                    <!-- Máscara Circular -->
                    <div class="absolute inset-0 bg-white" style="clip-path: circle(90px at center);"></div>
                    
                    <!-- Imagem de Preview -->
                    <div id="imagePreviewContainer" class="absolute inset-0">
                        <img id="imagePreview" 
                             src="" 
                             alt="Preview" 
                             class="w-full h-full object-cover cursor-move transition-all duration-200 ease-in-out"
                             style="transform-origin: center center; display: none;"
                             draggable="false">
                        
                        <!-- Placeholder quando não há imagem -->
                        <div id="imagePlaceholder" class="absolute inset-0 flex items-center justify-center text-gray-400">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <p class="text-xs">Selecione uma foto</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlay circular para mostrar o corte -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="w-full h-full" style="background: radial-gradient(circle 90px at center, transparent 90px, rgba(0,0,0,0.5) 90px);"></div>
                        <div class="absolute inset-0 border-4 border-white rounded-full" style="width: 180px; height: 180px; top: 10px; left: 10px;"></div>
                    </div>
                </div>
                
                <!-- Instruções -->
                <p class="text-xs text-gray-500 text-center mt-2">
                    Arraste a imagem para posicioná-la
                </p>
            </div>

            <!-- Controles Simples -->
            <div class="mb-6" id="imageControls" style="display: none;">
                <!-- Zoom -->
                <div class="mb-4">
                    <label class="text-sm text-gray-600 block mb-2">Tamanho:</label>
                    <div class="flex items-center space-x-3">
                        <button onclick="scaleImage(-0.1)" class="btn-trampix-secondary text-xs px-3 py-1">-</button>
                        <input type="range" 
                               id="scaleSlider" 
                               min="0.8" 
                               max="2" 
                               step="0.1" 
                               value="1" 
                               class="flex-1"
                               oninput="updateImageTransform()">
                        <button onclick="scaleImage(0.1)" class="btn-trampix-secondary text-xs px-3 py-1">+</button>
                    </div>
                </div>

                <!-- Reset -->
                <button onclick="resetPosition()" class="w-full btn-trampix-secondary text-sm">
                    Centralizar
                </button>
            </div>
        </div>

        <!-- Rodapé com Ações -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <button onclick="closePhotoEditor()" class="btn-trampix-secondary">
                Cancelar
            </button>
            
            <button onclick="confirmPhotoChanges()" class="btn-trampix-primary opacity-50 cursor-not-allowed" disabled id="confirmButton">
                Salvar
            </button>
        </div>
    </div>
</div>

<!-- Form oculto para envio -->
<form id="photoEditorForm" method="post" action="{{ route('profile.image.update') }}" enctype="multipart/form-data" class="hidden">
    @csrf
    @method('patch')
    <input type="hidden" name="profile_type" value="{{ $type }}">
    <input type="hidden" name="position_x" id="finalPositionX" value="0">
    <input type="hidden" name="position_y" id="finalPositionY" value="0">
    <input type="hidden" name="scale" id="finalScale" value="1">
    <input type="file" name="profile_image" id="finalImageInput">
</form>