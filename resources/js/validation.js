/**
 * Sistema de Validação Personalizada Trampix
 * Substitui validações nativas do navegador por mensagens estilizadas
 */

// Mensagens de erro padrão
const messages = {
    required: 'Este campo é obrigatório.',
    email: 'Informe um endereço de e-mail válido.',
    passwordConfirm: 'As senhas não coincidem.',
    minLength: 'Este campo deve ter pelo menos {min} caracteres.',
};

class TrampixValidator {
    constructor() {
        this.init();
    }

    init() {
        // Aguarda o DOM estar carregado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupValidation());
        } else {
            this.setupValidation();
        }
    }

    setupValidation() {
        // Intercepta todos os formulários da página
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            // Remove validação nativa se não tiver novalidate
            if (!form.hasAttribute('novalidate')) {
                form.setAttribute('novalidate', '');
            }

            // Adiciona listener de submit
            form.addEventListener('submit', (e) => this.handleSubmit(e, form));

            // Adiciona listeners nos campos para remover erros em tempo real
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', () => this.clearFieldError(input));
                input.addEventListener('focus', () => this.clearFieldError(input));
                input.addEventListener('blur', () => this.validateField(input));
            });
        });
    }

    handleSubmit(event, form) {
        event.preventDefault();
        
        // Remove todos os erros existentes
        this.clearAllErrors(form);
        
        // Valida todos os campos
        const isValid = this.validateForm(form);
        
        if (isValid) {
            // Se válido, submete o formulário
            form.submit();
        }
    }

    validateForm(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        const required = input.hasAttribute('required');
        const minLength = input.getAttribute('minlength');
        
        // Validação de campo obrigatório
        if (required && !value) {
            this.showError(input, messages.required);
            return false;
        }

        // Se campo não é obrigatório e está vazio, não valida
        if (!required && !value) {
            return true;
        }

        // Validação de e-mail
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showError(input, messages.email);
                return false;
            }
        }

        // Validação de comprimento mínimo
        if (minLength && value.length < parseInt(minLength)) {
            const message = messages.minLength.replace('{min}', minLength);
            this.showError(input, message);
            return false;
        }

        // Validação de confirmação de senha
        if (input.name === 'password_confirmation' || input.name === 'confirm_password') {
            const passwordField = input.form.querySelector('input[name="password"]');
            if (passwordField && value !== passwordField.value) {
                this.showError(input, messages.passwordConfirm);
                return false;
            }
        }

        return true;
    }

    showError(input, message) {
        // Remove erro existente
        this.clearFieldError(input);

        // Cria elemento de erro
        const errorDiv = document.createElement('div');
        errorDiv.className = 'trampix-validation-error text-red-600 text-sm mt-1 font-medium transition-all duration-300 opacity-0 transform translate-y-[-10px]';
        errorDiv.textContent = message;
        errorDiv.setAttribute('data-field-error', input.name || input.id);

        // Insere após o campo
        const container = input.closest('div') || input.parentNode;
        container.appendChild(errorDiv);

        // Adiciona classe de erro ao input
        input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.remove('border-gray-300', 'focus:border-purple-500', 'focus:ring-purple-500');

        // Anima a entrada
        requestAnimationFrame(() => {
            errorDiv.classList.remove('opacity-0', 'translate-y-[-10px]');
            errorDiv.classList.add('opacity-100', 'translate-y-0');
        });
    }

    clearFieldError(input) {
        // Remove mensagem de erro
        const container = input.closest('div') || input.parentNode;
        const existingError = container.querySelector(`[data-field-error="${input.name || input.id}"]`);
        
        if (existingError) {
            // Anima a saída
            existingError.classList.add('opacity-0', 'transform', 'translate-y-[-10px]');
            setTimeout(() => {
                if (existingError.parentNode) {
                    existingError.parentNode.removeChild(existingError);
                }
            }, 300);
        }

        // Remove classes de erro do input
        input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.add('border-gray-300', 'focus:border-purple-500', 'focus:ring-purple-500');
    }

    clearAllErrors(form) {
        const errors = form.querySelectorAll('.trampix-validation-error');
        errors.forEach(error => {
            error.classList.add('opacity-0', 'transform', 'translate-y-[-10px]');
            setTimeout(() => {
                if (error.parentNode) {
                    error.parentNode.removeChild(error);
                }
            }, 300);
        });

        // Remove classes de erro de todos os inputs
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            input.classList.add('border-gray-300', 'focus:border-purple-500', 'focus:ring-purple-500');
        });
    }
}

// Inicializa o validador quando o script é carregado
new TrampixValidator();

// Exporta para uso em outros módulos se necessário
export default TrampixValidator;