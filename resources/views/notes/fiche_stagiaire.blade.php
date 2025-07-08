@extends('layout.default')

@section('title', 'Notes du Stagiaire - ' . $stagiaire->prenom . ' ' . $stagiaire->nom)

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-4xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-500 to-teal-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Notes pour <span class="text-green-600">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Gérez et consultez les notes du stagiaire.
            </p>

            {{-- Messages de session --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
                </div>
            @endif
            @if(Session::has('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                    <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('error') }}</span>
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

            {{-- Section pour ajouter une note (visible uniquement par Superviseurs/Admins) --}}
            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                @include('notes.partials.form', ['stagiaire' => $stagiaire])
            @endif

            <hr class="my-8 border-gray-300">
            <h4 class="text-2xl font-bold text-gray-800 mb-4">Historique des notes</h4>
            <div class="space-y-4">
            @forelse($notes as $note)
                @php
                    $peutVoir = false;
                    $user = Auth::user();
                    
                    if ($note->visibilite === 'all') {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'donneur' && $note->donneur_id === $user->id) {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'donneur + stagiaire' && ($note->donneur_id === $user->id || $note->stagiaire_id === $user->id)) {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'superviseurs- stagiaire' && ($user->isSuperviseur() || $user->isAdministrateur())) {
                        $peutVoir = true;
                    }
                @endphp
                @if($peutVoir)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex-grow mb-3 sm:mb-0">
                            <p class="text-gray-700 text-base mb-2">
                                <strong>{{ \Carbon\Carbon::parse($note->date_note)->format('d/m/Y') }}</strong> : {{ $note->valeur }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Ajoutée par: <span class="font-semibold">{{ $note->donneur->prenom }} {{ $note->donneur->nom }}</span>
                                @if($note->is_propagated)
                                    <span class="ml-2 text-purple-600 font-semibold">(Propagée de {{ $note->originalStagiaire->prenom ?? 'N/A' }} {{ $note->originalStagiaire->nom ?? '' }})</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-0 sm:ml-4 text-sm font-semibold mt-2 sm:mt-0">
                            @php
                                $visibilityText = '';
                                $visibilityClass = '';
                                switch ($note->visibilite) {
                                    case 'all':
                                        $visibilityText = 'Visible par tous';
                                        $visibilityClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'donneur':
                                        $visibilityText = 'Donneur uniquement';
                                        $visibilityClass = 'bg-red-100 text-red-800';
                                        break;
                                    case 'donneur + stagiaire':
                                        $visibilityText = 'Donneur et Stagiaire';
                                        $visibilityClass = 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'superviseurs- stagiaire':
                                        $visibilityText = 'Superviseurs seulement (Privée au Stagiaire)';
                                        $visibilityClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    default:
                                        $visibilityText = 'Visibilité inconnue';
                                        $visibilityClass = 'bg-gray-100 text-gray-800';
                                        break;
                                }
                            @endphp
                            <span class="px-2 py-1 rounded-full {{ $visibilityClass }}">
                                {{ $visibilityText }}
                            </span>
                        </div>
                        {{-- Actions Modifier/Supprimer (visibles uniquement par le donneur de la note) --}}
                        @if(Auth::id() === $note->donneur_id)
                            <div class="flex-shrink-0 ml-0 sm:ml-4 mt-3 sm:mt-0 flex space-x-2">
  <a href="{{ route('notes.edit', $note->id) }}" 
   class="inline-flex items-center justify-center w-8 h-8 p-0 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">
    <i class="fas fa-edit"></i> </a>

    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="inline-block">
        @csrf
        @method('DELETE')
        <button type="button"
    class="inline-flex items-center justify-center w-8 h-8 p-0 rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out delete-note-btn"
    data-bs-toggle="modal"
    data-bs-target="#deleteConfirmationModal"
    data-item-id="{{ $note->id }}"
    data-item-title="la note du {{ optional($note->date)->format('d/m/Y') ?: 'Date non spécifiée' }} ({{ Str::limit($note->libelle ?? '', 20) }})"
    data-action-type="delete-note"
    data-form-action="{{ route('notes.destroy', $note->id) }}"
>
    <i class="fas fa-trash-alt"></i> </button>
    </form>
</div>
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-gray-500 text-center py-4">Aucune note trouvée pour ce stagiaire.</p>
            @endforelse
            </div>

            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                <div class="mt-6 text-center">
                    <a href="{{ route('notes.liste_stagiaires') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-semibold rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>



<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl shadow-lg border-0">
            <div class="modal-header bg-gradient-to-r from-red-500 to-red-700 text-white border-b-0 rounded-t-xl p-4">
                <h5 class="modal-title font-bold text-xl flex items-center" id="deleteConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill mr-2"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close text-white opacity-80" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 text-center text-gray-700">
                <p class="text-lg">
                    Êtes-vous sûr de vouloir supprimer <span id="itemTitleToDelete" class="font-semibold text-red-600">cet élément</span> ?
                    <br>
                    Cette action est irréversible.
                </p>
                <form id="dynamicModalForm" method="POST" style="display: none;">
                    @csrf
                    </form>
            </div>
            <div class="modal-footer flex justify-center bg-gray-50 rounded-b-xl p-4">
                <button type="button" class="btn px-5 py-2.5 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" data-bs-dismiss="modal">
                    Annuler
                </button>
                <button type="button" id="confirmDeleteButton" class="btn px-5 py-2.5 rounded-lg text-white bg-red-600 hover:bg-red-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>


@endsection



<script>


document.addEventListener('DOMContentLoaded', function () {
    const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');

    if (deleteConfirmationModal) {
        deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Le bouton qui a déclenché le modal

            const itemId = button.getAttribute('data-item-id');
            const itemTitle = button.getAttribute('data-item-title');
            const actionType = button.getAttribute('data-action-type'); // 'cloture-reunion' ou 'delete-note' ou autre
            const formAction = button.getAttribute('data-form-action'); // Récupère l'action du formulaire

            const modalTitleElement = deleteConfirmationModal.querySelector('.modal-title');
            const modalBodySpan = deleteConfirmationModal.querySelector('.modal-body #itemTitleToDelete');
            const confirmButton = deleteConfirmationModal.querySelector('#confirmDeleteButton');

            // --- Configuration du contenu du modal ---
            if (actionType === 'cloture-reunion') {
                modalTitleElement.textContent = 'Confirmer la clôture de la réunion';
                if (modalBodySpan) {
                    modalBodySpan.innerHTML = `la réunion du <span class="font-semibold text-red-600">${itemTitle}</span>`;
                }
                confirmButton.textContent = 'Clôturer la réunion';
                confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700');
                confirmButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            } else if (actionType === 'delete-note') { // Nouvelle condition pour la suppression de note
                modalTitleElement.textContent = 'Confirmer la suppression de la note';
                if (modalBodySpan) {
                    modalBodySpan.innerHTML = `la note "${itemTitle}"`;
                }
                confirmButton.textContent = 'Supprimer la note';
                confirmButton.classList.remove('bg-blue-600', 'hover:bg-blue-700'); // S'assurer que les couleurs sont rouges pour la suppression
                confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');
            }
            // Ajoutez d'autres `else if` pour d'autres types de suppression (utilisateur, groupe, etc.)
            else { // Logique par défaut pour les suppressions générales (si vous en avez)
                modalTitleElement.textContent = 'Confirmer la suppression';
                if (modalBodySpan) {
                     modalBodySpan.innerHTML = `"${itemTitle}"`; // Texte par défaut pour la suppression
                }
                confirmButton.textContent = 'Supprimer';
                confirmButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                confirmButton.classList.add('bg-red-600', 'hover:bg-red-700');
            }


            // --- Logique pour gérer le formulaire de soumission dynamique ---
            let dynamicForm = document.getElementById('dynamicModalForm');
            if (!dynamicForm) {
                dynamicForm = document.createElement('form');
                dynamicForm.setAttribute('id', 'dynamicModalForm');
                dynamicForm.setAttribute('method', 'POST');
                dynamicForm.style.display = 'none';
                document.body.appendChild(dynamicForm);

                const csrfInput = document.createElement('input');
                csrfInput.setAttribute('type', 'hidden');
                csrfInput.setAttribute('name', '_token');
                csrfInput.setAttribute('value', '{{ csrf_token() }}');
                dynamicForm.appendChild(csrfInput);
            }

            // Supprime l'ancien input _method si présent
            let methodInput = dynamicForm.querySelector('input[name="_method"]');
            if (methodInput) {
                dynamicForm.removeChild(methodInput);
            }

            // Définit l'action et la méthode _method selon le type d'action
            if (actionType === 'cloture-reunion') {
                dynamicForm.setAttribute('action', `/reunions/${itemId}/cloture`);
                // Pas de _method='DELETE' pour une requête POST de clôture
            } else if (formAction) { // Pour toutes les actions de suppression avec data-form-action
                dynamicForm.setAttribute('action', formAction);
                methodInput = document.createElement('input');
                methodInput.setAttribute('type', 'hidden');
                methodInput.setAttribute('name', '_method');
                methodInput.setAttribute('value', 'DELETE');
                dynamicForm.appendChild(methodInput);
            } else {
                console.error('Missing data-form-action for delete operation or unhandled actionType.');
                // Gérer les cas non prévus si nécessaire
            }


            // Attache l'événement click au bouton de confirmation du modal
            confirmButton.onclick = function () {
                dynamicForm.submit(); // Soumet le formulaire dynamique
            };
        });
    }
});


</script>
