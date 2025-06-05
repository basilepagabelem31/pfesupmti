<div class="modal fade" id="editSujetModal" tabindex="-1" aria-labelledby="editSujetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSujetModalLabel">Modifier le Sujet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editSujetForm">
                @csrf
                @method('PUT') {{-- Important pour la mise à jour --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_titre" class="form-label">Titre du Sujet</label>
                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="edit_titre" name="titre" required>
                        @error('titre')
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
                        <label for="edit_promotion_id" class="form-label">Promotion</label>
                        <select class="form-select @error('promotion_id') is-invalid @enderror" id="edit_promotion_id" name="promotion_id" required>
                            <option value="">Sélectionner une promotion</option>
                            @foreach($promotions as $promotion)
                                <option value="{{ $promotion->id }}">{{ $promotion->titre }}</option>
                            @endforeach
                        </select>
                        @error('promotion_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_groupe_id" class="form-label">Groupe</label>
                        <select class="form-select @error('groupe_id') is-invalid @enderror" id="edit_groupe_id" name="groupe_id" required>
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                        @error('groupe_id')
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