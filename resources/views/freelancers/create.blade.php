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
                    <form method="POST" action="{{ route('freelancers.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="bio" :value="__('Bio')" />
                                    <textarea id="bio" name="bio" class="form-control" rows="4" placeholder="Conte um pouco sobre você e sua experiência...">{{ old('bio') }}</textarea>
                                    <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="portfolio_url" :value="__('URL do Portfólio')" />
                                    <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="form-control" :value="old('portfolio_url')" placeholder="https://meuportfolio.com" />
                                    <x-input-error :messages="$errors->get('portfolio_url')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="phone" :value="__('Telefone')" />
                                    <x-text-input id="phone" name="phone" type="text" class="form-control" :value="old('phone')" placeholder="(11) 99999-9999" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="location" :value="__('Localização')" />
                                    <x-text-input id="location" name="location" type="text" class="form-control" :value="old('location')" placeholder="São Paulo, SP" />
                                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="hourly_rate" :value="__('Valor por Hora (R$)')" />
                                    <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="0" class="form-control" :value="old('hourly_rate')" placeholder="50.00" />
                                    <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="availability" :value="__('Disponibilidade')" />
                                    <textarea id="availability" name="availability" class="form-control" rows="3" placeholder="Ex: Disponível para projetos de 20h/semana">{{ old('availability') }}</textarea>
                                    <x-input-error :messages="$errors->get('availability')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="cv" :value="__('Currículo (PDF, DOC, DOCX)')" />
                                    <input id="cv" name="cv" type="file" class="form-control" accept=".pdf,.doc,.docx" />
                                    <div class="form-text">Máximo 2MB. Formatos aceitos: PDF, DOC, DOCX</div>
                                    <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                {{ __('Cancelar') }}
                            </a>

                            <x-primary-button class="btn btn-primary">
                                {{ __('Criar Perfil') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>