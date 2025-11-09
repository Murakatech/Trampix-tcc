{{-- Modal de Criação de Freelancer --}}
<div id="createFreelancerModal" class="hidden fixed inset-0 z-50 flex items-start sm:items-center justify-center bg-black/50 p-4 overflow-y-auto" onclick="if(event.target === this) closeModal('createFreelancerModal')">
        
    <div class="trampix-card w-full max-w-md sm:max-w-lg md:max-w-xl p-0 bg-white rounded-lg shadow-xl max-h-[85vh] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="createFreelancerTitle">
        
        {{-- Cabeçalho do Modal --}}
        <div class="flex items-center justify-between px-6 py-4 sticky top-0 bg-white border-b">
            <h2 id="createFreelancerTitle" class="trampix-h2 text-gray-900">Criar Perfil de Freelancer</h2>
            <button onclick="closeModal('createFreelancerModal')" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Formulário --}}
            <form method="POST" action="{{ route('freelancers.store') }}" class="space-y-4 px-6 py-4">
                @csrf
                {{-- Nome Profissional --}}
                <div>
                    <label for="freelancer_display_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Profissional *
                    </label>
                    <input type="text"
                           id="freelancer_display_name"
                           name="display_name"
                           class="trampix-input w-full"
                           placeholder="Como você quer ser conhecido profissionalmente"
                           value="{{ old('display_name') }}"
                           required>
                    <p class="text-sm text-gray-600 mt-1">Este nome será exibido em seu perfil e candidaturas.</p>
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
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

                {{-- LinkedIn (substitui Portfólio) --}}
                <div>
                    <label for="freelancer_linkedin" class="block text-sm font-medium text-gray-700 mb-2">
                        LinkedIn
                    </label>
                    <input type="url" 
                           id="freelancer_linkedin"
                           name="linkedin_url" 
                           class="trampix-input w-full" 
                           placeholder="https://www.linkedin.com/in/seu-perfil">
                    @error('linkedin_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Telefone --}}
                

                {{-- Telefone (Preferêncial Whatsapp) - Obrigatório --}}
                <div>
                    <label for="freelancer_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        Telefone (Preferêncial Whatsapp) *
                    </label>
                    <input type="text" 
                           id="freelancer_whatsapp"
                           name="whatsapp" 
                           class="trampix-input w-full" 
                           placeholder="Ex: (16) 99999-9999" required data-mask="br-phone">
                    @error('whatsapp')
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

                {{-- Segmentos de Atuação (opcional) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Segmentos de Atuação</label>
                    <p class="text-xs text-gray-600 mb-2">Selecione os segmentos econômicos em que você atua. Você pode ajustar isso depois.</p>
                    @php($allSegments = \App\Models\Segment::orderBy('name')->get())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($allSegments as $seg)
                            <label class="flex items-center p-2 border border-gray-200 rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="segments[]" value="{{ $seg->id }}" class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">{{ $seg->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('segments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botões de Ação --}}
                <div class="flex justify-end space-x-3 px-6 py-4 border-t border-gray-200 sticky bottom-0 bg-white">
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