@extends('layout.default')

@section('title', 'Profil Superviseur')

@section('content')
{{-- Conteneur externe pour toute la page, ajoutant un peu de padding et centrant le contenu --}}
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    {{-- Conteneur principal de la page de profil (la carte blanche) --}}
    <div class="w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in
                max-w-xl md:max-w-3xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl mx-auto">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Élément décoratif en haut --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-8 mt-4 leading-tight">
                Profil <span class="text-blue-600">Superviseur</span>
            </h2>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez et mettez à jour vos informations personnelles.
            </p>

            {{-- Messages de session (succès/erreurs globales) --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200 animate-fade-in" role="alert">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
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

            {{-- Section Informations du superviseur --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-blue-500"></i> Informations Personnelles Actuelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-lg">
                    <div><strong>Nom :</strong> {{ Auth::user()->nom }}</div>
                    <div><strong>Prénom :</strong> {{ Auth::user()->prenom }}</div>
                    <div><strong>Email :</strong> {{ Auth::user()->email }}</div>
                    <div><strong>Téléphone :</strong> {{ Auth::user()->telephone ?? 'Non renseigné' }}</div>
                    <div><strong>CIN :</strong> {{ Auth::user()->cin ?? 'Non renseigné' }}</div>
                    <div><strong>Adresse :</strong> {{ Auth::user()->adresse ?? 'Non renseigné' }}</div>
                    <div><strong>Ville :</strong> {{ Auth::user()->ville->nom ?? 'Non renseigné' }}</div>
                    <div><strong>Pays :</strong> {{ Auth::user()->pays->nom ?? 'Non renseigné' }}</div>
                </div>
            </div>

            {{-- Formulaire de mise à jour du profil --}}
            {{-- La condition @if(Auth::user()->id == $user->id) a été retirée si cette vue est toujours pour l'utilisateur connecté --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-user-edit mr-3 text-purple-600"></i> Modifier mon Profil
                </h3>
                <form id="form_edit_profile" action="{{ route('superviseur.profile.update', $user->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom :</label>
                            <input type="text" name="nom" id="nom" class="form-input @error('nom') is-invalid @enderror" value="{{ old('nom', $user->nom) }}" required>
                            @error('nom') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">Prénom :</label>
                            <input type="text" name="prenom" id="prenom" class="form-input @error('prenom') is-invalid @enderror" value="{{ old('prenom', $user->prenom) }}" required>
                            @error('prenom') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email :</label>
                            <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone :</label>
                            <input type="text" name="telephone" id="telephone" class="form-input @error('telephone') is-invalid @enderror" value="{{ old('telephone', $user->telephone) }}">
                            @error('telephone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="cin" class="block text-sm font-medium text-gray-700 mb-1">CIN :</label>
                            <input type="text" name="cin" id="cin" class="form-input @error('cin') is-invalid @enderror" value="{{ old('cin', $user->cin) }}" required>
                            @error('cin') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse :</label>
                            <input type="text" name="adresse" id="adresse" class="form-input @error('adresse') is-invalid @enderror" value="{{ old('adresse', $user->adresse) }}">
                            @error('adresse') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        {{-- Pays et Ville en lecture seule --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pays :</label>
                            <span class="block w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg shadow-sm border border-gray-300">
                                {{ $user->pays->nom ?? 'Non renseigné' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville :</label>
                            <span class="block w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg shadow-sm border border-gray-300">
                                {{ $user->ville->nom ?? 'Non renseigné' }}
                            </span>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lock mr-3 text-pink-600"></i> Modifier mon mot de passe
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel :</label>
                            <input type="password" name="current_password" id="current_password" class="form-input @error('current_password') is-invalid @enderror">
                            @error('current_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe :</label>
                            <input type="password" name="new_password" id="new_password" class="form-input @error('new_password') is-invalid @enderror">
                            @error('new_password') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe :</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-input @error('new_password_confirmation') is-invalid @enderror">
                            @error('new_password_confirmation') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if(Session::get('password_changed'))
                        <div class="bg-green-100 text-green-800 p-3 rounded-md mt-4 shadow-sm border border-green-200 animate-fade-in" role="alert">
                            Mot de passe modifié avec succès.
                        </div>
                    @endif

                    <div class="flex justify-center mt-8">
                        <button type="submit" class="py-3 px-6 rounded-lg text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out transform hover:scale-105 active:scale-95">
                            <i class="fas fa-save mr-3"></i> Mettre à jour le profil
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- Styles personnalisés pour les champs de formulaire --}}
<style>
    /* Styles pour les champs de formulaire (inputs et selects) */
    .form-input {
        /* Styles de base pour tous les inputs */
        @apply block w-full px-4 py-2.5 text-base text-gray-800 bg-gray-50 rounded-lg shadow-sm
               border border-gray-300 placeholder-gray-400
               transition-all duration-200 ease-in-out;
        /* Styles au focus (quand le champ est sélectionné) */
        @apply focus:outline-none focus:ring-3 focus:ring-blue-300 focus:border-blue-500;
    }

    /* Style pour les champs invalides (avec erreurs de validation) */
    .form-input.is-invalid {
        @apply border-red-500 ring-1 ring-red-200 focus:ring-red-400 focus:border-red-600 bg-red-50;
    }

    /* Animations subtiles pour l'apparition des sections */
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }
</style>

{{-- Aucun JavaScript spécifique n'est nécessaire pour cette page --}}

@endsection
