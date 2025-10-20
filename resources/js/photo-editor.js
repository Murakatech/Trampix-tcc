// Editor de Foto de Perfil - Trampix
class PhotoEditor {
    constructor() {
        this.currentImage = null;
        this.originalImage = null;
        this.transforms = {
            rotation: 0,
            positionX: 0,
            positionY: 0,
            scale: 1
        };
        this.isImageLoaded = false;
        
        this.initializeElements();
        this.bindEvents();
    }

    initializeElements() {
        this.modal = document.getElementById('photoEditorModal');
        this.imagePreview = document.getElementById('imagePreview');
        this.imagePlaceholder = document.getElementById('imagePlaceholder');
        this.finalPreview = document.getElementById('finalPreview');
        this.confirmButton = document.getElementById('confirmButton');
        
        // Sliders
        this.rotationSlider = document.getElementById('rotationSlider');
        this.positionXSlider = document.getElementById('positionXSlider');
        this.positionYSlider = document.getElementById('positionYSlider');
        this.scaleSlider = document.getElementById('scaleSlider');
        
        // Value displays
        this.rotationValue = document.getElementById('rotationValue');
        this.positionXValue = document.getElementById('positionXValue');
        this.positionYValue = document.getElementById('positionYValue');
        this.scaleValue = document.getElementById('scaleValue');
    }

    bindEvents() {
        // Slider events
        this.rotationSlider?.addEventListener('input', () => this.updateImageTransform());
        this.positionXSlider?.addEventListener('input', () => this.updateImageTransform());
        this.positionYSlider?.addEventListener('input', () => this.updateImageTransform());
        this.scaleSlider?.addEventListener('input', () => this.updateImageTransform());
    }

    open(currentImageSrc = null) {
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        if (currentImageSrc) {
            this.loadExistingImage(currentImageSrc);
        } else {
            this.resetEditor();
        }
    }

    close() {
        this.modal.classList.add('hidden');
        document.body.style.overflow = '';
        this.resetEditor();
    }

    loadExistingImage(src) {
        const img = new Image();
        img.onload = () => {
            this.originalImage = img;
            this.currentImage = img;
            this.imagePreview.src = src;
            this.imagePreview.style.display = 'block';
            this.imagePlaceholder.style.display = 'none';
            this.isImageLoaded = true;
            this.updateImageTransform();
            this.updateFinalPreview();
        };
        img.src = src;
    }

