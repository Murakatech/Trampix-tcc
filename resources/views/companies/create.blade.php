<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Perfil de Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('companies.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="display_name" :value="__('Nome da Empresa')" />
                                    <x-text-input id="display_name" name="display_name" type="text" class="form-control" :value="old('display_name')" required placeholder="Minha Empresa Ltda" />
                                    <div class="form-text">Nome que será exibido publicamente</div>
                                    <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="description" :value="__('Descrição')" />
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Descreva sua empresa, área de atuação, missão...">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="website" :value="__('Website')" />
                                    <x-text-input id="website" name="website" type="url" class="form-control" :value="old('website')" placeholder="https://minhaempresa.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="phone" :value="__('Telefone')" />
                                    <x-text-input id="phone" name="phone" type="text" class="form-control" :value="old('phone')" placeholder="(11) 3333-4444" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="employees_count" :value="__('Número de Funcionários')" />
                                    <x-text-input id="employees_count" name="employees_count" type="number" min="1" class="form-control" :value="old('employees_count')" placeholder="10" />
                                    <x-input-error :messages="$errors->get('employees_count')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="founded_year" :value="__('Ano de Fundação')" />
                                    <x-text-input id="founded_year" name="founded_year" type="number" min="1900" max="{{ date('Y') }}" class="form-control" :value="old('founded_year')" placeholder="2020" />
                                    <x-input-error :messages="$errors->get('founded_year')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Empresa ativa (aceita candidaturas)
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
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