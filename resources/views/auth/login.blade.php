<x-guest-layout>
    {{-- Conteneur principal avec arrière-plan dégradé et centrage --}}
    <div class="max-h-screen flex items-center justify-center bg-gradient-to-br from-teal-500 to-blue-600 p-4 sm:p-6 lg:p-8 font-inter">
        {{-- Carte de connexion avec fond semi-transparent, bords arrondis et ombre --}}
        {{-- Largeur ajustée de max-w-md à max-w-lg pour une carte plus large --}}
        <div class="w-full max-w-lg bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-10 lg:p-12 border border-gray-100 animate-fade-in">
            
            {{-- Section d'en-tête de la carte (titre et sous-titre) --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-2 leading-tight">
                    Bienvenue
                </h1>
                <p class="text-gray-600 text-lg sm:text-xl">
                    Connectez-vous à votre compte
                </p>
                {{-- Vous pouvez insérer un logo ici si vous en avez un --}}
                {{-- <img src="/path/to/your/logo.png" alt="Votre Logo" class="mx-auto h-20 w-auto mt-6 mb-4"> --}}
            </div>

            {{-- Affichage du statut de la session (ex: "Email envoyé") --}}
            <x-auth-session-status class="mb-6 text-center text-sm font-medium text-green-600" :status="session('status')" />

            {{-- Formulaire de connexion --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Champ Email --}}
                <div>
                    <x-input-label for="email" value="{{ __('Email') }}" class="text-gray-700 text-base font-semibold mb-2" />
                    <x-text-input 
                        id="email" 
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base 
                               transition duration-150 ease-in-out" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username" 
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                {{-- Champ Mot de passe --}}
                <div class="mt-4">
                    <x-input-label for="password" value="{{ __('Mot de passe') }}" class="text-gray-700 text-base font-semibold mb-2" />
                    <x-text-input 
                        id="password" 
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 
                               focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-base 
                               transition duration-150 ease-in-out"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password" 
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                {{-- Section "Se souvenir de moi" et "Mot de passe oublié" --}}
                <div class="flex flex-col sm:flex-row items-center justify-between mt-4 space-y-2 sm:space-y-0">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 h-4 w-4" 
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-gray-700">{{ __('Se souvenir de moi') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <!-- <a class="underline text-sm text-blue-600 hover:text-blue-800 rounded-md 
                                  focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 
                                  transition duration-150 ease-in-out" 
                           href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié ?') }}
                        </a> -->
                    @endif
                </div>

                {{-- Bouton de connexion --}}
                <div class="flex items-center justify-center mt-6">
                    <x-primary-button class="w-full justify-center py-3 px-4 rounded-lg text-lg font-semibold 
                                           text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 
                                           focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out 
                                           uppercase tracking-wider">
                        {{ __('Se connecter') }}
                    </x-primary-button>
                </div>

                {{-- Le lien pour l'inscription a été supprimé --}}
            </form>
        </div>
    </div>
</x-guest-layout>

{{-- Styles et animations supplémentaires (si non gérés globalement par Tailwind CSS) --}}
<style>
    /* Optionnel: Ajoutez ceci à votre fichier CSS principal si 'animate-fade-in' n'est pas déjà défini */
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }

    /* Optionnel: Si la police Inter n'est pas déjà chargée via votre configuration Tailwind ou globalement */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    .font-inter {
        font-family: 'Inter', sans-serif;
    }
</style>