    loadImageForEditing(input) {
        const file = input.files[0];
        if (!file) return;

        // Validar tipo de arquivo
        if (!file.type.startsWith('image/')) {
            alert('Por favor, selecione apenas arquivos de imagem.');
            return;
        }

        // Validar tamanho (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('A imagem deve ter no máximo 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                this.originalImage = img;
                this.currentImage = img;
                this.imagePreview.src = e.target.result;
                this.imagePreview.style.display = 'block';
                this.imagePlaceholder.style.display = 'none';
                this.isImageLoaded = true;
                this.resetTransforms();
                this.updateImageTransform();
                this.updateFinalPreview();
                this.enableConfirmButton();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    updateImageTransform() {
        if (!this.isImageLoaded) return;

        // Atualizar valores dos transforms
        this.transforms.rotation = parseFloat(this.rotationSlider.value);
        this.transforms.positionX = parseFloat(this.positionXSlider.value);
        this.transforms.positionY = parseFloat(this.positionYSlider.value);
        this.transforms.scale = parseFloat(this.scaleSlider.value);

        // Atualizar displays dos valores
        this.rotationValue.textContent = `${this.transforms.rotation}°`;
        this.positionXValue.textContent = this.transforms.positionX;
        this.positionYValue.textContent = this.transforms.positionY;
        this.scaleValue.textContent = `${this.transforms.scale}x`;

        // Aplicar transformações na imagem
        const transform = `
            translate(${this.transforms.positionX}px, ${this.transforms.positionY}px) 
            rotate(${this.transforms.rotation}deg) 
            scale(${this.transforms.scale})
        `;
        
        this.imagePreview.style.transform = transform;
        
        // Atualizar preview final
        this.updateFinalPreview();
    }

    updateFinalPreview() {
        if (!this.isImageLoaded || !this.finalPreview) return;

        const canvas = this.finalPreview;
        const ctx = canvas.getContext('2d');
        const size = 80;

        // Limpar canvas
        ctx.clearRect(0, 0, size, size);

        // Salvar estado do contexto
        ctx.save();

        // Configurar clipping para formato circular ou quadrado
        const profileType = document.querySelector('input[name="profile_type"]')?.value;
        if (profileType === 'freelancer') {
            // Clipping circular para freelancer
            ctx.beginPath();
            ctx.arc(size/2, size/2, size/2, 0, 2 * Math.PI);
            ctx.clip();
        } else {
            // Clipping com bordas arredondadas para empresa
            this.roundRect(ctx, 0, 0, size, size, 8);
            ctx.clip();
        }

        // Mover para o centro do canvas
        ctx.translate(size/2, size/2);

        // Aplicar transformações
        ctx.rotate((this.transforms.rotation * Math.PI) / 180);
        ctx.scale(this.transforms.scale, this.transforms.scale);
        ctx.translate(this.transforms.positionX * 0.5, this.transforms.positionY * 0.5);

        // Calcular dimensões da imagem para caber no canvas
        const img = this.originalImage;
        const aspectRatio = img.width / img.height;
        let drawWidth, drawHeight;

        if (aspectRatio > 1) {
            drawWidth = size;
            drawHeight = size / aspectRatio;
        } else {
            drawWidth = size * aspectRatio;
            drawHeight = size;
        }

        // Desenhar a imagem
        ctx.drawImage(img, -drawWidth/2, -drawHeight/2, drawWidth, drawHeight);

        // Restaurar estado do contexto
        ctx.restore();
    }

    roundRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    rotateImage(degrees) {
        const newRotation = this.transforms.rotation + degrees;
        this.rotationSlider.value = Math.max(-180, Math.min(180, newRotation));
        this.updateImageTransform();
    }

    resetRotation() {
        this.rotationSlider.value = 0;
        this.updateImageTransform();
    }

    resetPosition() {
        this.positionXSlider.value = 0;
        this.positionYSlider.value = 0;
        this.updateImageTransform();
    }

    scaleImage(factor) {
        const newScale = this.transforms.scale * factor;
        this.scaleSlider.value = Math.max(0.5, Math.min(3, newScale));
        this.updateImageTransform();
    }

    resetScale() {
        this.scaleSlider.value = 1;
        this.updateImageTransform();
    }

    resetTransforms() {
        this.rotationSlider.value = 0;
        this.positionXSlider.value = 0;
        this.positionYSlider.value = 0;
        this.scaleSlider.value = 1;
        this.updateImageTransform();
    }

    resetAllTransforms() {
        this.resetTransforms();
    }

    resetEditor() {
        this.isImageLoaded = false;
        this.currentImage = null;
        this.originalImage = null;
        this.imagePreview.style.display = 'none';
        this.imagePlaceholder.style.display = 'block';
        this.resetTransforms();
        this.disableConfirmButton();
        
        // Limpar canvas
        if (this.finalPreview) {
            const ctx = this.finalPreview.getContext('2d');
            ctx.clearRect(0, 0, this.finalPreview.width, this.finalPreview.height);
        }
    }

    enableConfirmButton() {
        this.confirmButton.disabled = false;
        this.confirmButton.classList.remove('opacity-50', 'cursor-not-allowed');
    }

    disableConfirmButton() {
        this.confirmButton.disabled = true;
        this.confirmButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    previewChanges() {
        if (!this.isImageLoaded) {
            alert('Selecione uma imagem primeiro.');
            return;
        }

        // Mostrar preview em uma nova janela ou modal
        const canvas = document.createElement('canvas');
        canvas.width = 300;
        canvas.height = 300;
        const ctx = canvas.getContext('2d');

        // Aplicar as mesmas transformações do preview final, mas em tamanho maior
        ctx.save();
        
        const profileType = document.querySelector('input[name="profile_type"]')?.value;
        if (profileType === 'freelancer') {
            ctx.beginPath();
            ctx.arc(150, 150, 150, 0, 2 * Math.PI);
            ctx.clip();
        } else {
            this.roundRect(ctx, 0, 0, 300, 300, 20);
            ctx.clip();
        }

        ctx.translate(150, 150);
        ctx.rotate((this.transforms.rotation * Math.PI) / 180);
        ctx.scale(this.transforms.scale, this.transforms.scale);
        ctx.translate(this.transforms.positionX * 1.5, this.transforms.positionY * 1.5);

        const img = this.originalImage;
        const aspectRatio = img.width / img.height;
        let drawWidth, drawHeight;

        if (aspectRatio > 1) {
            drawWidth = 300;
            drawHeight = 300 / aspectRatio;
        } else {
            drawWidth = 300 * aspectRatio;
            drawHeight = 300;
        }

        ctx.drawImage(img, -drawWidth/2, -drawHeight/2, drawWidth, drawHeight);
        ctx.restore();

        // Mostrar em uma nova janela
        const previewWindow = window.open('', '_blank', 'width=400,height=400');
        previewWindow.document.write(`
            <html>
                <head><title>Preview - ${profileType === 'freelancer' ? 'Foto de Perfil' : 'Logo da Empresa'}</title></head>
                <body style="margin:0; padding:20px; text-align:center; font-family:Arial,sans-serif;">
                    <h3>Preview da ${profileType === 'freelancer' ? 'Foto de Perfil' : 'Logo da Empresa'}</h3>
                    <div style="margin:20px 0;">
                        <img src="${canvas.toDataURL()}" style="border:2px solid #ddd; ${profileType === 'freelancer' ? 'border-radius:50%;' : 'border-radius:10px;'}">
                    </div>
                    <button onclick="window.close()" style="padding:8px 16px; background:#6366f1; color:white; border:none; border-radius:4px; cursor:pointer;">Fechar</button>
                </body>
            </html>
        `);
    }

    async confirmPhotoChanges() {
        if (!this.isImageLoaded) {
            alert('Selecione uma imagem primeiro.');
            return;
        }

        // Criar canvas final para gerar a imagem processada
        const canvas = document.createElement('canvas');
        canvas.width = 400;
        canvas.height = 400;
        const ctx = canvas.getContext('2d');

        // Aplicar transformações
        ctx.save();
        ctx.translate(200, 200);
        ctx.rotate((this.transforms.rotation * Math.PI) / 180);
        ctx.scale(this.transforms.scale, this.transforms.scale);
        ctx.translate(this.transforms.positionX * 2, this.transforms.positionY * 2);

        const img = this.originalImage;
        const aspectRatio = img.width / img.height;
        let drawWidth, drawHeight;

        if (aspectRatio > 1) {
            drawWidth = 400;
            drawHeight = 400 / aspectRatio;
        } else {
            drawWidth = 400 * aspectRatio;
            drawHeight = 400;
        }

        ctx.drawImage(img, -drawWidth/2, -drawHeight/2, drawWidth, drawHeight);
        ctx.restore();

        // Converter canvas para blob
        canvas.toBlob(async (blob) => {
            // Criar FormData
            const formData = new FormData();
            formData.append('profile_image', blob, 'edited-profile-image.png');
            formData.append('profile_type', document.querySelector('input[name="profile_type"]')?.value || 'freelancer');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            formData.append('_method', 'PATCH');

            try {
                // Mostrar loading
                this.confirmButton.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Salvando...
                `;
                this.confirmButton.disabled = true;

                // Enviar para o servidor
                const response = await fetch('/profile/image', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    // Sucesso - recarregar a página ou atualizar a imagem
                    window.location.reload();
                } else {
                    throw new Error('Erro ao salvar a imagem');
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao salvar a imagem. Tente novamente.');
                
                // Restaurar botão
                this.confirmButton.innerHTML = `
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirmar Alterações
                `;
                this.confirmButton.disabled = false;
            }
        }, 'image/png', 0.9);
    }
}

// Instância global do editor
let photoEditor;

// Funções globais para compatibilidade com o HTML
function openPhotoEditor(currentImageSrc = null) {
    if (!photoEditor) {
        photoEditor = new PhotoEditor();
    }
    photoEditor.open(currentImageSrc);
}

function closePhotoEditor() {
    if (photoEditor) {
        photoEditor.close();
    }
}

function loadImageForEditing(input) {
    if (photoEditor) {
        photoEditor.loadImageForEditing(input);
    }
}

function updateImageTransform() {
    if (photoEditor) {
        photoEditor.updateImageTransform();
    }
}

function rotateImage(degrees) {
    if (photoEditor) {
        photoEditor.rotateImage(degrees);
    }
}

function resetRotation() {
    if (photoEditor) {
        photoEditor.resetRotation();
    }
}

function resetPosition() {
    if (photoEditor) {
        photoEditor.resetPosition();
    }
}

function scaleImage(factor) {
    if (photoEditor) {
        photoEditor.scaleImage(factor);
    }
}

function resetScale() {
    if (photoEditor) {
        photoEditor.resetScale();
    }
}

function resetAllTransforms() {
    if (photoEditor) {
        photoEditor.resetAllTransforms();
    }
}

function previewChanges() {
    if (photoEditor) {
        photoEditor.previewChanges();
    }
}

function confirmPhotoChanges() {
    if (photoEditor) {
        photoEditor.confirmPhotoChanges();
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    photoEditor = new PhotoEditor();
});