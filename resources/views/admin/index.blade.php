@extends('layout.default')

@section('title', 'Gestion Admin & Superviseurs')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-indigo-600">Administrateurs</span> et <span class="text-purple-600">Superviseurs</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez, ajoutez, modifiez et supprimez les comptes administratifs et de supervision.
            </p>

            {{-- Bouton Ajouter --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#modal-add">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un Administrateur/Superviseur
                </button>
            </div>

            {{-- Messages de session --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 shadow-md border border-red-200">
                    <p class="font-bold mb-3 flex items-center space-x-2 text-lg">
                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span>Erreurs de validation :</span>
                    </p>
                    <ul class="list-disc list-inside text-base mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tableau des Administrateurs/Superviseurs --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    {{-- Modification de la couleur de l'en-tête du tableau --}}
                    <thead class="bg-indigo-50"> 
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Prénom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">CIN</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Adresse</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Pays</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Ville</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Rôle</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-800 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($admins as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->prenom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->telephone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->cin }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->adresse }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->pays?->nom  }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->ville?->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($admin->role?->nom == 'Administrateur') bg-indigo-100 text-indigo-800
                                    @elseif($admin->role?->nom == 'Superviseur') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $admin->role?->nom }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $admin->id }}">
                                        Modifier
                                    </button>
                                    <form action="{{ route('admin.delete', $admin->id) }}" onsubmit=" return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')" method="post" class="inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-4 text-center text-gray-500" colspan="12">Aucun administrateur ou superviseur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                <div class="flex justify-center">
                    {{-- Utilise la pagination par défaut de Bootstrap 5 car les classes sont déjà définies --}}
                    {{ $admins->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Inclusion des modales (leur contenu DOIT être stylisé avec Tailwind si désiré) --}}
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
    $.getJSON(`/villes/by-pays/${countryId}`, data => {
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
    // Note: STAGIAIRE_ID est potentiellement mal nommé ici s'il représente le rôle ID pour "Stagiaire".
    // Assurez-vous que cette variable est correctement définie dans le contrôleur ou ailleurs.
    // Par exemple, si vous avez un ID de rôle 3 pour "Stagiaire", vous devez le passer ici.
    const STAGIAIRE_ID = @json($stagiaireId ?? null); // Passez null par défaut si non défini

    // Fonction d'affichage des champs stagiaire
    function toggleStagiaire(roleSelect, container) {
      // S'assurer que STAGIAIRE_ID est un nombre pour la comparaison
      if (STAGIAIRE_ID !== null && parseInt(roleSelect.val()) === parseInt(STAGIAIRE_ID)) {
        container.show();
      } else {
        container.hide().find('input, select').val(''); // Clear inputs and selects
      }
    }

    // Événements pour le modal « Ajouter »
    $('#modal-add').on('show.bs.modal', function() {
      const modal = $(this);
      // Réinitialiser les champs et l'état des champs stagiaires à l'ouverture de la modale d'ajout
      modal.find('#role_id_add').val(''); // Réinitialiser le sélecteur de rôle
      modal.find('#fields_add').hide().find('input, select').val(''); // Cacher et vider les champs stagiaires
      
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
      // Fix pour la valeur de la ville déjà sélectionnée
      const currentVilleId = {{ $admin->ville_id ?? 'null' }};

      toggleStagiaire(roleSel, fields);
      loadCities(
        m.find('#pays_id_edit_{{ $admin->id }}').val(),
        m.find('#ville_id_edit_{{ $admin->id }}'),
        currentVilleId
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
