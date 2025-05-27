@php $isEditing = isset($admin); @endphp
<div class="modal fade" id="modal-edit-{{ $admin->id }}" tabindex="-1" aria-labelledby="modalEditLabel-{{ $admin->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form id="form_edit_{{ $admin->id }}" action="{{ route('admin.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditLabel-{{ $admin->id }}">Modifier {{ $admin->nom }} {{ $admin->prenom }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <!-- Nom -->
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="{{ old('nom', $admin->nom) }}" required>
          </div>
          <!-- Prénom -->
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $admin->prenom) }}" required>
          </div>
          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
          </div>
          <!-- Téléphone -->
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="tel" name="telephone" class="form-control" value="{{ old('telephone', $admin->telephone) }}" required>
          </div>
          <!-- CIN -->
          <div class="mb-3">
            <label class="form-label">CIN</label>
            <input type="text" name="cin" class="form-control" value="{{ old('cin', $admin->cin) }}" required>
          </div>
          <!-- Adresse -->
          <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $admin->adresse) }}" required>
          </div>
          <!-- Rôle -->
          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select id="role_id_edit_{{ $admin->id }}" name="role_id" class="form-select" required>
              <option value="">Sélectionner un rôle</option>
              @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ $r->id == old('role_id', $admin->role_id) ? 'selected' : '' }}>{{ $r->nom }}</option>
              @endforeach
            </select>
          </div>
          <!-- Champs stagiaire -->
          <div id="fields_edit_{{ $admin->id }}" style="display:none;">
            <div class="mb-3">
              <label class="form-label">Université</label>
              <input type="text" name="universite" class="form-control" value="{{ old('universite', $admin->universite) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Faculté</label>
              <input type="text" name="faculte" class="form-control" value="{{ old('faculte', $admin->faculte) }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Titre de la formation</label>
              <input type="text" name="titre_formation" class="form-control" value="{{ old('titre_formation', $admin->titre_formation) }}">
            </div>
          </div>
          <!-- Pays -->
          <div class="mb-3">
            <label class="form-label">Pays</label>
            <select id="pays_id_edit_{{ $admin->id }}" name="pays_id" class="form-select" required>
              <option value="">Sélectionner un pays</option>
              @foreach($pays as $p)
                <option value="{{ $p->id }}" {{ $p->id == old('pays_id', $admin->pays_id) ? 'selected' : '' }}>{{ $p->nom }}</option>
              @endforeach
            </select>
          </div>
          <!-- Ville -->
          <div class="mb-3">
            <label class="form-label">Ville</label>
            <select id="ville_id_edit_{{ $admin->id }}" name="ville_id" class="form-select" required disabled>
              <option value="">Sélectionner une ville</option>
            </select>
          </div>
          <!-- Statut -->
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select name="statut_id" class="form-select" required>
              <option value="">Sélectionner un statut</option>
              @foreach($statuts as $s)
                <option value="{{ $s->id }}" {{ $s->id == old('statut_id', $admin->statut_id) ? 'selected' : '' }}>{{ $s->nom }}</option>
              @endforeach
            </select>
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
