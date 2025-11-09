@props([
    'actionType' => 'candidatura',
    'jobTitle' => '',
    'companyName' => '',
    'modalId' => 'actionConfirmationModal',
    'showTip' => true,
    'confirmText' => null
])

{{-- Modal de Confirmação de Ações Customizado --}}
<div id="{{ $modalId }}" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-2 sm:p-4"
     x-data="{ 
        open: false,
        actionType: '{{ $actionType }}',
        jobTitle: '{{ $jobTitle }}',
        companyName: '{{ $companyName }}'
     }"
     x-show="open"
     x-transition:enter="transition ease-out duration-500"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-title"
     aria-describedby="modal-description"
     aria-live="polite"
     tabindex="-1">
    
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-2 sm:mx-4 max-h-[90vh] overflow-y-auto transform transition-all duration-500"
         x-show="open"
         x-transition:enter="transition ease-out duration-500 delay-150"
         x-transition:enter-start="opacity-0 scale-90 translate-y-8 rotate-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0 rotate-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0 rotate-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4 rotate-1"
         @click.away="closeActionModal('{{ $modalId }}')"
         @keydown.escape="closeActionModal('{{ $modalId }}')"
         role="document">
        
        {{-- Header dinâmico baseado no actionType --}}
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-t-xl p-4 sm:p-6 text-center">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 shadow-lg transform transition-transform duration-200 hover:scale-105" 
                 role="img" 
                 aria-label="Ícone de {{ $actionType }}">
                @if($actionType === 'candidatura')
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @elseif($actionType === 'exclusao')
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                @else
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>
            <h3 id="modal-title" class="text-lg sm:text-xl font-bold text-white">
                @if($actionType === 'candidatura')
                    Confirmar Candidatura
                @elseif($actionType === 'exclusao')
                    Confirmar Exclusão
                @else
                    Confirmar Ação
                @endif
            </h3>
        </div>
        
        {{-- Conteúdo --}}
        <div class="p-4 sm:p-6">
            <div class="text-center mb-4 sm:mb-6">
                <p id="modal-description" class="text-gray-700 text-sm sm:text-base leading-relaxed">
                    @if($actionType === 'candidatura')
                        @if($jobTitle && $companyName)
                            Você está prestes a se candidatar à vaga <strong>{{ $jobTitle }}</strong> na empresa <strong>{{ $companyName }}</strong>. Deseja continuar?
                        @elseif($jobTitle)
                            Você está prestes a se candidatar à vaga <strong>{{ $jobTitle }}</strong>. Deseja continuar?
                        @else
                            Tem certeza que deseja enviar sua candidatura para esta vaga?
                        @endif
                    @else
                        Tem certeza que deseja realizar esta ação?
                    @endif
                </p>
                
                {{-- Dica adicional para candidatura --}}
                @if($actionType === 'candidatura' && $showTip)
                    <div class="mt-3 sm:mt-4 p-3 sm:p-4 bg-blue-50 border border-blue-200 rounded-lg" role="complementary" aria-label="Dica para candidatura">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-500 mt-0.5 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-left">
                                <p class="text-xs sm:text-sm text-blue-700 font-medium">Dica Trampix</p>
                                <p class="text-xs sm:text-sm text-blue-600 mt-1">
                                    Uma mensagem personalizada pode aumentar suas chances de ser selecionado!
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Botões de ação --}}
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button type="button" 
                        onclick="closeActionModal('{{ $modalId }}')" 
                        onmouseenter="window.ActionConfirmation.analytics.track('button_hover', {modal_id: '{{ $modalId }}', button_type: 'cancel'})"
                        class="flex-1 px-3 sm:px-4 py-2 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 text-sm sm:text-base"
                        aria-label="Cancelar ação"
                        tabindex="0">
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                
                <button type="button" 
                        onclick="confirmAction('{{ $modalId }}')" 
                        onmouseenter="window.ActionConfirmation.analytics.track('button_hover', {modal_id: '{{ $modalId }}', button_type: 'confirm'})"
                        class="flex-1 px-3 sm:px-4 py-2 sm:py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 shadow-lg text-sm sm:text-base"
                        aria-label="{{ $confirmText ? $confirmText : ('Confirmar ' . $actionType) }}"
                        tabindex="0">
                    @if($actionType === 'candidatura')
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        {{ $confirmText ?? 'Enviar Candidatura' }}
                    @elseif($actionType === 'exclusao')
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Confirmar Exclusão
                    @else
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirmar
                    @endif
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Sistema de gerenciamento de modais de ação
window.ActionConfirmation = window.ActionConfirmation || {
    pendingCallbacks: {},
    debug: {{ config('app.debug') ? 'true' : 'false' }},
    analyticsEndpoint: '', // Rota analytics.track não definida - desabilitada temporariamente
    analytics: {
        track: function(event, data) {
            const timestamp = new Date().toISOString();
            const sessionId = this.getSessionId();
            
            const analyticsData = {
                ...data,
                timestamp,
                session_id: sessionId,
                user_agent: navigator.userAgent,
                viewport: {
                    width: window.innerWidth,
                    height: window.innerHeight
                },
                page_url: window.location.href
            };
            
            // Integração com Google Analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', event, analyticsData);
            }
            
            // Integração com Mixpanel
            if (typeof mixpanel !== 'undefined') {
                mixpanel.track(event, analyticsData);
            }
            
            // Enviar para endpoint interno (se disponível)
            if (window.ActionConfirmation.analyticsEndpoint) {
                fetch(window.ActionConfirmation.analyticsEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        event,
                        data: analyticsData
                    })
                }).catch(err => console.warn('Analytics endpoint error:', err));
            }
            
            // Log para desenvolvimento
            if (window.ActionConfirmation.debug) {
                console.log('ActionConfirmation Analytics:', event, analyticsData);
            }
        },
        
        getSessionId: function() {
            let sessionId = sessionStorage.getItem('action_confirmation_session');
            if (!sessionId) {
                sessionId = 'ac_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('action_confirmation_session', sessionId);
            }
            return sessionId;
        },
        
        trackPerformance: function(event, startTime) {
            const endTime = performance.now();
            const duration = endTime - startTime;
            
            this.track('performance_' + event, {
                duration_ms: Math.round(duration),
                performance_timing: {
                    start: startTime,
                    end: endTime
                }
            });
        }
    }
};

