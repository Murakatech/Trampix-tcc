{{-- Upload de Foto de Perfil --}}
<div class="trampix-card">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
            @if($type === 'freelancer')
                Foto de Perfil
            @else
                Logo da Empresa
            @endif
        </h2>
        
        {{-- Exibir foto atual se existir --}}
        @php
            $profile = $type === 'freelancer' ? ($freelancer ?? null) : ($company ?? null);
            $hasPhoto = $profile && $profile->profile_photo;
        @endphp
        
        @if($hasPhoto)
            <div class="mb-4">
                <img src="{{ asset('storage/' . $profile->profile_photo) }}" 
                     alt="{{ $type === 'freelancer' ? 'Foto de perfil' : 'Logo da empresa' }}" 
                     class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
            </div>
        @endif
        
        {{-- Upload de nova foto --}}
        <form method="post" action="{{ route('profile.photo.upload') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="profile_type" value="{{ $type }}">
            
            <div>
                <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $hasPhoto ? 'Alterar' : 'Enviar' }} 
                    {{ $type === 'freelancer' ? 'foto' : 'logo' }}
                </label>
                <input id="profile_photo" name="profile_photo" type="file" 
                       class="trampix-input @error('profile_photo') border-red-500 @enderror" 
                       accept="image/*">
                @error('profile_photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="btn-trampix-secondary">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                {{ $hasPhoto ? 'Alterar' : 'Enviar' }}
            </button>
        </form>

        @if($hasPhoto)
            <form method="post" action="{{ route('profile.photo.delete') }}" class="inline mt-2" id="removePhotoForm">
                @csrf
                @method('delete')
                <input type="hidden" name="profile_type" value="{{ $type }}">
                <button type="button" class="text-red-600 hover:text-red-800 text-sm transition-colors duration-200"
                        onclick="showRemovePhotoConfirmation('{{ $type === 'freelancer' ? 'foto' : 'logo' }}')">
                    Remover {{ $type === 'freelancer' ? 'foto' : 'logo' }}
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Componente de Confirmação --}}
<x-action-confirmation 
    actionType="generic" 
    modalId="removePhotoConfirmationModal" />

@push('scripts')
<script>
    // Função para remover foto/logo
    function showRemovePhotoConfirmation(photoType) {
        showActionModal('removePhotoConfirmationModal', {
            actionType: 'generic',
            message: `🗑️ Tem certeza que deseja remover a ${photoType}?\n\nEsta ação não pode ser desfeita.`,
            onConfirm: () => {
                const form = document.getElementById('removePhotoForm');
                showNotification(`Removendo ${photoType}...`, 'warning');
                form.submit();
            },
            onCancel: () => {
                showNotification('Remoção cancelada.', 'info');
            }
        });
    }
</script>
@endpush