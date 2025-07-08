@extends('layout.default')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-500 to-cyan-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Mes <span class="text-teal-600">Fichiers</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez et gérez vos documents téléversés.
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
                    <span class="font-semibold text-lg">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Bouton Ajouter un Fichier --}}
            <div class="flex justify-start mb-8">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150 ease-in-out uppercase tracking-wider"
                    data-bs-toggle="modal" data-bs-target="#createFileModal">
                    <i class="fas fa-upload mr-2"></i> Téléverser un nouveau fichier
                </button>
            </div>

            @if ($fichiers->isEmpty())
                <div class="bg-blue-50 text-blue-800 p-6 rounded-xl flex items-center justify-center space-x-3 shadow-md border border-blue-200">
                    <svg class="h-10 w-10 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <p class="font-semibold text-xl">Aucun fichier trouvé pour le moment.</p>
                </div>
            @else
                <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 mb-8">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Nom du Fichier</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Téléversé par</th>
                                <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider text-center">Modifiable</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider text-center">Supprimable</th> -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-800 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($fichiers as $fichier)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center">
                                        <i class="fas fa-file-alt text-gray-500 mr-2"></i> {{ $fichier->nom_fichier }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($fichier->description, 50) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($fichier->type_fichier) }}
                                        </span>
                                    </td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
    {{ $fichier->televerseur?->prenom }} {{ $fichier->televerseur?->nom }}
