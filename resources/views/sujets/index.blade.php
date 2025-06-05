@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">

            <h1 class="mb-4 text-primary">Gestion des Sujets</h1>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    @if ($errors->any())
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Bouton d'ajout de sujet qui ouvre le modal -->
            <button type="button" class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createSujetModal">
                <i class="fas fa-plus-circle me-2"></i> Ajouter un nouveau Sujet
            </button>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Liste des Sujets</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="p-3">Titre</th>
                                    <th scope="col" class="p-3">Description</th>
                                    <th scope="col" class="p-3">Promotion</th>
                                    <th scope="col" class="p-3">Groupe</th>
                                    <th scope="col" class="p-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sujets as $sujet)
                                    <tr>
                                        <td class="p-2">{{ $sujet->titre }}</td>
                                        <td class="p-2">{{ $sujet->description }}</td>
                                        <td class="p-2">{{ $sujet->promotion->titre ?? 'N/A' }}</td> {{-- Affiche le titre de la promotion --}}
                                        <td class="p-2">{{ $sujet->groupe->nom ?? 'N/A' }}</td> {{-- Affiche le nom du groupe --}}
                                        <td class="p-2 text-center">
                                            <!-- Bouton Modifier -->
                                            <button type="button" class="btn btn-sm btn-info text-white me-2" data-bs-toggle="modal" data-bs-target="#editSujetModal"
                                                    data-id="{{ $sujet->id }}"
                                                    data-titre="{{ $sujet->titre }}"
                                                    data-description="{{ $sujet->description }}"
                                                    data-promotion_id="{{ $sujet->promotion_id }}"
                                                    data-groupe_id="{{ $sujet->groupe_id }}">
                                                <i class="fas fa-edit"></i> Modifier
                                            </button>

                                            <!-- Bouton Supprimer -->
                                            <form action="{{ route('sujets.destroy', $sujet->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce sujet ? Cette action est irréversible et impossible si des stagiaires y sont inscrits.');">
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
                                        <td colspan="5" class="text-center p-4 text-muted">Aucun sujet trouvé.</td>
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

<!-- Modal d'Ajout de Sujet -->
@include('sujets._create_modal', ['promotions' => $promotions, 'groupes' => $groupes])

<!-- Modal de Modification de Sujet -->
@include('sujets._edit_modal', ['promotions' => $promotions, 'groupes' => $groupes])

@endsection

@push('scripts')
<script>
    // Script pour remplir le modal de modification
    var editSujetModal = document.getElementById('editSujetModal');
    if (editSujetModal) {
        editSujetModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Bouton qui a déclenché le modal
            var id = button.getAttribute('data-id');
            var titre = button.getAttribute('data-titre');
            var description = button.getAttribute('data-description');
            var promotionId = button.getAttribute('data-promotion_id');
            var groupeId = button.getAttribute('data-groupe_id');

            var modalTitle = editSujetModal.querySelector('.modal-title');
            var modalForm = editSujetModal.querySelector('form');
            var modalTitreInput = editSujetModal.querySelector('#edit_titre');
            var modalDescriptionInput = editSujetModal.querySelector('#edit_description');
            var modalPromotionSelect = editSujetModal.querySelector('#edit_promotion_id');
            var modalGroupeSelect = editSujetModal.querySelector('#edit_groupe_id');

            modalTitle.textContent = 'Modifier le Sujet : ' + titre;
            modalForm.action = '/sujets/' + id; // Assurez-vous que cette URL est correcte
            modalTitreInput.value = titre;
            modalDescriptionInput.value = description;

            // Sélectionne l'option correcte dans le select de promotion
            if (modalPromotionSelect) {
                Array.from(modalPromotionSelect.options).forEach(option => {
                    if (option.value == promotionId) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                });
            }

            // Sélectionne l'option correcte dans le select de groupe
            if (modalGroupeSelect) {
                Array.from(modalGroupeSelect.options).forEach(option => {
                    if (option.value == groupeId) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                });
            }
        });
    }
</script>
@endpush