function showActionModal(modalId, options = {}) {
     const startTime = performance.now();
     
     const modal = document.getElementById(modalId);
     if (!modal) return;

     // Armazenar elemento que tinha foco antes do modal
     window.ActionConfirmation.previousFocus = document.activeElement;

     // Armazenar callbacks
     window.ActionConfirmation.pendingCallbacks[modalId] = {
         onConfirm: options.onConfirm || null,
         onCancel: options.onCancel || null,
         actionType: options.actionType || 'candidatura',
         jobTitle: options.jobTitle || '',
         companyName: options.companyName || ''
     };

     // Atualizar mensagem se fornecida
     if (options.message) {
         const messageElement = modal.querySelector('#modal-description');
         if (messageElement) {
             messageElement.innerHTML = options.message;
         }
     }

     // Mostrar modal
     modal.classList.remove('hidden');
     if (modal._x_dataStack && modal._x_dataStack[0]) {
         modal._x_dataStack[0].open = true;
     }

     // Bloquear scroll do body
     document.body.style.overflow = 'hidden';

     // Analytics
     window.ActionConfirmation.analytics.track('modal_opened', {
         modal_id: modalId,
         action_type: options.actionType || 'candidatura',
         job_title: options.jobTitle || '',
         company_name: options.companyName || ''
     });

     // Foco para acessibilidade - focar no modal primeiro, depois no primeiro botão
     setTimeout(() => {
         modal.focus();
         const confirmButton = modal.querySelector('button[onclick*="confirmAction"]');
         if (confirmButton) {
             confirmButton.focus();
         }
         
         // Track performance
         window.ActionConfirmation.analytics.trackPerformance('modal_show', startTime);
     }, 100);

     // Configurar trap de foco
     setupFocusTrap(modal);
 }

