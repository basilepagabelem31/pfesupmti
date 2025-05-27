<div class="modal fade" id="ajouter_admin" tabindex="-1" aria-labelledby="ajouterAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <form id="adminForm" action="{{route('admin.store')}}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="ajouterAdminModalLabel">Ajouter un Administrateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="nom_create" class="form-label">Nom</label>
            <input type="text" name="nom" id="nom_create" class="form-control">
            <div class="invalid-feedback" id="error_nom"></div>
          </div>

          <div class="mb-3">
            <label for="prenom_create" class="form-label">Prénom</label>
            <input type="text" name="prenom" id="prenom_create" class="form-control">
            <div class="invalid-feedback" id="error_prenom"></div>
          </div>

          <div class="mb-3">
            <label for="password_create" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password_create" class="form-control">
            <div class="invalid-feedback" id="error_password"></div>
          </div>

          <div class="mb-3">
            <label for="email_create" class="form-label">Email</label>
            <input type="email" name="email" id="email_create" class="form-control">
            <div class="invalid-feedback" id="error_email"></div>
          </div>

          <div class="mb-3">
            <label for="telephone_create" class="form-label">Téléphone</label>
            <input type="tel" name="telephone" id="telephone_create" class="form-control">
            <div class="invalid-feedback" id="error_telephone"></div>
          </div>

          <div class="mb-3">
            <label for="cin_create" class="form-label">CIN</label>
            <input type="text" name="cin" id="cin_create" class="form-control">
            <div class="invalid-feedback" id="error_cin"></div>
          </div>

          <div class="mb-3">
            <label for="adresse_create" class="form-label">Adresse</label>
            <input type="text" name="adresse" id="adresse_create" class="form-control">
            <div class="invalid-feedback" id="error_adresse"></div>
          </div>

          <div class="mb-3">
            <label for="role_id_create" class="form-label">Rôle</label>
            <select name="role_id" id="role_id_create" class="form-select">
              <option value=""> Sélectionner un rôle </option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->nom }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error_role_id"></div>
          </div>

          <div class="mb-3">
            <label for="statut_id_create" class="form-label">Statut</label>
            <select name="statut_id" id="statut_id_create" class="form-select">
              <option value=""> Sélectionner un statut </option>
              @foreach($statuts as $statut)
                <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error_statut_id"></div>
          </div>

          <div class="mb-3">
            <label for="pays_id_create" class="form-label">Pays</label>
            <select name="pays_id" id="pays_id_create" class="form-select">
              <option value=""> Sélectionner un pays </option>
              @foreach($pays as $pay)
                <option value="{{ $pay->id }}">{{ $pay->nom }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error_pays_id"></div>
          </div>

          <div class="mb-3">
            <label for="ville_id_create" class="form-label">Ville</label>
            <select name="ville_id" id="ville_id_create" class="form-select">
              <option value=""> Sélectionner une ville </option>
              @foreach($villes as $ville)
                <option value="{{ $ville->id }}">{{ $ville->nom }}</option>
              @endforeach
            </select>
            <div class="invalid-feedback" id="error_ville_id"></div>
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
