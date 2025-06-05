<div class="modal fade" id="editGroupeModal" tabindex="-1" aria-labelledby="editGroupeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupeModalLabel">Modifier le Groupe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editGroupeForm">
                @csrf
                @method('PUT') {{-- Important pour la mise à jour --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_code" class="form-label">Code du Groupe (non modifiable)</label>
                        <input type="text" class="form-control" id="edit_code" name="code" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nom" class="form-label">Nom du Groupe</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="edit_nom" name="nom" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="edit_description" name="description" rows="3"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_jour" class="form-label">Jour</label>
                        <input type="date" class="form-control @error('jour') is-invalid @enderror" id="edit_jour" name="jour" required>
                        @error('jour')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_heure_debut" class="form-label">Heure de Début</label>
                        <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="edit_heure_debut" name="heure_debut" required>
                        @error('heure_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_heure_fin" class="form-label">Heure de Fin</label>
                        <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="edit_heure_fin" name="heure_fin" required>
                        @error('heure_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>