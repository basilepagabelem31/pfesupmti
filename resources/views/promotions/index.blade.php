@extends('layout.default')

@section('title', 'Gestion des Promotions')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-5xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-blue-600">Promotions</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Créez, modifiez et gérez les promotions de stagiaires.
            </p>

            {{-- Bouton Ajouter une Promotion --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#promotionModal" onclick="openPromotionModal()">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter une Promotion
                </button>
            </div>

            {{-- Messages de session --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('success') }}</span>
                </div>
            @endif

            {{-- NOUVEAU: Message d'erreur de session --}}
            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                    <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Tableau des promotions --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Titre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Créé le</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($promotions as $promotion)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{$promotion->titre}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($promotion->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Archivée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{$promotion->created_at->format('d/m/Y')}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Bouton éditer --}}
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out"
                                            data-bs-toggle="modal" data-bs-target="#promotionModal"
                                            onclick="openPromotionModal({{ $promotion->id }}, '{{ addslashes($promotion->titre) }}', '{{ $promotion->status }}', '{{ route('promotions.update', $promotion->id) }}')">
                                      <i class="fas fa-edit"></i>

                                        </button>
                                     <button type="button" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmationModal"
                    data-promotion-id="{{ $promotion->id }}"
                    data-promotion-title="{{ $promotion->titre }}">
                    <i class="fas fa-trash-alt"></i>
                    </button>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune promotion disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



{{-- Modal Promotion (utilisée pour la suppression) --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-bold text-gray-900 text-center flex-grow" id="deleteConfirmationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center text-gray-700">
                Êtes-vous sûr de vouloir supprimer la promotion "<span id="promotionTitleToDelete" class="font-semibold text-red-600"></span>" ? Cette action est irréversible.
            </div>
            <div class="modal-footer flex justify-center space-x-4 mt-8 border-t border-gray-100 pt-4">
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>



{{-- Modal Promotion (utilisée pour Ajouter et Modifier) --}}
<div class="modal fade" id="promotionModal" tabindex="-1" aria-labelledby="promotionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <form id="promotionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="promotionFormMethod" value="POST">
                <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                    <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="promotionModalLabel">Ajouter une Promotion</h5>
                    <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body space-y-6">
                    <div class="mb-3">
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="titre" name="titre" required>
                    </div>
                    <div class="mb-3" id="statusField" style="display: none">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="archive">Archivée</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer flex justify-end space-x-4 mt-8 border-t border-gray-100 pt-4">
                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('my_js')


<script>
function openPromotionModal(id = null, titre = '', status = 'active', updateUrl = null) {
    let form = document.getElementById('promotionForm');
    form.reset(); // Réinitialise le formulaire

    // Gérer l'affichage des messages d'erreur de validation Bootstrap
    // en retirant les classes 'is-invalid' ou les messages d'erreur précédents
    $(form).find('.is-invalid').removeClass('is-invalid');
    $(form).find('.invalid-feedback').remove();

    document.getElementById('titre').value = titre || '';
    document.getElementById('promotionModalLabel').textContent = id ? 'Modifier la Promotion' : 'Ajouter une Promotion';

    if (id) {
        form.action = updateUrl;
        document.getElementById('promotionFormMethod').value = 'PUT';
        document.getElementById('statusField').style.display = 'block'; // Afficher le champ statut
        document.getElementById('status').value = status;
        $(form).find('.modal-footer .btn-success').text('Modifier').removeClass('bg-green-600 focus:ring-green-500').addClass('bg-yellow-600 focus:ring-yellow-500 hover:bg-yellow-700');
    } else {
        form.action = "{{ route('promotions.store') }}";
        document.getElementById('promotionFormMethod').value = 'POST';
        document.getElementById('statusField').style.display = 'none'; // Masquer le champ statut
        $(form).find('.modal-footer .btn-success').text('Ajouter').removeClass('bg-yellow-600 focus:ring-yellow-500 hover:bg-yellow-700').addClass('bg-green-600 focus:ring-green-500');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Écouteur pour la soumission du formulaire de la modale
    $('#promotionForm').on('submit', function(e) {
        // Optionnel: Empêcher la soumission normale pour AJAX si vous souhaitez un comportement plus avancé
        // e.preventDefault(); 
        // ... (logique AJAX ici) ...
    });

    // Gérer les erreurs de validation après une soumission qui rafraîchit la page
    @if ($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('promotionModal'));
        modal.show();

        // Afficher les erreurs de validation spécifiques aux champs du formulaire de la modale
        @foreach ($errors->messages() as $field => $messages)
            var inputElement = document.getElementById('{{ $field }}');
            if (inputElement) {
                inputElement.classList.add('is-invalid');
                var feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('invalid-feedback');
                feedbackDiv.textContent = '{{ implode(", ", $messages) }}'; // Affiche tous les messages d'erreur
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

            // Récupère les informations de la promotion depuis les attributs data-*
            const promotionId = button.getAttribute('data-promotion-id');
            const promotionTitle = button.getAttribute('data-promotion-title');

            // Met à jour le contenu du modal
            const promotionTitleSpan = deleteConfirmationModal.querySelector('#promotionTitleToDelete');
            if (promotionTitleSpan) {
                promotionTitleSpan.textContent = promotionTitle;
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

            // Met à jour l'action du formulaire avec l'ID de la promotion
            deleteForm.setAttribute('action', `/promotions/${promotionId}`); // Assurez-vous que cette route correspond à votre `route('promotions.destroy', $promotion)` [cite: 656]

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
