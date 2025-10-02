<div>
    <p class="text-muted mb-3">
        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente excluídos. 
        Antes de excluir sua conta, baixe quaisquer dados ou informações que deseja manter.
    </p>

    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletion">
        Excluir Conta
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmUserDeletionLabel">Tem certeza que deseja excluir sua conta?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente excluídos. 
                            Digite sua senha para confirmar que deseja excluir permanentemente sua conta.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="visually-hidden">Senha</label>
                            <input id="password" name="password" type="password" 
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                   placeholder="Senha">
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir Conta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
