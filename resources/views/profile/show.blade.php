@extends('layouts.dashboard')

@section('header')
<h1 class="text-2xl font-bold text-gray-900">Perfil do Usuário</h1>
@endsection

@section('content')
<div class="container py-4">
    <style>
        .company-gradient { background: linear-gradient(to right, #1ca751, var(--trampix-green)); }
        .freelancer-gradient { background-image: linear-gradient(to right, #7c3aed, #4f46e5); }
    </style>
    @php
        // Determinar o perfil ativo baseado na sessão ou no que está disponível
        $activeRole = null;
        $activeProfile = null;
        $displayName = $user->name;
        // Garantir que tenhamos as relações disponíveis
        $company = $company ?? ($user->company ?? null);
        $freelancer = $freelancer ?? ($user->freelancer ?? null);
        
        if (auth()->check() && auth()->id() === $user->id) {
            // Para o próprio usuário, usar a sessão active_role
            $activeRole = session('active_role');
        } else {
            // Para visualização externa, mostrar o primeiro perfil disponível
            if ($company) {
                $activeRole = 'company';
            } elseif ($freelancer) {
                $activeRole = 'freelancer';
            }
        }
        // Permitir troca de visualização via querystring, independente de login
        $requestedRole = request('role');
        if (in_array($requestedRole, ['company', 'freelancer'])) {
            if ($requestedRole === 'company' && $company) {
                $activeRole = 'company';
            } elseif ($requestedRole === 'freelancer' && $freelancer) {
                $activeRole = 'freelancer';
            }
        }
        
        // Definir perfil ativo e nome de exibição
        if ($activeRole === 'company' && $company) {
            $activeProfile = $company;
            $displayName = $company->display_name ?? $company->company_name ?? $user->name;
        } elseif ($activeRole === 'freelancer' && $freelancer) {
            $activeProfile = $freelancer;
            $displayName = $freelancer->display_name ?? $user->name;
        }
    @endphp

    {{-- Cabeçalho com visual Trampix --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="rounded-3 p-4 d-flex align-items-center justify-content-between text-white {{ $activeRole === 'company' ? 'company-gradient' : 'freelancer-gradient' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3" style="width:64px;height:64px;">
                        @if($activeRole === 'company')
                            <i class="fas fa-building fa-lg"></i>
                        @else
                            <i class="fas fa-user fa-lg"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="h4 mb-1 fw-bold">{{ $displayName }}</h2>
                        @if($activeRole === 'freelancer')
                            <span class="badge bg-light text-dark">Freelancer</span>
                        @elseif($activeRole === 'company')
                            <span class="badge bg-light text-dark">Empresa</span>
                        @endif
                        @php
                            // Selecionar a média e contagem conforme o perfil ativo
                            $visibleRatingAvg = null;
                            $visibleRatingCount = 0;
                            if ($activeRole === 'company') {
                                $visibleRatingAvg = $companyPublicRatingAvg ?? null;
                                $visibleRatingCount = $companyPublicRatingCount ?? 0;
                            } elseif ($activeRole === 'freelancer') {
                                $visibleRatingAvg = $freelancerPublicRatingAvg ?? null;
                                $visibleRatingCount = $freelancerPublicRatingCount ?? 0;
                            }

                            $fullStars = $visibleRatingAvg ? floor($visibleRatingAvg) : 0;
                            $hasHalfStar = $visibleRatingAvg ? (($visibleRatingAvg - $fullStars) >= 0.5) : false;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        @endphp
                        <div class="mt-2">
                            @if($visibleRatingCount > 0 && $visibleRatingAvg)
                                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 px-3 py-2" style="backdrop-filter:saturate(160%) blur(2px);">
                                    <div class="d-flex align-items-center text-warning">
                                        @for($i = 0; $i < $fullStars; $i++)
                                            <i class="fas fa-star"></i>
                                        @endfor
                                        @if($hasHalfStar)
                                            <i class="fas fa-star-half-alt"></i>
                                        @endif
                                        @for($i = 0; $i < $emptyStars; $i++)
                                            <i class="far fa-star"></i>
                                        @endfor
                                    </div>
                                    <div class="fw-bold">
                                        {{ number_format($visibleRatingAvg, 1, ',', '.') }} / 5
                                    </div>
                                    <div class="small text-white-50">({{ $visibleRatingCount }} avaliação{{ $visibleRatingCount > 1 ? 'es' : '' }})</div>
                                </div>
                            @else
                                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 px-3 py-2" style="backdrop-filter:saturate(160%) blur(2px);">
                                    <div class="text-white-75">
                                        <i class="far fa-smile me-1"></i> Ainda não há avaliações
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Botões de troca de visualização do perfil (como antes, via querystring) -->
                    <div class="d-inline-flex align-items-center gap-2 ms-2">
                        @if($freelancer)
                            <a href="{{ route('profiles.show', ['user' => $user->id, 'role' => 'freelancer']) }}" class="btn btn-sm btn-trampix-primary">
                                🧑‍💻 Freelancer
                            </a>
                        @endif
                        @if($company)
                            <a href="{{ route('profiles.show', ['user' => $user->id, 'role' => 'company']) }}" class="btn btn-sm btn-trampix-company">
                                🏢 Empresa
                            </a>
                        @endif
                        {{-- Botão para criar perfil inverso diretamente nesta tela (somente para o próprio usuário) --}}
                        @auth
                            @if(auth()->id() === $user->id)
                                @if(!$company)
                                    <button type="button" class="btn btn-sm btn-trampix-company" onclick="openModal('createCompanyModal')">
                                        ➕ Criar Perfil de Empresa
                                    </button>
                                @endif
                                @if(!$freelancer)
                                    <button type="button" class="btn btn-sm btn-trampix-primary" onclick="openModal('createFreelancerModal')">
                                        ➕ Criar Perfil Freelancer
                                    </button>
                                @endif
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card de Perfil unificado --}}
    <div class="card border-0 rounded-3 shadow-lg">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    @php
                        $photo = $activeProfile->profile_photo ?? null;
                    @endphp
                    @if($photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Foto/Logo" class="img-thumbnail" style="width: 200px; height: 200px; object-fit: cover; cursor: pointer;" onclick="openImageModal(this.src)">
                        <div class="mt-2">
                            <small class="text-muted fst-italic">Clique para ver em tela inteira</small>
                        </div>
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; border-radius: 8px;">
                            @if($activeRole === 'company')
                                <i class="fas fa-building fa-4x text-muted"></i>
                            @else
                                <i class="fas fa-user fa-4x text-muted"></i>
                            @endif
                        </div>
                    @endif
                    
                </div>

                <div class="col-md-9">
                    {{-- Informações comuns do usuário --}}
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <span>{{ $user->email }}</span>
                    </div>

                    {{-- Informações específicas do perfil ativo --}}
                    @if($activeRole === 'freelancer' && $activeProfile)
                        @php
                            $waDigits = $activeProfile->whatsapp ? preg_replace('/\D+/', '', $activeProfile->whatsapp) : null;
                            $waLink = $waDigits ? ('https://wa.me/' . (\Illuminate\Support\Str::startsWith($waDigits, '55') ? $waDigits : ('55' . $waDigits))) : null;
                        @endphp
                        @if($activeProfile->bio)
                            <div class="mb-3">
                                <strong>Bio:</strong>
                                <p class="mb-0">{{ $activeProfile->bio }}</p>
                            </div>
                        @endif

                        @if($activeProfile->location)
                            <div class="mb-2">
                                <strong>Localização:</strong> {{ $activeProfile->location }}
                            </div>
                        @endif

                        @if($activeProfile->hourly_rate)
                            <div class="mb-2">
                                <strong>Valor por Hora:</strong> R$ {{ number_format($activeProfile->hourly_rate, 2, ',', '.') }}
                            </div>
                        @endif

                        @if($activeProfile->availability)
                            @php
                                $availabilityMap = [
                                    'full_time' => 'Tempo Integral',
                                    'part_time' => 'Meio Período',
                                    'project_based' => 'Por Projeto',
                                    'hourly' => 'Por Hora',
                                    'weekends' => 'Fins de Semana',
                                ];
                                $availabilityLabel = $availabilityMap[$activeProfile->availability] ?? ucfirst(str_replace('_',' ',$activeProfile->availability));
                            @endphp
                            <div class="mb-2">
                                <strong>Disponibilidade:</strong> {{ $availabilityLabel }}
                            </div>
                        @endif

                        @if($activeProfile->linkedin_url)
                            <div class="mb-3">
                                <strong>LinkedIn:</strong>
                                <a href="{{ $activeProfile->linkedin_url }}" target="_blank" class="text-primary">Ver Perfil</a>
                            </div>
                        @endif

                        @if($waLink)
                            <div class="mb-3">
                                <strong>WhatsApp:</strong>
                                <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                    <i class="fab fa-whatsapp me-2"></i> Conversar no WhatsApp
                                </a>
                            </div>
                        @endif

                        

                        @if($activeProfile->cv_url)
                            <div class="mb-3">
                                <strong>Currículo:</strong>
                                <a href="{{ route('freelancers.download-cv', $activeProfile) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-2"></i> Baixar CV
                                </a>
                            </div>
                        @endif

                        {{-- Segmentos do Freelancer --}}
                        @if(method_exists($activeProfile, 'segments') && $activeProfile->segments && $activeProfile->segments->count())
                            <div class="mb-2">
                                <strong>Segmentos:</strong>
                            </div>
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                @foreach($activeProfile->segments as $seg)
                                    <span class="badge bg-info text-dark">{{ $seg->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    @elseif($activeRole === 'company' && $activeProfile)
                        <div class="row">
                            @if($activeProfile->cnpj)
                                <div class="col-md-6 mb-2">
                                    <strong>CNPJ:</strong> {{ $activeProfile->cnpj }}
                                </div>
                            @endif
                            @if($activeProfile->sector)
                                <div class="col-md-6 mb-2">
                                    <strong>Setor:</strong> {{ $activeProfile->sector }}
                                </div>
                            @endif
                            @if($activeProfile->location)
                                <div class="col-md-6 mb-2">
                                    <strong>Localização:</strong> {{ $activeProfile->location }}
                                </div>
                            @endif
                            @if($activeProfile->phone)
                                <div class="col-md-6 mb-2">
                                    <strong>Telefone:</strong> {{ $activeProfile->phone }}
                                </div>
                            @endif
                            @if($activeProfile->employees_count)
                                <div class="col-md-6 mb-2">
                                    <strong>Funcionários:</strong> {{ $activeProfile->employees_count }}
                                </div>
                            @endif
                            @if($activeProfile->founded_year)
                                <div class="col-md-6 mb-2">
                                    <strong>Fundada em:</strong> {{ $activeProfile->founded_year }}
                                </div>
                            @endif
                        </div>

                        @if($activeProfile->description)
                            <div class="mb-3">
                                <strong>Sobre a empresa:</strong>
                                <p class="mb-0">{{ $activeProfile->description }}</p>
                            </div>
                        @endif

                        @if($activeProfile->website)
                            <div class="mb-3">
                                <strong>Website:</strong> 
                                <a href="{{ $activeProfile->website }}" target="_blank" class="text-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ $activeProfile->website }}
                                </a>
                            </div>
                        @endif
                        @if($activeProfile->linkedin_url)
                            <div class="mb-3">
                                <strong>LinkedIn:</strong>
                                <a href="{{ $activeProfile->linkedin_url }}" target="_blank" class="text-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ $activeProfile->linkedin_url }}
                                </a>
                            </div>
                        @endif
                        {{-- Segmentos da Empresa --}}
                        @if(method_exists($activeProfile, 'segments') && $activeProfile->segments && $activeProfile->segments->count())
                            <div class="mb-2">
                                <strong>Segmentos:</strong>
                            </div>
                            <div class="mb-3 d-flex flex-wrap gap-2">
                                @foreach($activeProfile->segments as $seg)
                                    <span class="badge bg-info text-dark">{{ $seg->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Ações contextuais --}}
                <div class="col-12 mt-3">
                    <div class="row align-items-center mt-2">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <a href="{{ route('dashboard') }}" class="{{ $activeRole === 'company' ? 'btn btn-trampix-company' : 'btn btn-trampix-primary' }}">
                                <i class="fas fa-arrow-left me-2"></i> Voltar ao Dashboard
                            </a>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-wrap gap-2 justify-content-start">
                        @auth
                            @if(auth()->id() === $user->id)
                                {{-- Botão único de configurar conta --}}
                            <a href="{{ route('profile.edit') }}" class="{{ $activeRole === 'company' ? 'btn btn-trampix-company' : 'btn btn-trampix-primary' }}">
                                <i class="fas fa-user-cog me-2"></i> Gerenciar Conta
                            </a>
                            
                                
                                {{-- Ações específicas do perfil ativo --}}
                                @if($activeRole === 'freelancer')
                                    <a href="{{ route('applications.index') }}" class="btn btn-trampix-primary">
                                        <i class="fas fa-briefcase me-2"></i> Minhas Candidaturas
                                    </a>
                                    <a href="{{ route('vagas.index') }}" class="btn btn-trampix-primary">
                                        <i class="fas fa-search me-2"></i> Buscar Vagas
                                    </a>
                                @elseif($activeRole === 'company' && $activeProfile)
                                    <a href="{{ route('company.vagas.index') }}" class="btn btn-trampix-company">
                                        <i class="fas fa-briefcase me-2"></i> Minhas Vagas
                                    </a>
                                @endif
                            @else
                                {{-- Visualização externa --}}
                                @if($activeRole === 'freelancer' && $activeProfile && auth()->user() && auth()->user()->isCompany())
                                    @if($activeProfile->cv_url)
                                        <a href="{{ route('freelancers.download-cv', $activeProfile) }}" class="btn btn-trampix-company">
                                            <i class="fas fa-download me-2"></i> Baixar CV
                                        </a>
                                    @endif
                                    <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary">
                                        <i class="fas fa-envelope me-2"></i> Entrar em Contato
                                    </a>
                                    @if(isset($waLink) && $waLink)
                                        <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                                            <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                        </a>
                                    @endif
                                @endif
                            @endif
                        @endauth
                            </div>
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
    @php
        $companyFields = ['display_name','cnpj','email','website','linkedin_url','description','phone','company_size','founded_year','service_categories','segments','activity_area_id'];
        $freelancerFields = ['display_name','bio','linkedin_url','whatsapp','location','hourly_rate','availability','service_categories','segments','cv','activity_area_id'];
        $errorKeys = array_keys($errors->getMessages());
        $hasCompanyErrors = count(array_intersect($errorKeys, $companyFields)) > 0;
        $hasFreelancerErrors = count(array_intersect($errorKeys, $freelancerFields)) > 0;
    @endphp
    <script id="PROFILE_FLAGS" type="application/json">
        {{ json_encode([
            'hasCompany' => (bool)($company ?? ($user->company ?? null)),
            'hasFreelancer' => (bool)($freelancer ?? ($user->freelancer ?? null)),
            'hasCompanyErrors' => (bool)($hasCompanyErrors ?? false),
            'hasFreelancerErrors' => (bool)($hasFreelancerErrors ?? false)
        ]) }}
    </script>
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
    {{-- Modais de criação inline (empresa e freelancer) --}}
    @include('profile.partials.create-company-modal')
    @include('profile.partials.create-freelancer-modal')

    {{-- Helpers JS para abrir/fechar modais e autoabrir via flags de URL --}}
    <script>
        // Evita redefinição se já existir
        if (typeof window.openModal !== 'function') {
            window.openModal = function(modalId) {
                var modal = document.getElementById(modalId);
                if (!modal) return;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                // Foco no primeiro campo interativo para acessibilidade
                var firstField = modal.querySelector('input, textarea, select, button');
                if (firstField) {
                    try { firstField.focus(); } catch (e) {}
                }
            };
        }
        if (typeof window.closeModal !== 'function') {
            window.closeModal = function(modalId) {
                var modal = document.getElementById(modalId);
                if (!modal) return;
                modal.style.display = 'none';
                // Restaurar scroll do body se nenhum outro modal estiver aberto
                var anyOpen = ['createCompanyModal','createFreelancerModal'].some(function(id){
                    var m = document.getElementById(id);
                    return m && m.style.display === 'flex';
                });
                if (!anyOpen) {
                    document.body.style.overflow = '';
                }
            };
        }
        // Fechar com ESC
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') {
                ['createCompanyModal','createFreelancerModal'].forEach(function(id){
                    var m = document.getElementById(id);
                    if (m && m.style.display === 'flex') {
                        closeModal(id);
                    }
                });
            }
        });
        // Autoabrir via flags na URL
        (function(){
            var params = new URLSearchParams(window.location.search);
            var openCompany = params.get('openCompanyCreate');
            var openFreelancer = params.get('openFreelancerCreate');
            var flagsEl = document.getElementById('PROFILE_FLAGS');
            var flags = flagsEl ? JSON.parse(flagsEl.textContent || '{}') : {};
            if (openCompany && openCompany !== '0') {
                var hasCompany = !!flags.hasCompany;
                if (!hasCompany) {
                    openModal('createCompanyModal');
                }
            }
            if (openFreelancer && openFreelancer !== '0') {
                var hasFreelancer = !!flags.hasFreelancer;
                if (!hasFreelancer) {
                    openModal('createFreelancerModal');
                }
            }
            // Autoabrir com base em erros de validação do Laravel
            var hasCompanyErrors = !!flags.hasCompanyErrors;
            var hasFreelancerErrors = !!flags.hasFreelancerErrors;
            if (hasCompanyErrors) {
                openModal('createCompanyModal');
            } else if (hasFreelancerErrors) {
                openModal('createFreelancerModal');
            }
        })();
    </script>
    {{-- Vagas recentes da empresa --}}
    @if($activeRole === 'company' && $activeProfile && $activeProfile->relationLoaded('vacancies') && $activeProfile->vacancies->count() > 0)
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="mb-3"><i class="fas fa-briefcase"></i> Vagas Recentes</h4>
                <div class="row">
                    @foreach($activeProfile->vacancies as $vacancy)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $vacancy->title }}</h5>
                                    <p class="card-text">{{ Str::limit($vacancy->description, 100) }}</p>
                                    <div class="mb-2">
                                        {{-- Tipo de contrato removido: todos os contratos são freelance --}}
                                        @if($vacancy->location_type)
                                            <span class="badge bg-secondary me-1">{{ $vacancy->location_type }}</span>
                                        @endif
                                            @if($vacancy->category)
                                                <span class="badge bg-info">{{ $vacancy->category?->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">Sem categoria</span>
                                            @endif
                                    </div>
                                    @if($vacancy->salary_range)
                                        <p class="text-success mb-2"><strong>{{ $vacancy->salary_range }}</strong></p>
                                    @endif
                                    <small class="text-muted">Publicada em {{ $vacancy->created_at->format('d/m/Y') }}</small>
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('vagas.show', $vacancy) }}" class="btn btn-outline-primary btn-sm">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
