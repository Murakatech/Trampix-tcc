// Upload de Currículo - Trampix
class CvUploader {
    constructor() {
        this.currentFile = null;
        this.isUploading = false;
        this.allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];
        this.maxFileSize = 10 * 1024 * 1024; // 10MB
        
        this.initializeElements();
        this.bindEvents();
    }

    initializeElements() {
        this.uploadArea = document.getElementById('cvUploadArea');
        this.uploadInitial = document.getElementById('cvUploadInitial');
        this.fileSelected = document.getElementById('cvFileSelected');
        this.uploadProgress = document.getElementById('cvUploadProgress');
        this.uploadSuccess = document.getElementById('cvUploadSuccess');
        
        this.fileInput = document.getElementById('cvFileInput');
        this.fileName = document.getElementById('cvFileName');
        this.fileSize = document.getElementById('cvFileSize');
        this.fileStatus = document.getElementById('cvFileStatus');
        this.fileIcon = document.getElementById('cvFileIcon');
        
        this.previewArea = document.getElementById('cvPreviewArea');
        this.previewFrame = document.getElementById('cvPreviewFrame');
        this.previewToggleText = document.getElementById('cvPreviewToggleText');
        
        this.confirmButton = document.getElementById('cvConfirmButton');
        this.progressBar = document.getElementById('cvProgressBar');
        this.progressText = document.getElementById('cvProgressText');
    }

    bindEvents() {
        // Prevenir comportamentos padrão do navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.uploadArea?.addEventListener(eventName, this.preventDefaults, false);
            document.body.addEventListener(eventName, this.preventDefaults, false);
        });

        // Destacar área de drop
        ['dragenter', 'dragover'].forEach(eventName => {
            this.uploadArea?.addEventListener(eventName, () => this.highlight(), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.uploadArea?.addEventListener(eventName, () => this.unhighlight(), false);
        });
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight() {
        this.uploadArea?.classList.add('border-indigo-500', 'bg-indigo-50');
    }

    unhighlight() {
        this.uploadArea?.classList.remove('border-indigo-500', 'bg-indigo-50');
    }

    validateFile(file) {
        const errors = [];

        // Verificar tipo de arquivo
        if (!this.allowedTypes.includes(file.type)) {
            errors.push('Tipo de arquivo não suportado. Use apenas PDF ou DOCX.');
        }

        // Verificar tamanho
        if (file.size > this.maxFileSize) {
            errors.push('Arquivo muito grande. O tamanho máximo é 10MB.');
        }

        // Verificar se o arquivo não está vazio
        if (file.size === 0) {
            errors.push('O arquivo está vazio.');
        }

        return errors;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    getFileIcon(file) {
        if (file.type === 'application/pdf') {
            return {
                bgColor: 'bg-red-100',
                iconColor: 'text-red-600',
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>`
            };
        } else {
            return {
                bgColor: 'bg-blue-100',
                iconColor: 'text-blue-600',
                icon: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>`
            };
        }
    }

    displayFile(file) {
        this.currentFile = file;
        
        // Atualizar informações do arquivo
        this.fileName.textContent = file.name;
        this.fileSize.textContent = this.formatFileSize(file.size);
        
        // Atualizar ícone
        const iconInfo = this.getFileIcon(file);
        this.fileIcon.className = `w-12 h-12 ${iconInfo.bgColor} rounded-lg flex items-center justify-center`;
        this.fileIcon.innerHTML = `<div class="${iconInfo.iconColor}">${iconInfo.icon}</div>`;
        
        // Mostrar pré-visualização para PDFs
        if (file.type === 'application/pdf') {
            this.showPreview(file);
        } else {
            this.hidePreview();
        }
        
        // Alternar estados
        this.uploadInitial.classList.add('hidden');
        this.fileSelected.classList.remove('hidden');
        this.uploadProgress.classList.add('hidden');
        this.uploadSuccess.classList.add('hidden');
    }

    showPreview(file) {
        const fileURL = URL.createObjectURL(file);
        this.previewFrame.src = fileURL;
        this.previewArea.classList.remove('hidden');
    }

    hidePreview() {
        this.previewArea.classList.add('hidden');
        this.previewFrame.src = '';
    }

    togglePreview() {
        const isHidden = this.previewArea.querySelector('#cvPreviewContent').classList.contains('hidden');
        
        if (isHidden) {
            this.previewArea.querySelector('#cvPreviewContent').classList.remove('hidden');
            this.previewToggleText.textContent = 'Ocultar';
        } else {
            this.previewArea.querySelector('#cvPreviewContent').classList.add('hidden');
            this.previewToggleText.textContent = 'Mostrar';
        }
    }

    removeFile() {
        this.currentFile = null;
        this.fileInput.value = '';
        
        // Limpar preview
        this.hidePreview();
        
        // Voltar ao estado inicial
        this.uploadInitial.classList.remove('hidden');
        this.fileSelected.classList.add('hidden');
        this.uploadProgress.classList.add('hidden');
        this.uploadSuccess.classList.add('hidden');
    }

    async uploadFile() {
        if (!this.currentFile || this.isUploading) return;

        this.isUploading = true;
        
        // Mostrar progresso
        this.fileSelected.classList.add('hidden');
        this.uploadProgress.classList.remove('hidden');
        
        // Criar FormData
        const formData = new FormData();
        formData.append('cv_file', this.currentFile);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

        try {
            // Simular progresso
            this.updateProgress(0);
            
            const xhr = new XMLHttpRequest();
            
            // Monitorar progresso
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    this.updateProgress(percentComplete);
                }
            });

            // Configurar resposta
            xhr.onload = () => {
                if (xhr.status === 200) {
                    this.updateProgress(100);
                    setTimeout(() => {
                        this.showSuccess();
                    }, 500);
                } else {
                    throw new Error('Erro no upload');
                }
            };

            xhr.onerror = () => {
                throw new Error('Erro de conexão');
            };

            // Enviar requisição
            xhr.open('POST', '/profile/cv/upload');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(formData);

        } catch (error) {
            console.error('Erro no upload:', error);
            alert('Erro ao enviar o currículo. Tente novamente.');
            
            // Voltar ao estado de seleção
            this.uploadProgress.classList.add('hidden');
            this.fileSelected.classList.remove('hidden');
            this.isUploading = false;
        }
    }

    updateProgress(percent) {
        const roundedPercent = Math.round(percent);
        this.progressBar.style.width = `${roundedPercent}%`;
        this.progressText.textContent = `${roundedPercent}%`;
    }

    showSuccess() {
        this.uploadProgress.classList.add('hidden');
        this.uploadSuccess.classList.remove('hidden');
        this.isUploading = false;
        
        // Recarregar a página após 2 segundos para mostrar o novo currículo
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }

    reset() {
        this.removeFile();
        this.isUploading = false;
    }

    handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;

        // Validar arquivo
        const errors = this.validateFile(file);
        if (errors.length > 0) {
            alert(errors.join('\n'));
            input.value = '';
            return;
        }

        this.displayFile(file);
    }

    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            const file = files[0];
            
            // Validar arquivo
            const errors = this.validateFile(file);
            if (errors.length > 0) {
                alert(errors.join('\n'));
                return;
            }

            // Atualizar input file
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.fileInput.files = dataTransfer.files;

            this.displayFile(file);
        }
    }

    async deleteCv() {
        if (!confirm('Tem certeza que deseja remover o currículo atual?')) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            formData.append('_method', 'DELETE');

            const response = await fetch('/profile/cv/delete', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Erro ao remover currículo');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao remover o currículo. Tente novamente.');
        }
    }
}

// Instância global do uploader
let cvUploader;

// Funções globais para compatibilidade com o HTML
function handleCvFileSelect(input) {
    if (cvUploader) {
        cvUploader.handleFileSelect(input);
    }
}

function handleCvDrop(e) {
    e.preventDefault();
    if (cvUploader) {
        cvUploader.handleDrop(e);
    }
}

function handleCvDragOver(e) {
    e.preventDefault();
}

function handleCvDragEnter(e) {
    e.preventDefault();
    if (cvUploader) {
        cvUploader.highlight();
    }
}

function handleCvDragLeave(e) {
    e.preventDefault();
    if (cvUploader) {
        cvUploader.unhighlight();
    }
}

function removeCvFile() {
    if (cvUploader) {
        cvUploader.removeFile();
    }
}

function confirmCvUpload() {
    if (cvUploader) {
        cvUploader.uploadFile();
    }
}

function toggleCvPreview() {
    if (cvUploader) {
        cvUploader.togglePreview();
    }
}

function resetCvUploader() {
    if (cvUploader) {
        cvUploader.reset();
    }
}

function deleteCv() {
    if (cvUploader) {
        cvUploader.deleteCv();
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    cvUploader = new CvUploader();
});