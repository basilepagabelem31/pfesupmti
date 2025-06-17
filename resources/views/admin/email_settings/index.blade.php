@extends('layout.default')

@section('title', 'Paramètres Email')

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8"> {{-- Ajout d'un fond léger pour la page --}}
    <div class="max-w-4xl mx-auto"> {{-- Conteneur plus compact pour le contenu principal --}}
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">
            <i class="fas fa-envelope-open-text mr-3 text-indigo-500"></i> Paramètres Email {{-- Icône et titre plus impactant --}}
        </h1>

        {{-- Messages de succès --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 flex items-center shadow-sm">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <div>
                    <h3 class="font-semibold text-lg">Succès !</h3>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Messages d'erreur --}}
        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 shadow-sm">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3 mt-1"></i>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Oups ! Il y a des erreurs :</h3>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-8 transform transition-all duration-300 hover:shadow-xl"> {{-- Carte principale avec plus de style --}}
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">Informations Actuelles</h2> {{-- Sous-titre pour la section d'affichage --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6"> {{-- Grille pour mieux organiser les informations --}}
                @foreach([
                    'protocole' => 'Protocole',
                    'host' => 'Host',
                    'port' => 'Port',
                    'username' => 'Username',
                    'password' => 'Mot de passe',
                    'from_address' => 'Email expéditeur',
                    'from_name' => 'Nom expéditeur',
                    'encryption' => 'Encryption'
                ] as $key => $label)
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p> {{-- Libellé en majuscules et plus discret --}}
                        <p class="mt-1 text-gray-800 text-lg font-semibold">{{ $settings->$key ?? 'Non renseigné' }}</p> {{-- Valeur en gras et plus grande --}}
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-6 border-t flex justify-end"> {{-- Bouton à droite avec une bordure supérieure --}}
                <button
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105"
                    onclick="document.getElementById('editModal').showModal()"
                >
                    <i class="fas fa-edit mr-2"></i> {{ $settings ? 'Modifier' : 'Créer' }} les paramètres
                </button>
            </div>
        </div>
    </div>
</div>

<dialog id="editModal" class="rounded-3xl shadow-2xl w-full max-w-2xl p-0 backdrop:bg-gray-900/70 backdrop:backdrop-blur-sm"> {{-- Modal plus grand, coins arrondis, et fond stylé --}}
    <form method="POST" action="{{ route('admin.email-settings.update') }}" class="bg-white rounded-3xl overflow-hidden">
        @csrf
        <div class="px-8 py-5 border-b border-gray-200 flex justify-between items-center bg-indigo-50"> {{-- En-tête du modal plus attrayant --}}
            <h2 class="text-2xl font-extrabold text-gray-800">
                <i class="fas fa-cogs mr-3 text-indigo-600"></i> {{ $settings ? 'Modifier' : 'Créer' }} les paramètres email
            </h2>
            <button type="button" class="text-gray-500 hover:text-gray-700 text-3xl leading-none" onclick="document.getElementById('editModal').close()">
                &times; {{-- Icône de fermeture X --}}
            </button>
        </div>
        <div class="p-8 space-y-6"> {{-- Plus d'espacement dans le formulaire --}}
            @foreach([
                'protocole' => ['label' => 'Protocole', 'type' => 'text'],
                'host' => ['label' => 'Host', 'type' => 'text'],
                'port' => ['label' => 'Port', 'type' => 'number'],
                'username' => ['label' => 'Username', 'type' => 'text'],
                'encryption' => ['label' => 'Encryption', 'type' => 'text'],
                'from_address' => ['label' => 'Email expéditeur', 'type' => 'email'],
                'from_name' => ['label' => 'Nom expéditeur', 'type' => 'text']
            ] as $name => $field)
                <div>
                    <label for="{{ $name }}" class="block text-sm font-semibold text-gray-700 mb-1">{{ $field['label'] }}</label> {{-- Libellé en gras --}}
                    <input type="{{ $field['type'] }}"
                        id="{{ $name }}"
                        name="{{ $name }}"
                        value="{{ old($name, $settings->$name ?? '') }}"
                        @if($field['type'] !== 'email') required @endif {{-- email n'est pas toujours requis --}}
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-indigo-400" {{-- Styles de champ améliorés --}}
                        placeholder="Entrez le {{ strtolower($field['label']) }}"
                    >
                    @error($name)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-3 text-gray-800 placeholder-gray-400 focus:outline-none transition-all duration-200 ease-in-out hover:border-indigo-400"
                    {{ !$settings ? 'required' : '' }}
                    placeholder="Laisse vide pour ne pas modifier"
                >
                @if($settings)
                    <small class="mt-1 text-gray-500 text-sm italic">Laissez vide pour conserver le mot de passe actuel.</small>
                @endif
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex justify-end space-x-4"> {{-- Pied de page du modal stylé --}}
            <button type="button" class="px-6 py-3 text-gray-700 hover:text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200" onclick="document.getElementById('editModal').close()">
                Annuler
            </button>
            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150 transform hover:scale-105">
                <i class="fas fa-save mr-2"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</dialog>
@endsection