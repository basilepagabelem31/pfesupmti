@php $isEditing = isset($editAdmin); @endphp

<div class="modal fade show" id="editModal" tabindex="-1" aria-modal="true" role="dialog" style="display:block;">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
      <form action="{{  route('admin.update', $editAdmin->id)  }}" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Modifier un Administrateur</h5>
          <a href="{{ route('admin.index') }}" class="btn-close"></a>
        </div>

        <div class="modal-body">

          {{-- Champs préremplis --}}
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control"
                   value="{{ old('nom',$editAdmin->nom) }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control"
                   value="{{ old('prenom',$editAdmin->prenom)  }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email',$editAdmin->email)  }}" required>
          </div>

         
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control"
                   value="{{ old('telephone',$editAdmin->telephone)  }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">CIN</label>
            <input type="text" name="cin" class="form-control"
                   value="{{ old('cin',$editAdmin->cin)  }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control"
                   value="{{ old('adresse',$editAdmin->adresse) }}" required>
          </div>

              <!-- Pays -->
      <div class="mb-3">
        <label class="form-label">Pays</label>
        <select name="pays_id" id="pays_id_edit" class="form-select" required>
          <option value="">Sélectionner un pays </option>
          @foreach($pays as $p)
            <option value="{{ $p->id }}"
              {{ old('pays_id', $isEditing ? $editAdmin->pays_id : '') == $p->id ? 'selected' : '' }}>
              {{ $p->nom }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Ville -->
      <div class="mb-3">
        <label class="form-label">Ville</label>
        <select name="ville_id" id="ville_id_edit" class="form-select" required>
          <option value="">Sélectionner une ville </option>
          @foreach($villes as $v)
            <option value="{{ $v->id }}"
              {{ old('ville_id', $isEditing ? $editAdmin->ville_id : '') == $v->id ? 'selected' : '' }}>
              {{ $v->nom }}
            </option>
          @endforeach
        </select>
      </div>


          <div class="mb-3">
            <label class="form-label">Rôle</label>
            <select name="role_id" class="form-select" required>
                <option value="">Sélectionner un role </option>
              @foreach($roles as $r)
                <option value="{{ $r->id }}"
                  {{ old('role_id', $isEditing ? $editAdmin->role_id : '') == $r->id   ? 'selected' : ''  }}>
                  {{ $r->nom }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select name="statut_id" class="form-select" required>
                <option value="">Sélectionner un statut </option>

              @foreach($statuts as $s)
                <option value="{{ $s->id }}"
                  {{old('statut_id', $isEditing ? $editAdmin->statut_id : '') == $s->id   ? 'selected' : ''  }}>
                  {{ $s->nom }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <a href="{{ route('admin.index') }}" class="btn btn-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal-backdrop fade show"></div>

