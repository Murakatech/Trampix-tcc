class PhotoEditor {
    constructor() {
        this.modal = document.getElementById('photo-editor-modal');
        this.canvas = document.getElementById('preview-canvas');
        this.ctx = this.canvas.getContext('2d');
        this.image = null;
        this.originalImage = null;
        
        // Controles
        this.rotationSlider = document.getElementById('rotation-slider');
        this.positionXSlider = document.getElementById('position-x-slider');
        this.positionYSlider = document.getElementById('position-y-slider');
        this.scaleSlider = document.getElementById('scale-slider');
        
        // Valores atuais
        this.rotation = 0;
        this.positionX = 0;
        this.positionY = 0;
        this.scale = 1;
        
        this.initEventListeners();
    }
    
    initEventListeners() {
        // Sliders
        this.rotationSlider.addEventListener('input', (e) => {
            this.rotation = parseInt(e.target.value);
            document.getElementById('rotation-value').textContent = this.rotation + '°';
            this.updatePreview();
        });
        
        this.positionXSlider.addEventListener('input', (e) => {
            this.positionX = parseInt(e.target.value);
            document.getElementById('position-x-value').textContent = this.positionX + 'px';
            this.updatePreview();
        });
        
        this.positionYSlider.addEventListener('input', (e) => {
            this.positionY = parseInt(e.target.value);
            document.getElementById('position-y-value').textContent = this.positionY + 'px';
            this.updatePreview();
        });
        
        this.scaleSlider.addEventListener('input', (e) => {
            this.scale = parseFloat(e.target.value);
            document.getElementById('scale-value').textContent = (this.scale * 100).toFixed(0) + '%';
            this.updatePreview();
        });
        
        // Upload de imagem
        document.getElementById('photo-upload').addEventListener('change', (e) => {
            this.loadImage(e.target.files[0]);
        });
        
        // Botões
        document.getElementById('reset-btn').addEventListener('click', () => {
            this.resetTransforms();
        });
        
        document.getElementById('preview-btn').addEventListener('click', () => {
            this.showFinalPreview();
        });
        
        document.getElementById('confirm-btn').addEventListener('click', () => {
            this.confirmChanges();
        });
        
        document.getElementById('cancel-btn').addEventListener('click', () => {
            this.closeModal();
        });
    }
    
    loadImage(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Por favor, selecione um arquivo de imagem válido.');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                this.originalImage = img;
                this.image = img;
                this.resetTransforms();
                this.updatePreview();
                
                // Mostrar área de edição
                document.getElementById('editor-area').classList.remove('hidden');
                document.getElementById('upload-area').classList.add('hidden');
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
    resetTransforms() {
        this.rotation = 0;
        this.positionX = 0;
        this.positionY = 0;
        this.scale = 1;
        
        // Atualizar sliders
        this.rotationSlider.value = 0;
        this.positionXSlider.value = 0;
        this.positionYSlider.value = 0;
        this.scaleSlider.value = 1;
        
        // Atualizar valores exibidos
        document.getElementById('rotation-value').textContent = '0°';
        document.getElementById('position-x-value').textContent = '0px';
        document.getElementById('position-y-value').textContent = '0px';
        document.getElementById('scale-value').textContent = '100%';
        
        this.updatePreview();
    }
    
    updatePreview() {
        if (!this.image) return;
        
        const canvas = this.canvas;
        const ctx = this.ctx;
        
        // Limpar canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Salvar estado
        ctx.save();
        
        // Mover para o centro do canvas
        ctx.translate(canvas.width / 2, canvas.height / 2);
        
        // Aplicar transformações
        ctx.rotate((this.rotation * Math.PI) / 180);
        ctx.scale(this.scale, this.scale);
        ctx.translate(this.positionX, this.positionY);
        
        // Desenhar imagem centralizada
        const imgWidth = this.image.width;
        const imgHeight = this.image.height;
        ctx.drawImage(this.image, -imgWidth / 2, -imgHeight / 2, imgWidth, imgHeight);
        
        // Restaurar estado
        ctx.restore();
    }
    
    showFinalPreview() {
        if (!this.image) return;
        
        // Criar canvas temporário para preview final
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        
        // Definir tamanho do canvas final (circular ou quadrado)
        const size = 200;
        tempCanvas.width = size;
        tempCanvas.height = size;
        
        // Aplicar clipping circular
        tempCtx.beginPath();
        tempCtx.arc(size / 2, size / 2, size / 2, 0, 2 * Math.PI);
        tempCtx.clip();
        
        // Aplicar transformações
        tempCtx.save();
        tempCtx.translate(size / 2, size / 2);
        tempCtx.rotate((this.rotation * Math.PI) / 180);
        tempCtx.scale(this.scale, this.scale);
        tempCtx.translate(this.positionX, this.positionY);
        
        // Desenhar imagem
        const imgWidth = this.image.width;
        const imgHeight = this.image.height;
        tempCtx.drawImage(this.image, -imgWidth / 2, -imgHeight / 2, imgWidth, imgHeight);
        tempCtx.restore();
        
        // Mostrar preview final
        const previewImg = document.getElementById('final-preview-img');
        previewImg.src = tempCanvas.toDataURL();
        previewImg.classList.remove('hidden');
        
        // Mostrar área de preview final
        document.getElementById('final-preview-area').classList.remove('hidden');
    }
    
    confirmChanges() {
        if (!this.image) return;
        
        // Criar canvas final
        const finalCanvas = document.createElement('canvas');
        const finalCtx = finalCanvas.getContext('2d');
        
        const size = 400; // Tamanho final da imagem
        finalCanvas.width = size;
        finalCanvas.height = size;
        
        // Aplicar transformações
        finalCtx.save();
        finalCtx.translate(size / 2, size / 2);
        finalCtx.rotate((this.rotation * Math.PI) / 180);
        finalCtx.scale(this.scale, this.scale);
        finalCtx.translate(this.positionX, this.positionY);
        
        // Desenhar imagem
        const imgWidth = this.image.width;
        const imgHeight = this.image.height;
        finalCtx.drawImage(this.image, -imgWidth / 2, -imgHeight / 2, imgWidth, imgHeight);
        finalCtx.restore();
        
        // Converter para blob e enviar
        finalCanvas.toBlob((blob) => {
            const formData = new FormData();
            formData.append('image', blob, 'profile-image.png');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Enviar via AJAX
            fetch('/profile/image/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar imagem na página
                    const currentImg = document.querySelector('.current-profile-image');
                    if (currentImg) {
                        currentImg.src = data.image_url + '?t=' + Date.now();
                    }
                    
                    this.closeModal();
                    alert('Foto de perfil atualizada com sucesso!');
                } else {
                    alert('Erro ao atualizar foto: ' + (data.message || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar imagem.');
            });
        }, 'image/png', 0.9);
    }
    
    openModal(currentImageSrc = null) {
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        if (currentImageSrc) {
            const img = new Image();
            img.onload = () => {
                this.originalImage = img;
                this.image = img;
                this.resetTransforms();
                this.updatePreview();
                
                // Mostrar área de edição
                document.getElementById('editor-area').classList.remove('hidden');
                document.getElementById('upload-area').classList.add('hidden');
            };
            img.src = currentImageSrc;
        } else {
            // Mostrar área de upload
            document.getElementById('upload-area').classList.remove('hidden');
            document.getElementById('editor-area').classList.add('hidden');
        }
        
        // Esconder preview final
        document.getElementById('final-preview-area').classList.add('hidden');
    }
    
    closeModal() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Reset
        this.image = null;
        this.originalImage = null;
        document.getElementById('photo-upload').value = '';
        document.getElementById('upload-area').classList.remove('hidden');
        document.getElementById('editor-area').classList.add('hidden');
        document.getElementById('final-preview-area').classList.add('hidden');
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    window.photoEditor = new PhotoEditor();
});

// Funções globais para interação com o HTML
function openPhotoEditor(currentImageSrc = null) {
    if (window.photoEditor) {
        window.photoEditor.openModal(currentImageSrc);
    }
}

function closePhotoEditor() {
    if (window.photoEditor) {
        window.photoEditor.closeModal();
    }
}