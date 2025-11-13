@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Cabeçalho com visual Trampix --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="rounded-3 p-4 d-flex align-items-center justify-content-between text-white bg-gradient-to-r from-purple-600 to-indigo-600">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3" style="width:64px;height:64px;">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                    <div>
                        <h2 class="h4 mb-1 fw-bold">
                            @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                                Meu Perfil de Freelancer
                            @else
                                Perfil de {{ $freelancer->user->name }}
                            @endif
                        </h2>
                        <span class="badge bg-light text-dark">Freelancer</span>
                    </div>
                </div>
                <div>
                    @auth
                        @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                            <div class="d-inline-flex align-items-center gap-2">
                                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light">
                                    <i class="fas fa-user-cog me-2"></i> Gerenciar Conta
                                </a>
                                @if(auth()->user()->company)
                                    {{-- Se o usuário também tiver perfil de empresa, oferecer acesso direto à edição da empresa --}}
                                    <a href="{{ route('companies.edit', auth()->user()->company) }}" class="btn btn-sm btn-trampix-primary">
                                        🏢 Editar Perfil de Empresa
                                    </a>
                                @else
                                    {{-- Caso o usuário só tenha perfil de freelancer, abrir criação na própria tela de edição de perfil --}}
                                    <a href="{{ route('profile.edit', ['openCompanyCreate' => 1]) }}" class="btn btn-sm btn-trampix-primary">
                                        🏢 Criar Perfil de Empresa
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
    {{-- Cabeçalho --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 mb-0">
                @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                    Meu Perfil de Freelancer
                @else
                    Perfil de {{ $freelancer->user->name }}
                @endif
            </h2>
        </div>
    </div>

    {{-- Card do Perfil --}}
    <div class="card border-0 rounded-3 shadow-lg">
        <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($freelancer->profile_photo)
                                <img src="{{ asset('storage/' . $freelancer->profile_photo) }}" 
                                     alt="Foto de {{ $freelancer->user->name }}" 
                                     class="img-thumbnail" 
                                     style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;"
                                     onclick="openImageModal(this.src)">
                                <div class="mt-2">
                                    <small class="text-muted fst-italic">Clique para ver em tela inteira</small>
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 200px; height: 200px; border-radius: 8px;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            @endif
                            
                        </div>
                        <div class="col-md-9">
                            <h3>{{ $freelancer->user->name }}</h3>
                            
                            @php
                                $availabilityMap = [
                                    'full_time' => 'Tempo Integral',
                                    'part_time' => 'Meio Período',
                                    'project_based' => 'Por Projeto',
                                    'hourly' => 'Por Hora',
                                    'weekends' => 'Fins de Semana',
                                ];
                                $availabilityLabel = $freelancer->availability ? ($availabilityMap[$freelancer->availability] ?? ucfirst(str_replace('_',' ', $freelancer->availability))) : null;
                            @endphp

                            @if($freelancer->bio)
                                <div class="mb-3">
                                    <strong>Bio:</strong>
                                    <p class="mb-0">{{ $freelancer->bio }}</p>
                                </div>
                            @endif
                            @if($freelancer->location)
                                <div class="mb-3">
                                    <strong>Localização:</strong> {{ $freelancer->location }}
                                </div>
                            @endif
                            @if($freelancer->hourly_rate)
                                <div class="mb-3">
                                    <strong>Valor por Hora:</strong> R$ {{ number_format($freelancer->hourly_rate, 2, ',', '.') }}
                                </div>
                            @endif
                            @if($availabilityLabel)
                                <div class="mb-3">
                                    <strong>Disponibilidade:</strong> {{ $availabilityLabel }}
                                </div>
                            @endif
                            @if($freelancer->linkedin_url)
                                <div class="mb-3">
                                    <strong>LinkedIn:</strong>
                                    <a href="{{ $freelancer->linkedin_url }}" target="_blank" class="text-primary">Ver Perfil</a>
                                </div>
                            @endif
                            @if($freelancer->whatsapp)
                                @php
                                    $waDigits = preg_replace('/\D+/', '', $freelancer->whatsapp);
                                    $waLink = 'https://wa.me/' . (\Illuminate\Support\Str::startsWith($waDigits, '55') ? $waDigits : ('55' . $waDigits));
                                @endphp
                                <div class="mb-3 d-flex align-items-center gap-2">
                                    <strong>WhatsApp:</strong>
                                    <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-success">
                                        <i class="fab fa-whatsapp me-2"></i> Conversar no WhatsApp
                                    </a>
                                </div>
                            @endif

                            

                            

                            

                            @if($freelancer->linkedin_url)
                                <div class="mb-3">
                                    <strong>LinkedIn:</strong> 
                                    <a href="{{ $freelancer->linkedin_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        Ver Perfil
                                    </a>
                                </div>
                            @endif

                            @if($freelancer->cv_url)
                                <div class="mb-3">
                                    <strong>Currículo:</strong>
                                    <a href="{{ route('freelancers.download-cv', $freelancer) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-2"></i> Baixar CV
                                    </a>
                                </div>
                            @endif

                            {{-- Áreas de Atuação (Categorias) --}}
                            @if($freelancer->serviceCategories && $freelancer->serviceCategories->count())
                                <div class="mb-2">
                                    <strong>Áreas de Atuação:</strong>
                                </div>
                                <div class="mb-3 d-flex flex-wrap gap-2">
                                    @foreach($freelancer->serviceCategories as $cat)
                                        <span class="badge bg-info text-dark">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            {{-- Botões de ação apenas para o próprio freelancer --}}
                            @if(auth()->user()->freelancer && auth()->user()->freelancer->id === $freelancer->id)
                                <div class="d-grid gap-2 text-center">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-trampix-primary w-100">
                                        <i class="fas fa-user-cog me-2"></i> Gerenciar Conta
                                    </a>
                                    
                                    <a href="{{ route('applications.index') }}" class="btn btn-trampix-primary w-100">
                                        <i class="fas fa-briefcase me-2"></i> Minhas Candidaturas
                                    </a>
                                    
                                    <a href="{{ route('vagas.index') }}" class="btn btn-trampix-primary w-100">
                                        <i class="fas fa-search me-2"></i> Buscar Vagas
                                    </a>
                                </div>
                            @else
                                {{-- Botões para empresas visualizando o perfil --}}
                                <div class="d-grid gap-2">
                                    @if($freelancer->cv_url)
                                        <a href="{{ route('freelancers.download-cv', $freelancer) }}" class="btn btn-trampix-company">
                                            <i class="fas fa-download me-2"></i> Baixar CV
                                        </a>
                                    @endif
                                    
                                    <a href="mailto:{{ $freelancer->user->email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-envelope me-2"></i> Entrar em Contato
                                    </a>
                                    @if($freelancer->whatsapp)
                                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                                            <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                        </a>
                                    @endif
                                    
                                    <a href="{{ route('dashboard') }}" class="btn btn-trampix-company text-start">
                                        <i class="fas fa-arrow-left me-2"></i> Voltar ao Dashboard
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal simples para visualizar imagem em tamanho completo --}}
    <div id="imageModal" class="image-modal" onclick="closeImageModal()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:1050; align-items:center; justify-content:center;">
        <span class="image-modal-close" onclick="closeImageModal()" style="position:absolute; top:20px; right:24px; color:#fff; font-size:28px; cursor:pointer;">&times;</span>
        <img src="" alt="Imagem" style="max-width:90vw; max-height:90vh; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
    </div>
    <script>
        function openImageModal(src) {
            var modal = document.getElementById('imageModal');
            var img = modal.querySelector('img');
            img.src = src;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        function closeImageModal() {
            var modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    </script>
</div>
@endsection
