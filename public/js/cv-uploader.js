class CvUploader {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.dropZone = this.container.querySelector('.cv-drop-zone');
        this.fileInput = this.container.querySelector('.cv-file-input');
        this.selectedFile = null;
        this.currentState = 'initial'; // initial, selected, uploading, success
        
        this.initEventListeners();
    }
    
    initEventListeners() {
        // Drag & Drop events
        this.dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });
        
        this.dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            this.dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        this.dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            this.dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                this.handleFile(files[0]);
            }
        });
        
        // Click to upload
        this.dropZone.addEventListener('click', () => {
            if (this.currentState === 'initial') {
                this.fileInput.click();
            }
        });
        
        // File input change
        this.fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                this.handleFile(e.target.files[0]);
            }
        });
    }
    
    handleFile(file) {
        // Validar tipo de arquivo
        const allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!allowedTypes.includes(file.type)) {
            alert('Por favor, selecione apenas arquivos PDF ou DOCX.');
            return;
        }
        
        // Validar tamanho (máx 10MB)
        const maxSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxSize) {
            alert('O arquivo deve ter no máximo 10MB.');
            return;
        }
        
        this.selectedFile = file;
        this.showSelectedState();
    }
    
    showSelectedState() {
        this.currentState = 'selected';
        
        // Atualizar interface
        const initialState = this.container.querySelector('.cv-initial-state');
        const selectedState = this.container.querySelector('.cv-selected-state');
        
        initialState.classList.add('hidden');
        selectedState.classList.remove('hidden');
        
        // Atualizar informações do arquivo
        const fileName = this.container.querySelector('.cv-file-name');
        const fileSize = this.container.querySelector('.cv-file-size');
        const fileIcon = this.container.querySelector('.cv-file-icon');
        
        fileName.textContent = this.selectedFile.name;
        fileSize.textContent = this.formatFileSize(this.selectedFile.size);
        
        // Ícone baseado no tipo
        if (this.selectedFile.type === 'application/pdf') {
            fileIcon.innerHTML = `
                <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 18h12V6l-4-4H4v16zm8-14v3h3l-3-3z"/>
                </svg>
            `;
        } else {
            fileIcon.innerHTML = `
                <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 18h12V6l-4-4H4v16zm8-14v3h3l-3-3z"/>
                </svg>
            `;
        }
        
        // Preview para PDF
        if (this.selectedFile.type === 'application/pdf') {
            this.showPdfPreview();
        }
    }
    
    showPdfPreview() {
        const previewArea = this.container.querySelector('.cv-preview-area');
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const pdfData = e.target.result;
            const iframe = document.createElement('iframe');
            iframe.src = pdfData;
            iframe.className = 'w-full h-40 border rounded';
            
            previewArea.innerHTML = '';
            previewArea.appendChild(iframe);
            previewArea.classList.remove('hidden');
        };
        
        reader.readAsDataURL(this.selectedFile);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    removeFile() {
        this.selectedFile = null;
        this.currentState = 'initial';
        this.fileInput.value = '';
        
        // Resetar interface
        const initialState = this.container.querySelector('.cv-initial-state');
        const selectedState = this.container.querySelector('.cv-selected-state');
        const previewArea = this.container.querySelector('.cv-preview-area');
        
        initialState.classList.remove('hidden');
        selectedState.classList.add('hidden');
        previewArea.classList.add('hidden');
        previewArea.innerHTML = '';
    }
    
    replaceFile() {
        this.fileInput.click();
    }
    
    confirmUpload() {
        if (!this.selectedFile) return;
        
        this.currentState = 'uploading';
        this.showUploadingState();
        
        const formData = new FormData();
        formData.append('cv', this.selectedFile);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Simular progresso
        const progressBar = this.container.querySelector('.cv-progress-bar');
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 200);
        
        // Enviar arquivo
        fetch('/profile/cv/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            
            setTimeout(() => {
                if (data.success) {
                    this.showSuccessState();
                } else {
                    alert('Erro ao fazer upload: ' + (data.message || 'Erro desconhecido'));
                    this.showSelectedState();
                }
            }, 500);
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Erro:', error);
            alert('Erro ao enviar arquivo.');
            this.showSelectedState();
        });
    }
    
    showUploadingState() {
        const selectedState = this.container.querySelector('.cv-selected-state');
        const uploadingState = this.container.querySelector('.cv-uploading-state');
        
        selectedState.classList.add('hidden');
        uploadingState.classList.remove('hidden');
        
        // Resetar barra de progresso
        const progressBar = this.container.querySelector('.cv-progress-bar');
        progressBar.style.width = '0%';
    }
    
    showSuccessState() {
        this.currentState = 'success';
        
        const uploadingState = this.container.querySelector('.cv-uploading-state');
        const successState = this.container.querySelector('.cv-success-state');
        
        uploadingState.classList.add('hidden');
        successState.classList.remove('hidden');
        
        // Atualizar nome do arquivo no estado de sucesso
        const successFileName = this.container.querySelector('.cv-success-file-name');
        if (successFileName) {
            successFileName.textContent = this.selectedFile.name;
        }
    }
    
    deleteCv() {
        if (!confirm('Tem certeza que deseja remover o currículo?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'DELETE');
        
        fetch('/profile/cv/delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.removeFile();
                alert('Currículo removido com sucesso!');
            } else {
                alert('Erro ao remover currículo: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover currículo.');
        });
    }
}

// Funções globais para interação com o HTML
function removeCvFile(containerId) {
    const uploader = window.cvUploaders && window.cvUploaders[containerId];
    if (uploader) {
        uploader.removeFile();
    }
}

function replaceCvFile(containerId) {
    const uploader = window.cvUploaders && window.cvUploaders[containerId];
    if (uploader) {
        uploader.replaceFile();
    }
}

function confirmCvUpload(containerId) {
    const uploader = window.cvUploaders && window.cvUploaders[containerId];
    if (uploader) {
        uploader.confirmUpload();
    }
}

function deleteCv(containerId) {
    const uploader = window.cvUploaders && window.cvUploaders[containerId];
    if (uploader) {
        uploader.deleteCv();
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos os uploaders de CV na página
    const cvContainers = document.querySelectorAll('[id^="cv-uploader-"]');
    window.cvUploaders = {};
    
    cvContainers.forEach(container => {
        const uploader = new CvUploader(container.id);
        window.cvUploaders[container.id] = uploader;
    });
});