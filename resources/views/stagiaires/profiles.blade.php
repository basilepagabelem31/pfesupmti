@extends('layout.default')

@section('title', 'Mon Profil')

@section('content')
{{-- Outer container for the entire page, providing minimal padding and ensuring content is centered --}}
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    {{-- Main content container for the profile page (the white card) --}}
    {{-- Added mx-auto for explicit horizontal centering and adjusted max-width for better responsiveness and sidebar compatibility --}}
    <div class="w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in
                max-w-xl md:max-w-3xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl mx-auto">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-8 mt-4 leading-tight">
                Mon <span class="text-blue-600">Profil</span>
            </h2>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez et mettez à jour vos informations personnelles, notes et fichiers.
            </p>

            {{-- Session messages (success/global errors) --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200 animate-fade-in" role="alert">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
                </div>
            @endif
            @if ($errors->any() && !old('modal_open')) {{-- Display general errors only if modal wasn't opened from failed validation --}}
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 shadow-md border border-red-200 animate-fade-in" role="alert">
                    <p class="font-bold mb-3 flex items-center space-x-2 text-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                        <span>Erreurs de validation :</span>
                    </p>
                    <ul class="list-disc list-inside text-base mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Section Informations Personnelles --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-blue-500"></i> Informations Personnelles Actuelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-lg">
                    <div><strong>Nom :</strong> {{ $user->nom }}</div>
                    <div><strong>Prénom :</strong> {{ $user->prenom }}</div>
                    <div><strong>Email :</strong> {{ $user->email }}</div>
                    <div><strong>Téléphone :</strong> {{ $user->telephone ?? 'Non renseigné' }}</div>
                    <div><strong>CIN :</strong> {{ $user->cin ?? 'Non renseigné' }}</div>
                    <div><strong>Adresse :</strong> {{ $user->adresse ?? 'Non renseigné' }}</div>
                    <div><strong>Ville :</strong> {{ $user->ville->nom ?? 'Non renseigné' }}</div>
                    <div><strong>Pays :</strong> {{ $user->pays->nom ?? 'Non renseigné' }}</div>
                    <div><strong>Université :</strong> {{ $user->universite ?? 'Non renseignée' }}</div>
                    <div><strong>Faculté :</strong> {{ $user->faculte ?? 'Non renseignée' }}</div>
                    <div><strong>Titre de formation :</strong> {{ $user->titre_formation ?? 'Non renseigné' }}</div>
                </div>
                <div class="flex justify-center mt-8">
                    <button id="openEditProfileModal" type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider">
                        <i class="fas fa-edit mr-2"></i> Modifier mon Profil
                    </button>
                </div>
            </div>

            {{-- Section : Mes Notes --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-scroll mr-3 text-rose-500"></i> Mes Notes
                </h3>
                @forelse($user->notes as $note)
                    <div class="border-b border-gray-100 py-3 last:border-b-0">
                        <p class="text-gray-700 text-base">
                            {{ $note->valeur }}
                            <small class="text-gray-500 ml-2">({{ $note->created_at->format('d/m/Y') }})</small>
                            <span class="ml-3 px-2 py-0.5 text-xs font-semibold rounded-full
                                @if($note->visibilite == 'all') bg-green-100 text-green-800
                                @elseif($note->visibilite == 'donneur + stagiaire') bg-blue-100 text-blue-800
                                @elseif($note->visibilite == 'donneur') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('-', ' ', str_replace('+', ' + ', $note->visibilite))) }}
                            </span>
                        </p>
                    </div>
                @empty
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Aucune note pour le moment.
                    </div>
                @endforelse
            </div>

            {{-- Section : Mes Fichiers --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-file-alt mr-3 text-yellow-500"></i> Mes Fichiers
                </h3>
                @forelse($user->fichiersPossedes as $fichier)
                    <div class="border-b border-gray-100 py-3 last:border-b-0">
                        <p class="text-gray-700 text-base flex justify-between items-center">
                            <span>
                                <a href="{{ asset('storage/' . $fichier->chemin) }}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center">
                                    <i class="fas fa-paperclip mr-2 text-blue-400"></i> {{ $fichier->titre }}
                                </a>
                                <small class="text-gray-500 ml-2">({{ $fichier->created_at->format('d/m/Y') }})</small>
                            </span>
                        </p>
                    </div>
                @empty
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Aucun fichier disponible.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modal for profile modification (This part remains unchanged from previous good version) --}}
<div id="editProfileModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4 hidden transition-opacity duration-300 ease-out opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full relative transform -translate-y-full transition-transform duration-300 ease-out sm:my-8 sm:align-middle sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-screen-minus-margins">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-3xl font-light focus:outline-none transition-colors duration-200" id="closeEditProfileModal">
            &times;
        </button>

        <h3 class="text-3xl font-extrabold text-gray-800 mb-6 flex items-center justify-center text-center">
            <i class="fas fa-user-edit mr-4 text-purple-600"></i> Modifier mon Profil
        </h3>
        <form method="POST" action="{{ route('stagiaires.profiles.update', $user->id) }}" class="space-y-5" id="profileUpdateForm">
            @csrf
            @method('PUT')

            {{-- Hidden field to indicate modal was open on submission --}}
            <input type="hidden" name="modal_open" value="1">

            {{-- Scrollable div for form content --}}
            <div class="overflow-y-auto pr-2" style="max-height: calc(80vh - 200px);">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="modal_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom :</label>
                        <input type="text" name="nom" id="modal_nom" class="form-input @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                        @error('nom') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom :</label>
                        <input type="text" name="prenom" id="modal_prenom" class="form-input @error('prenom') is-invalid @enderror" value="{{ old('prenom') }}" required>
                        @error('prenom') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_email" class="block text-sm font-medium text-gray-700 mb-1">Email :</label>
                        <input type="email" name="email" id="modal_email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone :</label>
                        <input type="text" name="telephone" id="modal_telephone" class="form-input @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}">
                        @error('telephone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_cin" class="block text-sm font-medium text-gray-700 mb-1">CIN :</label>
                        <input type="text" name="cin" id="modal_cin" class="form-input @error('cin') is-invalid @enderror" value="{{ old('cin') }}">
                        @error('cin') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse :</label>
                        <input type="text" name="adresse" id="modal_adresse" class="form-input @error('adresse') is-invalid @enderror" value="{{ old('adresse') }}">
                        @error('adresse') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Pays (Read-only) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays :</label>
                        <span class="block w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg shadow-sm border border-gray-300">
                            {{ $user->pays->nom ?? 'Non renseigné' }}
                        </span>
                    </div>
                    {{-- Ville (Read-only) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville :</label>
                        <span class="block w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg shadow-sm border border-gray-300">
                            {{ $user->ville->nom ?? 'Non renseigné' }}
                        </span>
                    </div>

                    <div>
                        <label for="modal_universite" class="block text-sm font-medium text-gray-700 mb-1">Université :</label>
                        <input type="text" name="universite" id="modal_universite" class="form-input @error('universite') is-invalid @enderror" value="{{ old('universite') }}">
                        @error('universite') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_faculte" class="block text-sm font-medium text-gray-700 mb-1">Faculté :</label>
                        <input type="text" name="faculte" id="modal_faculte" class="form-input @error('faculte') is-invalid @enderror" value="{{ old('faculte') }}">
                        @error('faculte') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="modal_titre_formation" class="block text-sm font-medium text-gray-700 mb-1">Titre de formation :</label>
                        <input type="text" name="titre_formation" id="modal_titre_formation" class="form-input @error('titre_formation') is-invalid @enderror" value="{{ old('titre_formation') }}">
                        @error('titre_formation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-6 border-gray-200">

                <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-lock mr-3 text-pink-600"></i> Modifier mon mot de passe
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <label for="modal_current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel :</label>
                        <input type="password" name="current_password" id="modal_current_password" class="form-input @error('current_password') is-invalid @enderror">
                        @error('current_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label for="modal_new_password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe :</label>
                        <input type="password" name="new_password" id="modal_new_password" class="form-input @error('new_password') is-invalid @enderror">
                        @error('new_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="modal_new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe :</label>
                        <input type="password" name="new_password_confirmation" id="modal_new_password_confirmation" class="form-input @error('new_password_confirmation') is-invalid @enderror">
                        @error('new_password_confirmation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div> {{-- End of scrollable div --}}

            <div class="flex justify-end space-x-4 mt-8 pt-4 border-t border-gray-100">
                <button type="button" class="py-3 px-6 rounded-lg text-lg font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition duration-150 ease-in-out" id="cancelEditProfileModal">Annuler</button>
                <button type="submit" class="py-3 px-6 rounded-lg text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Custom styles and animations (unchanged) --}}
<style>
    /* Styles for form fields (inputs and selects) - Updated for a better look */
    .form-input {
        @apply block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-500
               bg-gray-50 text-gray-800
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
               sm:text-base transition-all duration-200 ease-in-out;
    }

    /* Style for invalid fields (with validation errors) */
    .form-input.is-invalid {
        @apply border-red-500 focus:ring-red-500 focus:border-red-500 bg-red-50;
    }

    /* Subtle animations for section appearance */
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }

    /* Helper class for modal height calculation */
    .max-h-screen-minus-margins {
        /* This calc ensures the modal fits within the screen height with some margin */
        max-height: calc(100vh - 4rem); /* 4rem = 2rem top + 2rem bottom padding */
    }
</style>

@section('my_js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editProfileModal = document.getElementById('editProfileModal');
        const modalContent = editProfileModal.querySelector('div');
        const openEditProfileModalBtn = document.getElementById('openEditProfileModal');
        const closeEditProfileModalBtn = document.getElementById('closeEditProfileModal');
        const cancelEditProfileModalBtn = document.getElementById('cancelEditProfileModal');
        const profileUpdateForm = document.getElementById('profileUpdateForm');

        // Function to populate form fields in the modal
        function populateModalForm(data) {
            document.getElementById('modal_nom').value = data.nom;
            document.getElementById('modal_prenom').value = data.prenom;
            document.getElementById('modal_email').value = data.email;
            document.getElementById('modal_telephone').value = data.telephone;
            document.getElementById('modal_cin').value = data.cin;
            document.getElementById('modal_adresse').value = data.adresse;
            document.getElementById('modal_universite').value = data.universite;
            document.getElementById('modal_faculte').value = data.faculte;
            document.getElementById('modal_titre_formation').value = data.titre_formation;

            // Clear password fields for security reasons
            document.getElementById('modal_current_password').value = '';
            document.getElementById('modal_new_password').value = '';
            document.getElementById('modal_new_password_confirmation').value = '';

            // Clear previous validation error styles and hide error messages
            document.querySelectorAll('.form-input').forEach(input => {
                input.classList.remove('is-invalid');
            });
            document.querySelectorAll('div.text-red-500.text-xs.mt-1').forEach(errorDiv => {
                errorDiv.style.display = 'none'; // Hide the error message div
            });
        }

        // Function to open the modal
        function openModal() {
            // Use old() to pre-fill if a validation error occurred,
            // otherwise use the user's initial data.
            const dataToPopulate = {
                nom: "{{ old('nom', $user->nom) }}",
                prenom: "{{ old('prenom', $user->prenom) }}",
                email: "{{ old('email', $user->email) }}",
                telephone: "{{ old('telephone', $user->telephone ?? '') }}",
                cin: "{{ old('cin', $user->cin ?? '') }}",
                adresse: "{{ old('adresse', $user->adresse ?? '') }}",
                universite: "{{ old('universite', $user->universite ?? '') }}",
                faculte: "{{ old('faculte', $user->faculte ?? '') }}",
                titre_formation: "{{ old('titre_formation', $user->titre_formation ?? '') }}"
            };
            populateModalForm(dataToPopulate);

            // Apply validation error styles if the modal opens due to a failed validation
            @if($errors->any() && old('modal_open'))
                @foreach ($errors->messages() as $field => $messages)
                    let inputElement = document.getElementById('modal_{{ $field }}');
                    if (inputElement) {
                        inputElement.classList.add('is-invalid');
                        let errorDiv = inputElement.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('text-red-500')) {
                            errorDiv.style.display = 'block'; // Re-display the error message div
                        }
                    }
                @endforeach
            @endif

            editProfileModal.classList.remove('hidden', 'opacity-0');
            editProfileModal.classList.add('flex', 'opacity-100'); // Fades in the overlay
            modalContent.classList.remove('-translate-y-full');
            modalContent.classList.add('translate-y-0'); // Slides in the modal content
        }

        // Function to close the modal
        function closeModal() {
            editProfileModal.classList.remove('opacity-100');
            editProfileModal.classList.add('opacity-0'); // Fades out the overlay
            modalContent.classList.remove('translate-y-0');
            modalContent.classList.add('-translate-y-full'); // Slides the modal content up

            // Wait for the transition to finish before completely hiding
            setTimeout(() => {
                editProfileModal.classList.add('hidden');
            }, 300); // Must match CSS transition duration
        }

        // Event listener to open the modal
        openEditProfileModalBtn.addEventListener('click', openModal);

        // Event listener to close the modal via the X button
        closeEditProfileModalBtn.addEventListener('click', closeModal);

        // Event listener to close the modal via the Cancel button
        cancelEditProfileModalBtn.addEventListener('click', closeModal);

        // Event listener to close the modal by clicking outside its content
        editProfileModal.addEventListener('click', function(event) {
            if (event.target === editProfileModal) {
                closeModal();
            }
        });

        // Event listener to close the modal with the Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !editProfileModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Check if validation errors exist on page load and if submission came from the modal
        // If so, open the modal automatically
        @if($errors->any() && old('modal_open'))
            openModal();
        @endif
    });
</script>
@endsection
