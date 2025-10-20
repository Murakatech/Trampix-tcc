{{-- Editor de Foto de Perfil --}}
<div id="photoEditorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <!-- Cabeçalho do Modal -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
                Personalizar {{ $type === 'freelancer' ? 'Foto de Perfil' : 'Logo da Empresa' }}
            </h3>
            <button onclick="closePhotoEditor()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Conteúdo do Modal -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Área de Pré-visualização -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Pré-visualização</h4>
                        
                        <!-- Container da Imagem -->
                        <div class="relative bg-white rounded-lg border-2 border-dashed border-gray-300 overflow-hidden" 
                             style="width: 300px; height: 300px; margin: 0 auto;">
                            
                            <!-- Imagem de Preview -->
                            <div id="imagePreviewContainer" class="absolute inset-0 flex items-center justify-center">
                                <img id="imagePreview" 
                                     src="" 
                                     alt="Preview" 
                                     class="max-w-none transition-all duration-200 ease-in-out"
                                     style="transform-origin: center center;">
                                
                                <!-- Placeholder quando não há imagem -->
                                <div id="imagePlaceholder" class="text-center text-gray-400">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <p class="text-sm">Selecione uma imagem para editar</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resultado Final -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-gray-500 mb-2">Como ficará no perfil:</p>
                            <div class="inline-block">
                                <canvas id="finalPreview" 
                                        width="80" 
                                        height="80" 
                                        class="{{ $type === 'freelancer' ? 'rounded-full' : 'rounded-lg' }} border-2 border-gray-200 shadow-sm">
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controles de Edição -->
                <div class="space-y-6">
                    
                    <!-- Upload de Nova Imagem -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Selecionar Imagem</h4>
                        <div class="space-y-3">
                            <input type="file" 
                                   id="photoEditorInput" 
                                   accept="image/*" 
                                   class="hidden"
                                   onchange="loadImageForEditing(this)">
                            
                            <button onclick="document.getElementById('photoEditorInput').click()" 
                                    class="w-full btn-trampix-secondary text-sm">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Escolher Arquivo
                            </button>
                        </div>
                    </div>

                    <!-- Controles de Rotação -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Rotação</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-600 w-12">Ângulo:</label>
                                <input type="range" 
                                       id="rotationSlider" 
                                       min="-180" 
                                       max="180" 
                                       value="0" 
                                       class="flex-1"
                                       oninput="updateImageTransform()">
                                <span id="rotationValue" class="text-xs text-gray-600 w-8">0°</span>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="rotateImage(-90)" class="btn-trampix-secondary text-xs px-2 py-1">
                                    ↺ 90°
                                </button>
                                <button onclick="rotateImage(90)" class="btn-trampix-secondary text-xs px-2 py-1">
                                    ↻ 90°
                                </button>
                                <button onclick="resetRotation()" class="btn-trampix-secondary text-xs px-2 py-1">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Controles de Posição -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Posição</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-600 w-12">X:</label>
                                <input type="range" 
                                       id="positionXSlider" 
                                       min="-100" 
                                       max="100" 
                                       value="0" 
                                       class="flex-1"
                                       oninput="updateImageTransform()">
                                <span id="positionXValue" class="text-xs text-gray-600 w-8">0</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-600 w-12">Y:</label>
                                <input type="range" 
                                       id="positionYSlider" 
                                       min="-100" 
                                       max="100" 
                                       value="0" 
                                       class="flex-1"
                                       oninput="updateImageTransform()">
                                <span id="positionYValue" class="text-xs text-gray-600 w-8">0</span>
                            </div>
                            <button onclick="resetPosition()" class="btn-trampix-secondary text-xs w-full">
                                Centralizar
                            </button>
                        </div>
                    </div>

                    <!-- Controles de Escala -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Tamanho</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-600 w-12">Escala:</label>
                                <input type="range" 
                                       id="scaleSlider" 
                                       min="0.5" 
                                       max="3" 
                                       step="0.1" 
                                       value="1" 
                                       class="flex-1"
                                       oninput="updateImageTransform()">
                                <span id="scaleValue" class="text-xs text-gray-600 w-8">1x</span>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="scaleImage(0.8)" class="btn-trampix-secondary text-xs px-2 py-1">
                                    -
                                </button>
                                <button onclick="scaleImage(1.25)" class="btn-trampix-secondary text-xs px-2 py-1">
                                    +
                                </button>
                                <button onclick="resetScale()" class="btn-trampix-secondary text-xs px-2 py-1">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Geral -->
                    <div class="pt-4 border-t border-gray-200">
                        <button onclick="resetAllTransforms()" class="w-full btn-trampix-secondary text-sm">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Resetar Tudo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rodapé com Ações -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <button onclick="closePhotoEditor()" class="btn-trampix-secondary">
                Cancelar
            </button>
            
            <div class="flex space-x-3">
                <button onclick="previewChanges()" class="btn-trampix-secondary">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Pré-visualizar
                </button>
                
                <button onclick="confirmPhotoChanges()" class="btn-trampix-primary" disabled id="confirmButton">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirmar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form oculto para envio -->
<form id="photoEditorForm" method="post" action="{{ route('profile.image.update') }}" enctype="multipart/form-data" class="hidden">
    @csrf
    @method('patch')
    <input type="hidden" name="profile_type" value="{{ $type }}">
    <input type="hidden" name="rotation" id="finalRotation" value="0">
    <input type="hidden" name="position_x" id="finalPositionX" value="0">
    <input type="hidden" name="position_y" id="finalPositionY" value="0">
    <input type="hidden" name="scale" id="finalScale" value="1">
    <input type="file" name="profile_image" id="finalImageInput">
</form>