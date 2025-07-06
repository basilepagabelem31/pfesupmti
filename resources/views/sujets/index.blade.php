@extends('layout.default')

@section('title', 'Gestion des Sujets')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-6xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-500 to-teal-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-green-600">Sujets</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Créez, modifiez et gérez les sujets de stage et les inscriptions des stagiaires.
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

            {{-- Bouton Ajouter un Sujet --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#sujetModal" onclick="openSujetModal()">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un Sujet
                </button>
            </div>

            {{-- Table des sujets --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-green-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Titre</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Promotion</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-green-800 uppercase tracking-wider">Groupe</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-green-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($sujets as $sujet)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sujet->titre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sujet->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sujet->promotion->titre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sujet->groupe->nom ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out"
                                            data-bs-toggle="modal" data-bs-target="#sujetModal"
                                            onclick="openSujetModal({{ $sujet->id }}, '{{ addslashes($sujet->titre) }}', `{{ addslashes($sujet->description) }}`, {{ $sujet->promotion_id ?? 'null' }}, {{ $sujet->groupe_id ?? 'null' }}, '{{ route('sujets.update', $sujet->id) }}')">
                                            <i class="fas fa-edit"></i> 
                                        </button>
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"
                                            data-sujet-id="{{ $sujet->id }}" data-sujet-titre="{{ addslashes($sujet->titre) }}">
                                            <i class="fas fa-trash-alt"></i> 
                                        </button>
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out" data-bs-toggle="modal" data-bs-target="#inscriptionsModal"
                                            onclick="openInscriptionsModal({{ $sujet->id }}, '{{ addslashes($sujet->titre) }}')">
                                            <i class="fas fa-users-cog"></i> 
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun sujet trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Sujet (Ajouter/Modifier) --}}
<div class="modal fade" id="sujetModal" tabindex="-1" aria-labelledby="sujetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="sujetModalLabel">Ajouter un Sujet</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="sujetForm" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" id="sujetFormMethod" value="POST">
                    <div>
                        <label for="titre" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" id="titre" name="titre" value="{{ old('titre') }}" required
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('titre')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="promotion_id" class="block text-sm font-medium text-gray-700 mb-1">Promotion (active seulement)</label>
                        <select id="promotion_id" name="promotion_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Sélectionner</option>
                            @foreach($promotions as $promotion)
                                <option value="{{ $promotion->id }}" {{ old('promotion_id') == $promotion->id ? 'selected' : '' }}>{{ $promotion->titre }}</option>
                            @endforeach
                        </select>
                        @error('promotion_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="groupe_id" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                        <select id="groupe_id" name="groupe_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Sélectionner...</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}" {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                        @error('groupe_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="deleteConfirmationModalLabel">Confirmer la Suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir supprimer le sujet : <span id="sujetTitreToDelete" class="font-semibold text-red-600"></span> ?</p>
                <p class="text-red-500 text-sm font-semibold">Cette action est irréversible et impossible si le sujet contient des stagiaires.</p>
                <form id="deleteSujetForm" method="POST" class="flex justify-end space-x-4 mt-8">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Modal Gérer les inscriptions (Dynamic content via JS) --}}
<div class="modal fade" id="inscriptionsModal" tabindex="-1" aria-labelledby="inscriptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="inscriptionsModalLabel">Gérer les inscriptions pour : <span id="sujetTitreInscriptions"></span></h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body space-y-6">
                <div id="inscribedStagiairesList" class="mb-6">
                    <h6 class="text-xl font-semibold text-gray-800 mb-3">Stagiaires inscrits :</h6>
                    <ul class="list-disc list-inside text-gray-700 space-y-2" id="inscribedStagiairesUl">
                        {{-- Content will be loaded via JS --}}
                    </ul>
                    <p id="noInscribedStagiaires" class="text-gray-500 text-sm italic mt-2 hidden">Aucun stagiaire inscrit à ce sujet.</p>
                </div>

                <div id="inscrireStagiaireFormContainer">
                    <h6 class="text-xl font-semibold text-gray-800 mb-3">Inscrire un stagiaire :</h6>
                    <form method="POST" id="formInscrireStagiaire" class="space-y-4">
                        @csrf
                        <input type="hidden" name="sujet_id" id="inscrireSujetId">
                        <select name="stagiaire_id" id="selectStagiaireToInscribe" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Sélectionner un stagiaire</option>
                            {{-- Options will be loaded via JS --}}
                        </select>
                        @error('stagiaire_id') {{-- Add error handling for stagiaire_id --}}
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <i class="fas fa-user-plus mr-2"></i> Inscrire
                        </button>
                    </form>
                </div>
            </div>
            <div class="modal-footer border-t border-gray-100 pt-4 mt-6 flex justify-end">
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('my_js')


<script>
    // Fonction pour ouvrir le modal Ajouter/Modifier Sujet
    function openSujetModal(id = null, titre = '', description = '', promotion_id = null, groupe_id = null, updateUrl = '') {
        let form = document.getElementById('sujetForm');
        let modalTitle = document.getElementById('sujetModalLabel');
        let sujetFormMethod = document.getElementById('sujetFormMethod');

        // Clear previous validation errors
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('.text-red-500.text-sm.mt-1').remove();

        form.reset(); // Reset form fields

        if (id) {
            modalTitle.textContent = 'Modifier le Sujet';
            form.action = updateUrl;
            sujetFormMethod.value = 'PUT'; // Set to PUT for update
            document.getElementById('titre').value = titre;
            document.getElementById('description').value = description;
            document.getElementById('promotion_id').value = promotion_id;
            document.getElementById('groupe_id').value = groupe_id;
        } else {
            modalTitle.textContent = 'Ajouter un Sujet';
            form.action = "{{ route('sujets.store') }}";
            sujetFormMethod.value = 'POST'; // Set to POST for create
            // Ensure select fields are reset to default option
            document.getElementById('promotion_id').value = '';
            document.getElementById('groupe_id').value = '';
        }
    }

    // Fonction pour ouvrir le modal de confirmation de suppression
    document.addEventListener('DOMContentLoaded', function () {
        var deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        if (deleteConfirmationModal) {
            deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Bouton qui a déclenché le modal
                var sujetId = button.getAttribute('data-sujet-id');
                var sujetTitre = button.getAttribute('data-sujet-titre');

                var modalTitleSpan = deleteConfirmationModal.querySelector('#sujetTitreToDelete');
                modalTitleSpan.textContent = sujetTitre;

                var deleteForm = document.getElementById('deleteSujetForm');
                deleteForm.action = `/sujets/${sujetId}`; // Assurez-vous que cette URL est correcte
            });
        }
    });

    // Fonction pour ouvrir le modal Gérer les inscriptions
    async function openInscriptionsModal(sujetId, sujetTitre) {
        document.getElementById('sujetTitreInscriptions').textContent = sujetTitre;
        document.getElementById('inscrireSujetId').value = sujetId;

        const inscribedStagiairesUl = document.getElementById('inscribedStagiairesUl');
        const selectStagiaireToInscribe = document.getElementById('selectStagiaireToInscribe');
        const noInscribedStagiaires = document.getElementById('noInscribedStagiaires');

        // Clear previous content
        inscribedStagiairesUl.innerHTML = '';
        selectStagiaireToInscribe.innerHTML = '<option value="">Sélectionner un stagiaire</option>';
        noInscribedStagiaires.classList.add('hidden');

        // Clear previous validation errors for the inscription form
        const formInscrireStagiaire = document.getElementById('formInscrireStagiaire');
        $(formInscrireStagiaire).find('.is-invalid').removeClass('is-invalid');
        $(formInscrireStagiaire).find('.text-red-500.text-sm.mt-1').remove();


        // Fetch inscribed and available stagiaires via AJAX
        try {
            const response = await fetch(`/sujets/${sujetId}/stagiaires-for-enrollment`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json(); // Expects { inscribed: [], available: [] }

            // Populate inscribed stagiaires
            if (data.inscribed.length > 0) {
                data.inscribed.forEach(stagiaire => {
                    const li = document.createElement('li');
                    li.classList.add('flex', 'items-center', 'justify-between', 'py-1', 'group'); // Added group for hover effects
                    li.innerHTML = `
                        <span class="text-gray-700">${stagiaire.prenom} ${stagiaire.nom}</span>
                        <form method="POST" action="/sujets/${sujetId}/desinscrire/${stagiaire.id}" class="inline-block" onsubmit="return confirm('Voulez-vous vraiment désinscrire ce stagiaire ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2 py-1 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out text-sm">Désinscrire</button>
                        </form>
                    `;
                    inscribedStagiairesUl.appendChild(li);
                });
            } else {
                noInscribedStagiaires.classList.remove('hidden');
            }

            // Populate available stagiaires for inscription dropdown
            data.available.forEach(stagiaire => {
                const option = document.createElement('option');
                option.value = stagiaire.id;
                option.textContent = `${stagiaire.prenom} ${stagiaire.nom}`;
                selectStagiaireToInscribe.appendChild(option);
            });

            // Set the form action for inscription
            formInscrireStagiaire.action = `/sujets/${sujetId}/inscrire`;

        } catch (error) {
            console.error("Error fetching stagiaires for enrollment:", error);
            // Instead of alert, consider showing a message in the modal body
            // var errorMessageDiv = document.createElement('div');
            // errorMessageDiv.classList.add('text-red-500', 'text-center', 'mt-4');
            // errorMessageDiv.textContent = "Une erreur est survenue lors du chargement des stagiaires.";
            // document.getElementById('inscriptionsModal').querySelector('.modal-body').appendChild(errorMessageDiv);
            alert("Une erreur est survenue lors du chargement des stagiaires.");
        }
    }


    // Script pour la gestion des erreurs de validation (pour les modales)
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            var createSujetModalErrors = false;
            var editSujetModalErrors = false;
            var inscriptionModalErrors = false;

            // Déterminer quelle modale de sujet doit être ouverte
            // Pour le modal d'ajout/modification de sujet:
            @if ($errors->has('titre') || $errors->has('description') || $errors->has('promotion_id') || $errors->has('groupe_id'))
                // Si l'un des champs de sujet a une erreur
                createSujetModalErrors = true;
            @endif

            // Pour la modale d'inscription:
            @if ($errors->has('stagiaire_id') && session('sujet_id_for_modal'))
                inscriptionModalErrors = true;
            @endif

            if (createSujetModalErrors) {
                var sujetModal = new bootstrap.Modal(document.getElementById('sujetModal'));
                sujetModal.show();
            } else if (inscriptionModalErrors) {
                 // Si c'est une erreur d'inscription, on a besoin du sujet_id et du titre pour rouvrir la bonne modale
                 // Vous devez passer ces valeurs en session depuis le contrôleur en cas d'erreur de validation
                 // Ex: return back()->withInput()->withErrors($validator)->with('sujet_id_for_modal', $sujet->id)->with('sujet_titre_for_modal', $sujet->titre);
                 const sujetIdForModal = {{ session('sujet_id_for_modal') ?? 'null' }};
                 const sujetTitreForModal = `{!! addslashes(session('sujet_titre_for_modal')) !!}`; // Utilisez addslashes pour les titres avec apostrophes

                 if (sujetIdForModal && sujetTitreForModal) {
                     openInscriptionsModal(sujetIdForModal, sujetTitreForModal).then(() => {
                        var inscriptionsModal = new bootstrap.Modal(document.getElementById('inscriptionsModal'));
                        inscriptionsModal.show();
                     });
                 }
            }


            // Afficher les erreurs de validation spécifiques aux champs du formulaire des modales
            @foreach ($errors->messages() as $field => $messages)
                var inputElement;
                if ('{{ $field }}' === 'stagiaire_id') {
                    // Pour le champ stagiaire_id dans la modale d'inscription
                    inputElement = document.getElementById('selectStagiaireToInscribe');
                } else {
                    // Pour les champs dans la modale d'ajout/modification de sujet
                    inputElement = document.getElementById('{{ $field }}');
                }

                if (inputElement) {
                    inputElement.classList.add('is-invalid'); // Ajoute la classe Bootstrap pour la validation
                    var feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1'); // Utilise les classes Tailwind
                    feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                    inputElement.parentNode.appendChild(feedbackDiv);
                }
            @endforeach
        @endif
    });
</script>
@endsection
