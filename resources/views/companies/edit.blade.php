<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Perfil da Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('companies.update', $company) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="name" :value="__('Nome da Empresa')" />
                                    <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $company->name)" required placeholder="Nome da sua empresa" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="description" :value="__('Descrição')" />
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Descreva sua empresa, área de atuação, missão...">{{ old('description', $company->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="website" :value="__('Website')" />
                                    <x-text-input id="website" name="website" type="url" class="form-control" :value="old('website', $company->website)" placeholder="https://www.suaempresa.com" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="phone" :value="__('Telefone')" />
                                    <x-text-input id="phone" name="phone" type="text" class="form-control" :value="old('phone', $company->phone)" placeholder="(11) 3333-4444" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <x-input-label for="employee_count" :value="__('Número de Funcionários')" />
                                    <select id="employee_count" name="employee_count" class="form-control">
                                        <option value="">Selecione o porte da empresa</option>
                                        <option value="1-10" {{ old('employee_count', $company->employee_count) == '1-10' ? 'selected' : '' }}>1-10 funcionários</option>
                                        <option value="11-50" {{ old('employee_count', $company->employee_count) == '11-50' ? 'selected' : '' }}>11-50 funcionários</option>
                                        <option value="51-200" {{ old('employee_count', $company->employee_count) == '51-200' ? 'selected' : '' }}>51-200 funcionários</option>
                                        <option value="201-500" {{ old('employee_count', $company->employee_count) == '201-500' ? 'selected' : '' }}>201-500 funcionários</option>
                                        <option value="500+" {{ old('employee_count', $company->employee_count) == '500+' ? 'selected' : '' }}>Mais de 500 funcionários</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('employee_count')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="founded_year" :value="__('Ano de Fundação')" />
                                    <x-text-input id="founded_year" name="founded_year" type="number" min="1800" max="{{ date('Y') }}" class="form-control" :value="old('founded_year', $company->founded_year)" placeholder="{{ date('Y') }}" />
                                    <x-input-error :messages="$errors->get('founded_year')" class="mt-2" />
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            {{ __('Empresa ativa (aceita candidaturas)') }}
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('companies.show') }}" class="btn btn-secondary">
                                {{ __('Cancelar') }}
                            </a>

                            <x-primary-button class="btn btn-primary">
                                {{ __('Atualizar Perfil') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>