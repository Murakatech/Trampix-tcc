{{-- Modal de Criação de Empresa --}}
<div id="createCompanyModal" class="hidden fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-black/50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal('createCompanyModal')">
        
    <div class="trampix-card w-full max-w-md sm:max-w-lg md:max-w-xl p-0 bg-white rounded-lg shadow-xl max-h-[85vh] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="createCompanyTitle">
        
        {{-- Cabeçalho do Modal --}}
        <div class="flex items-center justify-between px-6 py-4 sticky top-0 bg-white border-b">
            <h2 id="createCompanyTitle" class="trampix-h2 text-gray-900">Criar Perfil de Empresa</h2>
            <button onclick="closeModal('createCompanyModal')" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Formulário --}}
            <form method="POST" action="{{ route('companies.store') }}" class="space-y-4 px-6 py-4">
                @csrf
                
                {{-- Nome da Empresa --}}
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Empresa *
                    </label>
                    <input type="text" 
                           id="company_name"
                           name="display_name" 
                           class="trampix-input w-full" 
                           placeholder="Digite o nome da empresa"
                           required>
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descrição --}}
                <div>
                    <label for="company_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição da Empresa
                    </label>
                    <textarea id="company_description"
                              name="description" 
                              rows="4"
                              class="trampix-input w-full resize-none"
                              placeholder="Descreva brevemente a empresa e suas atividades"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Website (opcional) --}}
                <div>
                    <label for="company_website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website
                    </label>
                    <input type="url" 
                           id="company_website"
                           name="website" 
                           class="trampix-input w-full" 
                           placeholder="https://www.exemplo.com">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                

                {{-- E-mail (obrigatório) --}}
                <div>
                    <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-mail *
                    </label>
                    <input type="email"
                           id="company_email"
                           name="email"
                           class="trampix-input w-full"
                           placeholder="contato@empresa.com"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CNPJ (opcional) - posicionado após o E-mail --}}
                <div>
                    <label for="company_cnpj" class="block text-sm font-medium text-gray-700 mb-2">
                        CNPJ
                    </label>
                    <input type="text"
                           id="company_cnpj"
                           name="cnpj"
                           class="trampix-input w-full br-cnpj"
                           placeholder="00.000.000/0000-00"
                           >
                    @error('cnpj')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LinkedIn (opcional) --}}
                <div>
                    <label for="company_linkedin" class="block text-sm font-medium text-gray-700 mb-2">
                        LinkedIn
                    </label>
                    <input type="url"
                           id="company_linkedin"
                           name="linkedin_url"
                           class="trampix-input w-full"
                           placeholder="https://www.linkedin.com/company/seu-perfil">
                    @error('linkedin_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões de Ação --}}
                <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200 sticky bottom-0 bg-white">
                    <button type="button" 
                            onclick="closeModal('createCompanyModal')" 
                            class="btn-trampix-secondary">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="btn-trampix-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Criar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>