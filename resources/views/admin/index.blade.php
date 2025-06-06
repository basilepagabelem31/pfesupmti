@extends('layout.default')

@section('title', 'Dashboard')

@section('content')
<!-- BOUTON Ajouter -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-add">
  Ajouter un Administrateur/Superviseur
</button>
<form action="{{ route('logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-link">Déconnexion</button>
</form>

@if(Session::has('success'))
<div class="alert alert-success" role="alert">
    {{ Session::get('success') }}
</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>CIN</th>
            <th>Code</th>
            <th>Adresse</th>
            <th>Pays</th>
            <th>Ville</th>
            <th>Rôle</th>
            <th>Université</th>
            <th>Faculte</th>
            <th>Titre de Formation</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($admins as $admin)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $admin->nom }}</td>
            <td>{{ $admin->prenom }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->telephone }}</td>
            <td>{{ $admin->cin }}</td>
            <td>{{ $admin->code }}</td>
            <td>{{ $admin->adresse }}</td>
            <td>{{ $admin->pays?->nom  }}</td>
            <td>{{ $admin->ville?->nom }}</td>
            <td>{{ $admin->role?->nom }}</td>
            <td>{{ $admin->universite ??'Non-defini' }}</td>
            <td>{{ $admin->faculte ?? 'Non-defini' }}</td>
            <td>{{ $admin->titre_formation ?? 'Non-defini' }}</td>
            <td>{{ $admin->statut?->nom  }}</td>
            <td>
                <div class="d-flex gap-2">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $admin->id }}">
                         Edit
                    </button>
                    <form action="{{ route('admin.delete', $admin->id) }}" onclick=" return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')" method="post" style="display:inline;">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger">
                             Delete
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td class="text-center" colspan="12">Aucun administrateur trouvé.</td>
        </tr>

        @endforelse
    </tbody>

</table>
<div class="d-flex justify-content-center">{{ $admins->links('pagination::bootstrap-5') }}</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif
@include('admin.partials.modal-create')
@foreach($admins as $admin)
    @include('admin.partials.modal-edit', ['admin' => $admin])
@endforeach
@endsection

@section('my_js')
<!-- jQuery en premier si besoin -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap ensuite -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Cache pour les villes par pays
const cityCache = {};

function loadCities(countryId, citySelect, selected = null) {
    if (!countryId) {
        citySelect.empty().append('<option value="">Sélectionner un pays d\'abord</option>');
        return;
    }
    // Si données globales hydratées (optionnel)
    if (window._paysVilles) {
        const pays = window._paysVilles.find(p => p.id == countryId);
        const villes = pays ? pays.villes : [];
        populate(villes, citySelect, selected);
        return;
    }
    // Fallback AJAX avec cache
    if (cityCache[countryId]) {
        populate(cityCache[countryId], citySelect, selected);
        return;
    }
    citySelect.prop('disabled', true).html('<option>Chargement…</option>');
    $.getJSON(`/villes/${countryId}`, data => {
        cityCache[countryId] = data;
        populate(data, citySelect, selected);
    }).fail(() => {
        citySelect.html('<option>Erreur chargement</option>').prop('disabled', false);
    });
}

function populate(data, citySelect, selected) {
    citySelect.empty().append('<option value="">Sélectionner une ville</option>');
    data.forEach(v => {
        citySelect.append(
            `<option value="${v.id}" ${v.id==selected?'selected':''}>${v.nom}</option>`
        );
    });
    citySelect.prop('disabled', false);
}

  document.addEventListener('DOMContentLoaded', function() {
    // Injection sécurisée de l'ID stagiaire
    const STAGIAIRE_ID = @json($stagiaireId);

    // Fonction d'affichage des champs stagiaire
    function toggleStagiaire(roleSelect, container) {
      if (parseInt(roleSelect.val()) === STAGIAIRE_ID) {
        container.show();
      } else {
        container.hide().find('input').val('');
      }
    }

    // Fonction de chargement des villes (hydration / cache / AJAX)
    // … (ton code loadCities ici) …

    // Événements pour le modal « Ajouter »
    $('#modal-add').on('show.bs.modal', function() {
      const modal = $(this);
      toggleStagiaire(modal.find('#role_id_add'), modal.find('#fields_add'));
      loadCities(modal.find('#pays_id_add').val(), modal.find('#ville_id_add'));
    });
    $('#role_id_add').on('change', function() {
      toggleStagiaire($(this), $('#fields_add'));
    });
    $('#pays_id_add').on('change', function() {
      loadCities($(this).val(), $('#ville_id_add'));
    });

    // Événements pour chaque modal « Edit »
    @foreach($admins as $admin)
    $('#modal-edit-{{ $admin->id }}').on('show.bs.modal', function() {
      const m       = $(this);
      const roleSel = m.find('#role_id_edit_{{ $admin->id }}');
      const fields  = m.find('#fields_edit_{{ $admin->id }}');
      toggleStagiaire(roleSel, fields);
      loadCities(
        m.find('#pays_id_edit_{{ $admin->id }}').val(),
        m.find('#ville_id_edit_{{ $admin->id }}'),
        {{ $admin->ville_id }}
      );
    });
    $('#role_id_edit_{{ $admin->id }}').on('change', function() {
      toggleStagiaire($(this), $('#fields_edit_{{ $admin->id }}'));
    });
    $('#pays_id_edit_{{ $admin->id }}').on('change', function() {
      loadCities($(this).val(), $('#ville_id_edit_{{ $admin->id }}'));
    });
    @endforeach
  });
</script>
@endsection