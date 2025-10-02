<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Foto de Perfil</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                @if($profilePhoto)
                    <img src="{{ asset('storage/' . $profilePhoto) }}" 
                         alt="Foto de Perfil" 
                         class="img-thumbnail mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                         style="width: 150px; height: 150px; border-radius: 8px;">
                        <i class="fas fa-user fa-3x text-muted"></i>
                    </div>
                @endif
                
                @if($profilePhoto)
                    <form action="{{ route('profile.photo.delete') }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="profile_type" value="{{ $profileType }}">
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                onclick="return confirm('Tem certeza que deseja remover a foto?')">
                            <i class="fas fa-trash"></i> Remover
                        </button>
                    </form>
                @endif
            </div>
            
            <div class="col-md-8">
                <form action="{{ route('profile.photo.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="profile_type" value="{{ $profileType }}">
                    
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Escolher Nova Foto</label>
                        <input type="file" 
                               class="form-control @error('profile_photo') is-invalid @enderror" 
                               id="profile_photo" 
                               name="profile_photo" 
                               accept="image/*">
                        @error('profile_photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> 
                        {{ $profilePhoto ? 'Atualizar Foto' : 'Enviar Foto' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>