function closeActionModal(modalId, reason = 'cancel') {
     const startTime = performance.now();
     
     const modal = document.getElementById(modalId);
     if (!modal) return;

     // Executar callback de cancelamento se existir
     const callbacks = window.ActionConfirmation.pendingCallbacks[modalId];
     if (reason === 'cancel' && callbacks && callbacks.onCancel) {
         callbacks.onCancel();
     }

     // Fechar modal
     if (modal._x_dataStack && modal._x_dataStack[0]) {
         modal._x_dataStack[0].open = false;
     }
     
     setTimeout(() => {
         modal.classList.add('hidden');
         
         // Restaurar scroll do body
         document.body.style.overflow = '';
         
         // Restaurar foco para o elemento anterior
         if (window.ActionConfirmation.previousFocus) {
             window.ActionConfirmation.previousFocus.focus();
             window.ActionConfirmation.previousFocus = null;
         }
         
         // Track performance
         window.ActionConfirmation.analytics.trackPerformance('modal_close', startTime);
     }, 200);

     // Analytics
     const eventName = reason === 'confirm' ? 'modal_closed' : 'modal_cancelled';
     window.ActionConfirmation.analytics.track(eventName, {
         modal_id: modalId,
         action_type: callbacks ? callbacks.actionType : 'unknown'
     });

     // Limpar callbacks apenas em cancelamento
     if (reason === 'cancel') {
         delete window.ActionConfirmation.pendingCallbacks[modalId];
     }
}

function confirmAction(modalId) {
    const startTime = performance.now();
    const callbacks = window.ActionConfirmation.pendingCallbacks[modalId];
    
    if (callbacks && callbacks.onConfirm) {
        // Analytics
        window.ActionConfirmation.analytics.track('action_confirmed', {
            modal_id: modalId,
            action_type: callbacks.actionType,
            job_title: callbacks.jobTitle,
            company_name: callbacks.companyName
        });

        // Fechar modal sem acionar cancelamento
        closeActionModal(modalId, 'confirm');
        
        // Executar callback com delay para animação
        setTimeout(() => {
            callbacks.onConfirm();
            
            // Track performance
            window.ActionConfirmation.analytics.trackPerformance('action_confirm', startTime);

            // Limpar callbacks após confirmação
            delete window.ActionConfirmation.pendingCallbacks[modalId];
        }, 300);
    }
}

// Função para configurar focus trap
 function setupFocusTrap(modal) {
     const focusableElements = modal.querySelectorAll(
         'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
     );
     const firstElement = focusableElements[0];
     const lastElement = focusableElements[focusableElements.length - 1];

     modal.addEventListener('keydown', function(e) {
         if (e.key === 'Tab') {
             if (e.shiftKey) {
                 // Shift + Tab
                 if (document.activeElement === firstElement) {
                     e.preventDefault();
                     lastElement.focus();
                 }
             } else {
                 // Tab
                 if (document.activeElement === lastElement) {
                     e.preventDefault();
                     firstElement.focus();
                 }
             }
         }
     });
 }

 // Gerenciamento de teclado global (acessibilidade)
 document.addEventListener('keydown', function(e) {
     if (e.key === 'Escape') {
         // Encontrar modal aberto
         const openModal = document.querySelector('[id$="Modal"]:not(.hidden)');
         if (openModal) {
             closeActionModal(openModal.id);
         }
     }
 });

// Compatibilidade com implementação anterior
function showApplicationModal(message, form) {
    showActionModal('{{ $modalId }}', {
        message: message,
        actionType: 'candidatura',
        onConfirm: function() {
            if (form) form.submit();
        }
    });
}

function closeApplicationModal() {
    closeActionModal('{{ $modalId }}');
}

function confirmApplication() {
    confirmAction('{{ $modalId }}');
}
</script>
<script>
// Fallback global de notificação caso não esteja definido em outras páginas
if (typeof window.showNotification !== 'function') {
    window.showNotification = function(message, type = 'info') {
        try {
            // Tenta usar um toast simples se existir
            if (window.toastr && typeof window.toastr[type] === 'function') {
                window.toastr[type](message);
                return;
            }
        } catch (e) {
            // ignora
        }

        // Fallback para console e alert
        console.log(`[${type}]`, message);
        // Evita interromper UX com alert em tudo; usa alert apenas para erros
        if (type === 'error') {
            alert(message);
        }
    }
}
</script>