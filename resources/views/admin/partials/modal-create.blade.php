<div class="modal fade" id="ajouter_admin" tabindex="-1" aria-labelledby="ajouterAdminModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <form action="{{ route('admin.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title" id="ajouterAdminModalLabel">Ajouter un Administrateur</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          {{-- Nom --}}
          <div class="mb-3">
            <label for="nom_create" class="form-label">Nom</label>
            <input type="text" name="nom" id="nom_create"
                   class="form-control @error('nom') is-invalid @enderror"
                   value="{{ old('nom') }}" required>
            @error('nom')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Prénom --}}
          <div class="mb-3">
            <label for="prenom_create" class="form-label">Prénom</label>
            <input type="text" name="prenom" id="prenom_create"
                   class="form-control @error('prenom') is-invalid @enderror"
                   value="{{ old('prenom') }}" required>
            @error('prenom')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Password --}}
          <div class="mb-3">
            <label for="password_create" class="form-label">Mot de passe</label>
            <input type="password" name="password" id="password_create"
                   class="form-control @error('password') is-invalid @enderror"
                   required>
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Email --}}
          <div class="mb-3">
            <label for="email_create" class="form-label">Email</label>
            <input type="email" name="email" id="email_create"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Téléphone --}}
          <div class="mb-3">
            <label for="telephone_create" class="form-label">Téléphone</label>
            <input type="tel" name="telephone" id="telephone_create"
                   class="form-control @error('telephone') is-invalid @enderror"
                   value="{{ old('telephone') }}" required>
            @error('telephone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- CIN --}}
          <div class="mb-3">
            <label for="cin_create" class="form-label">CIN</label>
            <input type="text" name="cin" id="cin_create"
                   class="form-control @error('cin') is-invalid @enderror"
                   value="{{ old('cin') }}" required>
            @error('cin')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Adresse --}}
          <div class="mb-3">
            <label for="adresse_create" class="form-label">Adresse</label>
            <input type="text" name="adresse" id="adresse_create"
                   class="form-control @error('adresse') is-invalid @enderror"
                   value="{{ old('adresse') }}" required>
            @error('adresse')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Rôle --}}
          <div class="mb-3">
            <label for="role_id_create" class="form-label">Rôle</label>
            <select name="role_id" id="role_id_create"
                    class="form-select @error('role_id') is-invalid @enderror"
                    required>
              <option value=""> Sélectionner un rôle </option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}"
                  {{ old('role_id') == $role->id ? 'selected' : '' }}>
                  {{ $role->nom }}
                </option>
              @endforeach
            </select>
            @error('role_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Statut --}}
          <div class="mb-3">
            <label for="statut_id_create" class="form-label">Statut</label>
            <select name="statut_id" id="statut_id_create"
                    class="form-select @error('statut_id') is-invalid @enderror"
                    required>
              <option value=""> Sélectionner un statut </option>
              @foreach($statuts as $statut)
                <option value="{{ $statut->id }}"
                  {{ old('statut_id') == $statut->id ? 'selected' : '' }}>
                  {{ $statut->nom }}
                </option>
              @endforeach
            </select>
            @error('statut_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Pays --}}
          <div class="mb-3">
            <label for="pays_id_create" class="form-label">Pays</label>
            <select name="pays_id" id="pays_id_create"
                    class="form-select @error('pays_id') is-invalid @enderror"
                    required>
              <option value="">Sélectionner un pays </option>
              @foreach($pays as $pay)
                <option value="{{ $pay->id }}"
                  {{ old('pays_id') == $pay->id ? 'selected' : '' }}>
                  {{ $pay->nom }}
                </option>
              @endforeach
            </select>
            @error('pays_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Ville --}}
          <div class="mb-3">
            <label for="ville_id_create" class="form-label">Ville</label>
            <select name="ville_id" id="ville_id_create"
                    class="form-select @error('ville_id') is-invalid @enderror"
                    required>
              <option value="">Sélectionner une ville </option>
              @foreach($villes as $ville)
                <option value="{{ $ville->id }}"
                  {{ old('ville_id') == $ville->id ? 'selected' : '' }}>
                  {{ $ville->nom }}
                </option>
              @endforeach
            </select>
            @error('ville_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
