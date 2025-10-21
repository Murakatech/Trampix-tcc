@php
    use Illuminate\Support\Facades\Gate;
@endphp

@extends('layouts.app')

@section('header')
<div class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        
        @php
            $activeProfile = null;
            $profilePhoto = null;
            
            if (auth()->user()->freelancer) {
                $activeProfile = auth()->user()->freelancer;
                $profilePhoto = $activeProfile->profile_photo;
            } elseif (auth()->user()->company) {
                $activeProfile = auth()->user()->company;
                $profilePhoto = $activeProfile->profile_photo;
            }
        @endphp
        
        <!-- Miniatura da foto do perfil profissional + nome com menu contextual -->
        <div class="flex flex-col items-center space-y-2 relative" 
             x-data="{ 
                 menuOpen: false,
                 toggleMenu() {
                     this.menuOpen = !this.menuOpen;
                 },
                 closeMenu() {
                     this.menuOpen = false;
                 }
             }"
             x-on:keydown.escape.window="closeMenu()"
             x-on:click.outside="closeMenu()">
            
            <!-- Foto de perfil clicável -->
            <div @click="toggleMenu()" class="cursor-pointer">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                    @if($profilePhoto)
                        <img src="{{ asset('storage/' . $profilePhoto) }}" 
                             alt="Foto de Perfil" 
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-user text-gray-400 text-xl"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Nome clicável -->
            <p @click="toggleMenu()" class="text-sm font-medium text-gray-700 text-center cursor-pointer hover:text-purple-600 transition-colors duration-200">
                {{ Auth::user()->name }}
            </p>

            <!-- Menu contextual -->
            <div x-show="menuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="absolute top-full right-0 mt-2 w-64 bg-white rounded-xl shadow-lg p-3 z-50"
                 style="display: none;">
                
                <!-- Cabeçalho do menu com avatar e nome -->
                <div class="flex items-center space-x-3 p-3 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200">
                        @if($profilePhoto)
                            <img src="{{ asset('storage/' . $profilePhoto) }}" 
                                 alt="Foto de Perfil" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- Link para perfil profissional -->
                <div class="py-2">
                    <a href="{{ route('profiles.show', auth()->user()) }}" 
                       class="flex items-center w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition-colors duration-200 group">
                        <i class="fas fa-user-circle mr-3 text-gray-400 group-hover:text-purple-500"></i>
                        Ver Perfil Profissional
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="space-y-10">

        {{-- Seção ADMIN --}}
        @if(Gate::allows('isAdmin'))
            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-cog text-purple-500 mr-2"></i> Área Administrativa
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <a href="{{ route('admin.freelancers') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-users text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Freelancers</p>
                                <p class="text-sm text-gray-500">Gerenciar freelancers</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.companies') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-building text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Empresas</p>
                                <p class="text-sm text-gray-500">Gerenciar empresas</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.applications') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-blue-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Candidaturas</p>
                                <p class="text-sm text-gray-500">Ver todas candidaturas</p>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        {{-- Seção EMPRESA --}}
        @if(Gate::allows('isCompany'))
            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-briefcase text-purple-500 mr-2"></i> Área da Empresa
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="{{ route('vagas.create') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-plus text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Nova Vaga</p>
                                <p class="text-sm text-gray-500">Criar nova vaga</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('home') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-list text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Minhas Vagas</p>
                                <p class="text-sm text-gray-500">Gerenciar vagas</p>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        {{-- Seção FREELANCER --}}
        @if(Gate::allows('isFreelancer'))
            <section>
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                    <i class="fas fa-user text-purple-500 mr-2"></i> Área do Freelancer
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <a href="{{ route('applications.index') }}" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list text-purple-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Minhas Candidaturas</p>
                                <p class="text-sm text-gray-500">Ver candidaturas</p>
                            </div>
                        </div>
                    </a>

                    <a href="http://trampix.test/vagas/" class="trampix-card hover:scale-[1.02] transition">
                        <div class="flex items-center">
                            <i class="fas fa-search text-green-500 text-2xl mr-3"></i>
                            <div>
                                <p class="font-bold text-gray-900">Buscar Vagas</p>
                                <p class="text-sm text-gray-500">Encontrar oportunidades</p>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
        @endif

        {{-- Ferramentas de Desenvolvimento --}}
        <section class="pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <small class="text-gray-500">Ferramentas de Desenvolvimento</small>
                <a href="{{ route('styleguide') }}" class="btn-trampix-secondary text-sm">
                    <i class="fas fa-palette mr-1"></i> Ver Styleguide
                </a>
            </div>
        </section>

    </div>
</div>
@endsection
