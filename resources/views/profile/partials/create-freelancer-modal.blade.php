{{-- Modal de Criação de Freelancer --}}
<div id="createFreelancerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        
        <div class="trampix-card w-full max-w-lg p-6 bg-white rounded-lg shadow-xl">
        
        {{-- Cabeçalho do Modal --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="trampix-h2 text-gray-900">Criar Perfil de Freelancer</h2>
            <button onclick="closeModal('createFreelancerModal')" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Formulário --}}
            <form method="POST" action="{{ route('freelancers.store') }}" class="space-y-4">
                @csrf
                
                {{-- Biografia --}}
                <div>
                    <label for="freelancer_bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Biografia Profissional *
                    </label>
                    <textarea id="freelancer_bio"
                              name="bio" 
                              rows="4"
                              class="trampix-input w-full resize-none"
                              placeholder="Conte um pouco sobre sua experiência e especialidades"
                              required></textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- URL do Portfólio --}}
                <div>
                    <label for="freelancer_portfolio" class="block text-sm font-medium text-gray-700 mb-2">
                        URL do Portfólio
                    </label>
                    <input type="url" 
                           id="freelancer_portfolio"
                           name="portfolio_url" 
                           class="trampix-input w-full" 
                           placeholder="https://www.meuportfolio.com">
                    @error('portfolio_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Telefone --}}
                <div>
                    <label for="freelancer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefone
                    </label>
                    <input type="tel" 
                           id="freelancer_phone"
                           name="phone" 
                           class="trampix-input w-full" 
                           placeholder="(11) 99999-9999">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Valor por Hora --}}
                <div>
                    <label for="freelancer_hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                        Valor por Hora (R$)
                    </label>
                    <input type="number" 
                           id="freelancer_hourly_rate"
                           name="hourly_rate" 
                           class="trampix-input w-full" 
                           placeholder="50.00"
                           step="0.01"
                           min="0">
                    @error('hourly_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Disponibilidade --}}
                <div>
                    <label for="freelancer_availability" class="block text-sm font-medium text-gray-700 mb-2">
                        Disponibilidade
                    </label>
                    <select id="freelancer_availability" 
                            name="availability" 
                            class="trampix-input w-full">
                        <option value="">Selecione sua disponibilidade</option>
                        <option value="full_time">Tempo Integral</option>
                        <option value="part_time">Meio Período</option>
                        <option value="freelance">Projetos Específicos</option>
                        <option value="unavailable">Indisponível</option>
                    </select>
                    @error('availability')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões de Ação --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeModal('createFreelancerModal')" 
                            class="btn-trampix-secondary">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="btn-trampix-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Criar Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>