</td>                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $fichier->peut_modifier ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $fichier->peut_modifier ? 'Oui' : 'Non' }}
                                        </span>
                                    </td> -->
                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $fichier->peut_supprimer ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $fichier->peut_supprimer ? 'Oui' : 'Non' }}
                                        </span>
                                    </td> -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $fichier->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('fichiers.download', $fichier) }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                                <i class="fas fa-download mr-1"></i> 
                                            </a>
                                            {{-- Seul le stagiaire propriétaire peut modifier si la permission est vraie --}}
                                            @if (Auth::user()->id === $fichier->id_stagiaire && $fichier->peut_modifier)
                                            <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out"
                                                data-bs-toggle="modal" data-bs-target="#editFileModal"
                                                data-id="{{ $fichier->id }}"
                                                data-nom_fichier="{{ $fichier->nom_fichier }}"
                                                data-description="{{ $fichier->description }}"
                                                data-type_fichier="{{ $fichier->type_fichier }}"
                                                data-sujet_id="{{ $fichier->sujet_id }}"
                                                data-peut_modifier="{{ $fichier->peut_modifier ? '1' : '0' }}"
                                                data-peut_supprimer="{{ $fichier->peut_supprimer ? '1' : '0' }}">
                                                <i class="fas fa-edit mr-1"></i> 
                                            </button>
                                            @endif
                                            {{-- Seul le stagiaire propriétaire peut supprimer si la permission est vraie --}}
                                            @if (Auth::user()->id === $fichier->id_stagiaire && $fichier->peut_supprimer)
                                            <button type="button" class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                                data-bs-toggle="modal" data-bs-target="#deleteFileModal"
                                                data-file-id="{{ $fichier->id }}"
                                                data-file-name="{{ $fichier->nom_fichier }}">
                                                <i class="fas fa-trash-alt mr-1"></i> 
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Téléverser un nouveau Fichier --}}
<div class="modal fade" id="createFileModal" tabindex="-1" aria-labelledby="createFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="createFileModalLabel">Téléverser un nouveau Fichier</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="createFileForm" action="{{ route('fichiers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label for="nom_fichier_create" class="block text-sm font-medium text-gray-700 mb-1">Nom du Fichier</label>
                        <input type="text" id="nom_fichier_create" name="nom_fichier" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="description_create" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description_create" name="description" rows="3"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="fichier_create" class="block text-sm font-medium text-gray-700 mb-1">Fichier à téléverser</label>
                        <input type="file" id="fichier_create" name="fichier" required
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition duration-300 ease-in-out shadow-sm">
                        <p class="mt-1 text-xs text-gray-500">Max 10 Mo. Formats acceptés: pdf, doc, docx, jpg, jpeg, png, etc.</p>
                    </div>
                    <div>
                        <label for="type_fichier_create" class="block text-sm font-medium text-gray-700 mb-1">Type de Fichier</label>
                        <select id="type_fichier_create" name="type_fichier" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            <option value="convention">Convention</option>
                            <option value="rapport">Rapport</option>
                            <option value="attestation">Attestation</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="sujet_id_create" class="block text-sm font-medium text-gray-700 mb-1">Sujet (optionnel)</label>
                        <select id="sujet_id_create" name="sujet_id"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                            <option value="">-- Aucun Sujet --</option>
                            @foreach($sujets as $sujet)
                                <option value="{{ $sujet->id }}">{{ $sujet->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Pour les stagiaires, id_stagiaire est l'utilisateur connecté, pas de choix --}}
                    <input type="hidden" name="id_stagiaire" value="{{ Auth::id() }}">

                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-150 ease-in-out">Téléverser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Modifier un Fichier --}}
<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="editFileModalLabel">Modifier le Fichier</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="editFileForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="nom_fichier_edit" class="block text-sm font-medium text-gray-700 mb-1">Nom du Fichier</label>
                        <input type="text" id="nom_fichier_edit" name="nom_fichier" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="description_edit" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description_edit" name="description" rows="3"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="fichier_edit" class="block text-sm font-medium text-gray-700 mb-1">Remplacer le fichier (optionnel)</label>
                        <input type="file" id="fichier_edit" name="fichier"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-300 ease-in-out shadow-sm">
                        <p class="mt-1 text-xs text-gray-500">Max 10 Mo. Laissez vide pour garder le fichier actuel.</p>
                    </div>
                    <div>
                        <label for="type_fichier_edit" class="block text-sm font-medium text-gray-700 mb-1">Type de Fichier</label>
                        <select id="type_fichier_edit" name="type_fichier" required
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            <option value="convention">Convention</option>
                            <option value="rapport">Rapport</option>
                            <option value="attestation">Attestation</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div>
                        <label for="sujet_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Sujet (optionnel)</label>
                        <select id="sujet_id_edit" name="sujet_id"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            <option value="">-- Aucun Sujet --</option>
                            @foreach($sujets as $sujet)
                                <option value="{{ $sujet->id }}">{{ $sujet->titre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Les stagiaires ne gèrent pas les permissions eux-mêmes --}}
                    {{-- Ces champs seraient ici si le SUPERVISEUR/ADMIN modifiait via la même modale,
                         mais comme cette vue est pour le stagiaire, ils sont omis. --}}

                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteFileModal" tabindex="-1" aria-labelledby="deleteFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="deleteFileModalLabel">Confirmer la Suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir supprimer le fichier : <span id="fileNameToDelete" class="font-semibold text-red-600"></span> ?</p>
                <p class="text-red-500 text-sm font-semibold">Cette action est irréversible et supprimera le fichier définitivement.</p>
                <form id="deleteFileForm" method="POST" class="flex justify-end space-x-4 mt-8">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('my_js')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer le modal "Modifier le Fichier"
        $('#editFileModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // Bouton qui a déclenché le modal
            const fileId = button.data('id');
            const form = $('#editFileForm');

            // Réinitialiser les erreurs de validation précédentes
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.text-red-500.text-sm.mt-1').remove();
            
            // Remplir les champs du formulaire avec les données du fichier
            form.attr('action', `/fichiers/${fileId}`); // Mise à jour de l'action du formulaire

            $('#nom_fichier_edit').val(button.data('nom_fichier'));
            $('#description_edit').val(button.data('description'));
            $('#type_fichier_edit').val(button.data('type_fichier'));
            $('#sujet_id_edit').val(button.data('sujet_id'));
            
            // Les stagiaires ne gèrent pas ces permissions, donc ne les pré-remplissez pas ici.
            // La visibilité des boutons Modifier/Supprimer est gérée directement dans le Blade par les conditions.
        });

        // Gérer le modal "Téléverser un nouveau Fichier"
        $('#createFileModal').on('show.bs.modal', function(event) {
            const form = $('#createFileForm');
            form[0].reset(); // Réinitialiser le formulaire
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.text-red-500.text-sm.mt-1').remove();
            // Pas de pré-sélection de stagiaire ni de checkboxes de permission pour la modale d'ajout stagiaire
        });

        // Gérer le modal "Supprimer le Fichier"
        $('#deleteFileModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // Bouton qui a déclenché le modal
            const fileId = button.data('file-id');
            const fileName = button.data('file-name');

            $('#fileNameToDelete').text(fileName); // Affiche le nom du fichier à supprimer
            const deleteForm = $('#deleteFileForm');
            deleteForm.attr('action', `/fichiers/${fileId}`); // Définit l'action du formulaire de suppression
        });
        
        // Gestion des erreurs de validation après soumission (si la page se recharge avec des erreurs)
        @if ($errors->any())
            var openEditModal = false;
            var openCreateModal = false;

            // Check if errors are from edit form (heuristic: presence of _method=PUT and old('id'))
            @if(old('_method') === 'PUT' && old('id'))
                openEditModal = true;
            @endif

            // Check if errors are from create form (heuristic: absence of _method=PUT and presence of typical create fields)
            // For stagiaire, id_stagiaire is hidden, so checking for other fields is more reliable.
            @if(old('_method') !== 'PUT' && (old('nom_fichier') || old('fichier'))) 
                openCreateModal = true;
            @endif

            if (openEditModal) {
                var editFileModal = new bootstrap.Modal(document.getElementById('editFileModal'));
                editFileModal.show();

                const form = $('#editFileForm');
                const fileId = "{{ old('id') }}";
                
                if(fileId) {
                    form.attr('action', `/fichiers/${fileId}`);
                }
                
                $('#nom_fichier_edit').val("{{ old('nom_fichier') }}");
                $('#description_edit').val("{{ old('description') }}");
                $('#type_fichier_edit').val("{{ old('type_fichier') }}");
                $('#sujet_id_edit').val("{{ old('sujet_id') }}");

            } else if (openCreateModal) {
                var createFileModal = new bootstrap.Modal(document.getElementById('createFileModal'));
                createFileModal.show();

                const form = $('#createFileForm');
                $('#nom_fichier_create').val("{{ old('nom_fichier') }}");
                $('#description_create').val("{{ old('description') }}");
                $('#type_fichier_create').val("{{ old('type_fichier') }}");
                $('#sujet_id_create').val("{{ old('sujet_id') }}");
            }

            // Afficher les erreurs de validation spécifiques aux champs du formulaire des modales
            @foreach ($errors->messages() as $field => $messages)
                let inputEdit = document.getElementById('{{ $field }}_edit');
                let inputCreate = document.getElementById('{{ $field }}_create');

                if (inputEdit && openEditModal) {
                    inputEdit.classList.add('is-invalid');
                    let feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1');
                    feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                    inputEdit.parentNode.appendChild(feedbackDiv);
                } else if (inputCreate && openCreateModal) {
                    inputCreate.classList.add('is-invalid');
                    let feedbackDiv = document.createElement('div');
                    feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1');
                    feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                    inputCreate.parentNode.appendChild(feedbackDiv);
                }
            @endforeach
        @endif
    });
</script>
@endsection
