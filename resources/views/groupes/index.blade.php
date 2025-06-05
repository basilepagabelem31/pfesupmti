@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h1 class="mb-4 text-primary">Gestion des Groupes</h1>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    {{-- DÉBUT DE L'AJOUT IMPORTANT POUR AFFICHER LES ERREURS SPÉCIFIQUES --}}
                    @if ($errors->any())
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    {{-- FIN DE L'AJOUT IMPORTANT --}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <button type="button" class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createGroupeModal">
                <i class="fas fa-plus-circle me-2"></i> Ajouter un nouveau Groupe
            </button>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Liste des Groupes</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="p-3">Code</th>
                                    <th scope="col" class="p-3">Nom</th>
                                    <th scope="col" class="p-3">Description</th>
                                    <th scope="col" class="p-3">Jour</th>
                                    <th scope="col" class="p-3">Heure Début</th>
                                    <th scope="col" class="p-3">Heure Fin</th>
                                    <th scope="col" class="p-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groupes as $groupe)
                                    <tr>
                                        <td class="p-2">{{ $groupe->code }}</td>
                                        <td class="p-2">{{ $groupe->nom }}</td>
                                        <td class="p-2">{{ $groupe->description }}</td>
                                        <td class="p-2">{{ \Carbon\Carbon::parse($groupe->jour)->isoFormat('dddd') }}</td>
                                        <td class="p-2">{{ \Carbon\Carbon::parse($groupe->heure_debut)->format('H:i') }}</td>
                                        <td class="p-2">{{ \Carbon\Carbon::parse($groupe->heure_fin)->format('H:i') }}</td>
                                        <td class="p-2 text-center">
                                            <button type="button" class="btn btn-sm btn-info text-white me-2" data-bs-toggle="modal" data-bs-target="#editGroupeModal"
                                                    data-id="{{ $groupe->id }}"
                                                    data-code="{{ $groupe->code }}"
                                                    data-nom="{{ $groupe->nom }}"
                                                    data-description="{{ $groupe->description }}"
                                                    data-jour="{{ $groupe->jour }}"
                                                    data-heure_debut="{{ $groupe->heure_debut }}"
                                                    data-heure_fin="{{ $groupe->heure_fin }}">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>

                                            <form action="{{ route('groupes.destroy', $groupe->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ? Cette action est irréversible et impossible si le groupe contient des stagiaires.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                                    <i class="fas fa-trash-alt"></i> Supprimer
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4 text-muted">Aucun groupe trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('groupes.create')
@include('groupes.edit')

@endsection

@push('scripts')
<script>
    // Script pour remplir le modal de modification
    var editGroupeModal = document.getElementById('editGroupeModal');
    if (editGroupeModal) { // Vérifier si le modal existe
        editGroupeModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Bouton qui a déclenché le modal
            var id = button.getAttribute('data-id');
            var code = button.getAttribute('data-code');
            var nom = button.getAttribute('data-nom');
            var description = button.getAttribute('data-description');
            var jour = button.getAttribute('data-jour');

            // --- C'EST ICI QUE SE TROUVE LA CORRECTION ---
            // Récupère l'heure complète (ex: "01:54:00") et la formate en "HH:MM"
            var heureDebutFull = button.getAttribute('data-heure_debut');
            var heureDebut = heureDebutFull ? heureDebutFull.substring(0, 5) : ''; // Prend les 5 premiers caractères

            var heureFinFull = button.getAttribute('data-heure_fin');
            var heureFin = heureFinFull ? heureFinFull.substring(0, 5) : ''; // Prend les 5 premiers caractères
            // ------------------------------------------

            var modalTitle = editGroupeModal.querySelector('.modal-title');
            var modalForm = editGroupeModal.querySelector('form');
            var modalCodeInput = editGroupeModal.querySelector('#edit_code');
            var modalNomInput = editGroupeModal.querySelector('#edit_nom');
            var modalDescriptionInput = editGroupeModal.querySelector('#edit_description');
            var modalJourInput = editGroupeModal.querySelector('#edit_jour');
            var modalHeureDebutInput = editGroupeModal.querySelector('#edit_heure_debut');
            var modalHeureFinInput = editGroupeModal.querySelector('#edit_heure_fin');

            modalTitle.textContent = 'Modifier le Groupe ' + nom;
            modalForm.action = '/groupes/' + id; // Assurez-vous que cette URL est correcte
            modalCodeInput.value = code;
            modalNomInput.value = nom;
            modalDescriptionInput.value = description;
            modalJourInput.value = jour;
            modalHeureDebutInput.value = heureDebut; // Utilisez la valeur formatée HH:MM
            modalHeureFinInput.value = heureFin;     // Utilisez la valeur formatée HH:MM
        });
    }
</script>
@endpush