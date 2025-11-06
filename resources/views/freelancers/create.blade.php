<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Perfil de Freelancer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('freelancers.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-6">
                                <!-- Nome Profissional -->
                                <div>
                                    <x-input-label for="display_name" :value="__('Nome Profissional')" />
                                    <x-text-input id="display_name" name="display_name" type="text" class="trampix-input mt-1 block w-full" :value="old('display_name')" required placeholder="Como você quer ser conhecido profissionalmente" />
                                    <p class="text-sm text-gray-600 mt-1">Este nome será exibido em seu perfil e candidaturas</p>
                                    <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
                                </div>

                                <!-- Biografia Profissional -->
                                <div>
                                    <x-input-label for="bio" :value="__('Biografia Profissional')" />
                                    <textarea id="bio" name="bio" class="trampix-input mt-1 block w-full" rows="4" placeholder="Conte um pouco sobre sua experiência e especialidades">{{ old('bio') }}</textarea>
                                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                                </div>

                                <!-- URL do Portfólio -->
                                <div>
                                    <x-input-label for="portfolio_url" :value="__('URL do Portfólio')" />
                                    <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="trampix-input mt-1 block w-full" :value="old('portfolio_url')" placeholder="https://www.meuportfolio.com" />
                                    <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                                </div>

                                

                                <!-- WhatsApp (Obrigatório) -->
                                <div>
                                    <x-input-label for="whatsapp" :value="__('WhatsApp')" />
                                    <x-text-input id="whatsapp" name="whatsapp" type="text" class="trampix-input mt-1 block w-full" :value="old('whatsapp')" placeholder="Ex: (16) 99999-9999" required data-mask="br-phone" />
                                    
                                    <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
                                </div>

                                <!-- Localização -->
                                <div>
                                    <x-input-label for="location" :value="__('Localização')" />
                                    <x-text-input id="location" name="location" type="text" class="trampix-input mt-1 block w-full" :value="old('location')" placeholder="São Paulo, SP" />
                                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                                </div>

                                <!-- Área de Atuação (ActivityArea) -->
                                <div>
                                    <x-input-label for="activity_area_id" :value="__('Área de Atuação')" />
                                    <select id="activity_area_id" name="activity_area_id" class="trampix-input mt-1 block w-full">
                                        <option value="">Selecione sua área de atuação</option>
                                        @foreach(\App\Models\ActivityArea::where('type', 'freelancer')->orderBy('name')->get() as $area)
                                            <option value="{{ $area->id }}" {{ old('activity_area_id') == $area->id ? 'selected' : '' }}>
                                                {{ $area->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('activity_area_id')" class="mt-2" />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <!-- Valor por Hora -->
                                <div>
                                    <x-input-label for="hourly_rate" :value="__('Valor por Hora (R$)')" />
                                    <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0" class="trampix-input mt-1 block w-full" :value="old('hourly_rate')" placeholder="50.00" />
                                    <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                                </div>

                                <!-- Disponibilidade -->
                                <div>
                                    <x-input-label for="availability" :value="__('Disponibilidade')" />
                                    <select id="availability" name="availability" class="trampix-input mt-1 block w-full">
                                        <option value="">Selecione sua disponibilidade</option>
                                        <option value="full_time" {{ old('availability') == 'full_time' ? 'selected' : '' }}>Tempo integral (40h/semana)</option>
                                        <option value="part_time" {{ old('availability') == 'part_time' ? 'selected' : '' }}>Meio período (20h/semana)</option>
                                        <option value="freelance" {{ old('availability') == 'freelance' ? 'selected' : '' }}>Projetos pontuais</option>
                                        <option value="weekends" {{ old('availability') == 'weekends' ? 'selected' : '' }}>Fins de semana</option>
                                        <option value="flexible" {{ old('availability') == 'flexible' ? 'selected' : '' }}>Horário flexível</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('availability')" class="mt-2" />
                                </div>

                                <!-- Currículo -->
                                <div>
                                    <x-input-label for="cv" :value="__('Currículo (PDF, DOC, DOCX)')" />
                                    <input id="cv" name="cv" type="file" class="trampix-input mt-1 block w-full" accept=".pdf,.doc,.docx" />
                                    <p class="text-sm text-gray-600 mt-1">Máximo 2MB. Formatos aceitos: PDF, DOC, DOCX</p>
                                    <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex justify-between pt-6">
                            <a href="{{ route('dashboard') }}" class="btn-trampix-secondary">
                                {{ __('Cancelar') }}
                            </a>

                            <button type="submit" class="btn-trampix-primary">
                                {{ __('Criar Perfil') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>