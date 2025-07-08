@extends('layout.default')

@section('title', 'Gestion des Groupes')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-6xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-500 to-cyan-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-teal-600">Groupes</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Créez, modifiez et organisez les groupes de stagiaires avec leurs horaires.
            </p>

            {{-- Messages de session --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                    <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{!! session('error') !!}</span>
                    @if ($errors->any())
                        <ul class="list-disc list-inside text-base mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            {{-- Bouton Ajouter un nouveau Groupe --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#createGroupeModal">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un nouveau Groupe
                </button>
            </div>

            {{-- Tableau des Groupes --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-teal-50"> {{-- Nouvelle couleur pour l'en-tête du tableau --}}
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-teal-800 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-teal-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-teal-800 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-teal-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($groupes as $groupe)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $groupe->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $groupe->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $groupe->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out"
                                                data-bs-toggle="modal" data-bs-target="#editGroupeModal"
                                                data-id="{{ $groupe->id }}"
                                                data-code="{{ $groupe->code }}"
                                                data-nom="{{ $groupe->nom }}"
                                                data-description="{{ $groupe->description }}"
                                                >
                                            <i class="fas fa-edit"></i> 
                                        </button>

                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
    data-bs-toggle="modal"
    data-bs-target="#deleteConfirmationModal"
    data-item-id="{{ $groupe->id }}"
    data-item-title="{{ $groupe->nom }}"> {{-- Utilisation du nom du groupe pour l'affichage --}}
    <i class="fas fa-trash-alt"></i>
</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Aucun groupe trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajouter un Groupe --}}
<div class="modal fade" id="createGroupeModal" tabindex="-1" aria-labelledby="createGroupeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="createGroupeModalLabel">Ajouter un nouveau Groupe</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="createGroupeForm" action="{{ route('groupes.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="create_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                        <input type="text" id="create_nom" name="nom" value="{{ old('nom') }}" required
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('nom')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="create_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="create_description" name="description" rows="3"
                                  class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                   
                  
                 
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



{{-- Modal Supprimer un Groupe --}}

<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-bold text-gray-900 text-center flex-grow" id="deleteConfirmationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center text-gray-700">
                Êtes-vous sûr de vouloir supprimer le groupe "<span id="itemTitleToDelete" class="font-semibold text-red-600"></span>" ? Cette action est irréversible.
            </div>
            <div class="modal-footer flex justify-center space-x-4 mt-8 border-t border-gray-100 pt-4">
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>



{{-- Modal Modifier un Groupe --}}
<div class="modal fade" id="editGroupeModal" tabindex="-1" aria-labelledby="editGroupeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="editGroupeModalLabel">Modifier le Groupe</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="editGroupeForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_code" class="block text-sm font-medium text-gray-700 mb-1">Code du groupe</label>
                        {{-- Rendu en lecture seule car le code est généré automatiquement --}}
                        <input type="text" id="edit_code" name="code" readonly
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                        <input type="text" id="edit_nom" name="nom" required
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        @error('nom')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="edit_description" name="description" rows="3"
                                  class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
                        @error('description')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                  
                 
                  
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('my_js')


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script pour remplir le modal de modification
        var editGroupeModalElement = document.getElementById('editGroupeModal');
        if (editGroupeModalElement) {
            editGroupeModalElement.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Bouton qui a déclenché le modal
                var id = button.getAttribute('data-id');
                var code = button.getAttribute('data-code');
                var nom = button.getAttribute('data-nom');
                var description = button.getAttribute('data-description');
       

                var modalTitle = editGroupeModalElement.querySelector('.modal-title');
                var modalForm = editGroupeModalElement.querySelector('form');
                
                // Réinitialiser les erreurs de validation pour éviter les résidus
                // Utilisez jQuery pour trouver et retirer les classes et éléments
                $(modalForm).find('.is-invalid').removeClass('is-invalid');
                $(modalForm).find('.text-red-500.text-sm.mt-1').remove(); // Supprime les messages d'erreur Tailwind

                modalTitle.textContent = 'Modifier le Groupe : ' + nom;
                modalForm.action = '/groupes/' + id; // Assurez-vous que cette URL est correcte
                
                editGroupeModalElement.querySelector('#edit_code').value = code;
                editGroupeModalElement.querySelector('#edit_nom').value = nom;
                editGroupeModalElement.querySelector('#edit_description').value = description;
  
            });
        }

        // Script pour la gestion des erreurs de validation (pour les deux modales)
        @if ($errors->any())
            var createModalErrors = false;
            var editModalErrors = false;
            var editModalId = null;

            // Déterminer quelle modale était censée être ouverte (simple heuristique)
            // Si des erreurs existent pour des champs de la modale de création
            @if ($errors->has('nom') || $errors->has('description'))
                createModalErrors = true;
            @endif

            // Si des erreurs existent pour des champs de la modale d'édition
            // Il est recommandé que votre contrôleur passe un identifiant de groupe
            // en session si une erreur d'édition se produit (ex: `->with('edit_group_id', $groupe->id)`)
            // pour rouvrir la bonne modale après redirection.
            @if (session('edit_group_id'))
                editModalErrors = true;
                editModalId = 'editGroupeModal'; // Utilisez l'ID générique de la modale d'édition
            @endif

            if (createModalErrors) {
                var createModal = new bootstrap.Modal(document.getElementById('createGroupeModal'));
                createModal.show();
            } else if (editModalErrors && editModalId) {
                var editModal = new bootstrap.Modal(document.getElementById(editModalId));
                editModal.show();
            }

            // Afficher les erreurs de validation spécifiques aux champs du formulaire des modales
            @foreach ($errors->messages() as $field => $messages)
                var inputElement = document.getElementById('create_{{ $field }}') || document.getElementById('edit_{{ $field }}');
                if (inputElement) {
                    inputElement.classList.add('is-invalid'); // Ajoute la classe Bootstrap pour la validation
                    var feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1'); // Utilise les classes Tailwind pour le style
                    feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                    
                    // Insère le message d'erreur après l'input
                    inputElement.parentNode.appendChild(feedbackDiv);
                }
            @endforeach
        @endif
    });



document.addEventListener('DOMContentLoaded', function () {
    const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
    if (deleteConfirmationModal) {
        deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
            // Bouton qui a déclenché le modal
            const button = event.relatedTarget;

            // Récupère les informations de l'élément depuis les attributs data-*
            const itemId = button.getAttribute('data-item-id');
            const itemTitle = button.getAttribute('data-item-title');

            // Met à jour le contenu du modal
            const itemTitleSpan = deleteConfirmationModal.querySelector('#itemTitleToDelete');
            if (itemTitleSpan) {
                itemTitleSpan.textContent = itemTitle;
            }

            // Crée ou met à jour un formulaire de suppression caché dans le modal
            let deleteForm = deleteConfirmationModal.querySelector('#modalDeleteForm');
            if (!deleteForm) {
                deleteForm = document.createElement('form');
                deleteForm.setAttribute('id', 'modalDeleteForm');
                deleteForm.setAttribute('method', 'POST');
                deleteForm.style.display = 'none'; // Cache le formulaire
                deleteConfirmationModal.querySelector('.modal-body').appendChild(deleteForm);

                const csrfInput = document.createElement('input');
                csrfInput.setAttribute('type', 'hidden');
                csrfInput.setAttribute('name', '_token');
                csrfInput.setAttribute('value', '{{ csrf_token() }}');
                deleteForm.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.setAttribute('type', 'hidden');
                methodInput.setAttribute('name', '_method');
                methodInput.setAttribute('value', 'DELETE');
                deleteForm.appendChild(methodInput);
            }

            // Met à jour l'action du formulaire avec l'ID de l'élément
            // Utilise la route 'groupes.destroy' pour la suppression des groupes
            deleteForm.setAttribute('action', `/groupes/${itemId}`); // Correspond à la route ressource 'groupes.destroy'

            // Gère le clic sur le bouton "Supprimer" du modal
            const confirmDeleteButton = deleteConfirmationModal.querySelector('#confirmDeleteButton');
            if (confirmDeleteButton) {
                confirmDeleteButton.onclick = function () {
                    deleteForm.submit(); // Soumet le formulaire de suppression
                };
            }
        });
    }
});


</script>
@endsection
