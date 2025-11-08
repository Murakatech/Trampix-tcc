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
    passwordMinLength: 'A senha deve ter pelo menos 8 caracteres.',
    passwordSpecialChar: 'A senha deve conter pelo menos 1 caractere especial (!@#$%^&*).',
    passwordUppercase: 'A senha deve conter pelo menos 1 letra maiúscula.',
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
            // Normalizar valores mascarados antes do envio
            this.normalizeMaskedValues(form);
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

        // Validação específica de senha
        if (input.name === 'password' && value) {
            const passwordValidation = this.validatePassword(value);
            if (!passwordValidation.isValid) {
                this.showError(input, passwordValidation.message);
                return false;
            }
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

    validatePassword(password) {
        // Regra 1: Mínimo de 8 caracteres
        if (password.length < 8) {
            return {
                isValid: false,
                message: messages.passwordMinLength
            };
        }

        // Regra 2: Pelo menos 1 caractere especial
        const specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
        if (!specialCharRegex.test(password)) {
            return {
                isValid: false,
                message: messages.passwordSpecialChar
            };
        }

        // Regra 3: Pelo menos 1 letra maiúscula
        const uppercaseRegex = /[A-Z]/;
        if (!uppercaseRegex.test(password)) {
            return {
                isValid: false,
                message: messages.passwordUppercase
            };
        }

        return {
            isValid: true,
            message: ''
        };
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

    /**
     * Normaliza valores de campos mascarados para o formato esperado no backend
     */
    normalizeMaskedValues(form) {
        // BR Currency: "R$ 1.234,56" -> "1234.56" (respeita vírgula se presente)
        const currencyInputs = form.querySelectorAll('input[data-mask="br-currency"]');
        currencyInputs.forEach((input) => {
            let raw = (input.value || '').replace(/^R\$\s*/, '');
            if (!raw) { input.value = ''; return; }
            raw = raw.replace(/\./g, '').replace(',', '.');
            // Manter apenas números e um ponto decimal
            raw = raw.replace(/[^0-9.]/g, '');
            // Se houver múltiplos pontos, manter o primeiro
            const firstDot = raw.indexOf('.');
            if (firstDot !== -1) {
                const before = raw.slice(0, firstDot + 1);
                const after = raw.slice(firstDot + 1).replace(/\./g, '');
                raw = before + after;
            }
            input.value = raw;
        });
    }
}

// Inicializa o validador quando o script é carregado
new TrampixValidator();

// Exporta para uso em outros módulos se necessário
export default TrampixValidator;

// Máscara progressiva para telefone/WhatsApp BR: (99) 99999-9999
(function initWhatsappMask(){
    const applyMask = (value) => {
        const digits = value.replace(/\D/g, '').slice(0, 11); // DDD (2) + número (até 9)
        if (digits.length === 0) return '';

        // Até 2 dígitos: começa a formar o DDD
        if (digits.length <= 2) {
            return `(${digits}`;
        }

        const ddd = digits.slice(0, 2);
        const afterDdd = digits.slice(2);

        // 3 a 6 dígitos após DDD: exibe sem hífen
        if (afterDdd.length <= 4) {
            return `(${ddd}) ${afterDdd}`;
        }

        // 10 dígitos totais (8 após DDD): formato fixo (99) 9999-9999
        if (digits.length <= 10) {
            return `(${ddd}) ${digits.slice(2, 6)}-${digits.slice(6, 10)}`;
        }

        // 11 dígitos totais (9 após DDD): formato celular (99) 99999-9999
        return `(${ddd}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
    };

    const handleInput = (el) => {
        el.value = applyMask(el.value);
        // manter o cursor no fim para evitar saltos indesejados
        const len = el.value.length;
        try { el.setSelectionRange(len, len); } catch (_) {}
    };

    const init = () => {
        const inputs = document.querySelectorAll('input[data-mask="br-phone"]');
        inputs.forEach((input) => {
            input.addEventListener('input', () => handleInput(input));
            // aplica máscara ao carregar
            if (input.value) {
                input.value = applyMask(input.value);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

// Máscara para moeda BR (R$ 1.234,56)
(function initCurrencyMask(){
    // Formatação BRL sem "empurrar" dígitos para centavos automaticamente.
    // O usuário digita normalmente e, se quiser decimais, usa a vírgula.
    const formatBRL = (value) => {
        const prefix = 'R$ ';
        if (!value) return '';
        // Remover prefixo e espaços
        const raw = String(value).replace(/^R\$\s*/, '');
        // Separar parte inteira e decimais (se o usuário digitou vírgula)
        const parts = raw.split(',');
        let integer = (parts[0] || '').replace(/\D/g, '');
        let decimals = (parts[1] || '').replace(/\D/g, '').slice(0, 2);
        if (!integer) return '';
        const integerFormatted = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return prefix + integerFormatted + (decimals ? ',' + decimals : '');
    };

    const handleInput = (el) => {
        el.value = formatBRL(el.value);
        // Manter o cursor no fim para evitar comportamento estranho ao digitar
        const len = el.value.length;
        try { el.setSelectionRange(len, len); } catch (_) {}
    };

    const init = () => {
        const inputs = document.querySelectorAll('input[data-mask="br-currency"]');
        inputs.forEach((input) => {
            input.addEventListener('input', () => handleInput(input));
            // aplica máscara ao carregar
            if (input.value) {
                input.value = formatBRL(input.value);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();