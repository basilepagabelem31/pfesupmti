<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form id="form_add" action="{{ route('admin.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalAddLabel">  Ajouter un Administrateur/Superviseur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <!-- Nom -->
          <div class="mb-3">
            <label for="nom_add" class="form-label">Nom</label>
            <input type="text" name="nom" id="nom_add" class="form-control">
            <div class="invalid-feedback" id="error_nom_add"></div>
          </div>
          <!-- Prénom -->
          <div class="mb-3">
            <label for="prenom_add" class="form-label">Prénom</label>
            <input type="text" name="prenom" id="prenom_add" class="form-control">
            <div class="invalid-feedback" id="error_prenom_add"></div>
          </div>
          <!-- Email -->
          <div class="mb-3">
            <label for="email_add" class="form-label">Email</label>
            <input type="email" name="email" id="email_add" class="form-control">
            <div class="invalid-feedback" id="error_email_add"></div>
          </div>
           <!-- Password -->
          <div class="mb-3">
            <label for="password_add" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password_add" class="form-control">
          </div>
          <!-- Téléphone -->
          <div class="mb-3">
            <label for="telephone_add" class="form-label">Téléphone</label>
            <input type="tel" name="telephone" id="telephone_add" class="form-control">
            <div class="invalid-feedback" id="error_telephone_add"></div>
          </div>
          <!-- CIN -->
          <div class="mb-3">
            <label for="cin_add" class="form-label">CIN</label>
            <input type="text" name="cin" id="cin_add" class="form-control">
            <div class="invalid-feedback" id="error_cin_add"></div>
          </div>
          <!-- Adresse -->
          <div class="mb-3">
            <label for="adresse_add" class="form-label">Adresse</label>
            <input type="text" name="adresse" id="adresse_add" class="form-control">
            <div class="invalid-feedback" id="error_adresse_add"></div>
          </div>
          <!-- Rôle -->
          <div class="mb-3">
            <label for="role_id_add" class="form-label">Rôle</label>
            <select name="role_id" id="role_id_add" class="form-select">
              <option value="">Sélectionner un rôle</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->nom }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error_role_id_add"></div>
          </div>
          <!-- Champs stagiaire -->
          <div id="fields_add" style="display:none;">
            <div class="mb-3">
              <label for="universite_add" class="form-label">Université</label>
              <input type="text" name="universite" id="universite_add" class="form-control">
            </div>
            <div class="mb-3">
              <label for="faculte_add" class="form-label">Faculté</label>
              <input type="text" name="faculte" id="faculte_add" class="form-control">
            </div>
            <div class="mb-3">
              <label for="titre_formation_add" class="form-label">Titre de la formation</label>
              <input type="text" name="titre_formation" id="titre_formation_add" class="form-control">
            </div>
          </div>
          <!-- Pays -->
          <div class="mb-3">
            <label for="pays_id_add" class="form-label">Pays</label>
            <select name="pays_id" id="pays_id_add" class="form-select">
              <option value="">Sélectionner un pays</option>
              @foreach($pays as $pay)
                <option value="{{ $pay->id }}">{{ $pay->nom }}</option>
              @endforeach
            </select>
          </div>
          <!-- Ville -->
          <div class="mb-3">
            <label for="ville_id_add" class="form-label">Ville</label>
            <select name="ville_id" id="ville_id_add" class="form-select" disabled>
              <option value="">Sélectionner une ville</option>
            </select>
          </div>
          <!-- Statut -->
          <div class="mb-3">
            <label for="statut_id_add" class="form-label">Statut</label>
            <select name="statut_id" id="statut_id_add" class="form-select">
              <option value="">Sélectionner un statut</option>
              @foreach($statuts as $statut)
                <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
