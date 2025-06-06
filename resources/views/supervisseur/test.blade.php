@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Bienvenue, Superviseur {{ Auth::user()->prenom }} !</h1>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        {{-- Carte pour la gestion des Stagiaires --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Stagiaires</h2>
                </div>
                <p class="text-gray-600 mb-4">Affichez, gérez et importez les profils de tous les stagiaires.</p>
                <div class="flex justify-end space-x-2">
                    <a href="/superviseur/stagiaires" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-list-alt mr-2"></i> Voir les stagiaires
                    </a>
                    <a href="{{ route('stagiaires.import.form') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-file-import mr-2"></i> Importer
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour la gestion des Promotions --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-purple-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Promotions</h2>
                </div>
                <p class="text-gray-600 mb-4">Créez et organisez les différentes promotions de stagiaires.</p>
                <div class="flex justify-end">
                    <a href="{{ route('promotions.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-eye mr-2"></i> Gérer les promotions
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour la gestion des Sujets --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-green-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-book-open fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Sujets</h2>
                </div>
                <p class="text-gray-600 mb-4">Définissez et associez des sujets aux stagiaires et promotions.</p>
                <div class="flex justify-end">
                    <a href="{{ route('sujets.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-tasks mr-2"></i> Gérer les sujets
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour la gestion des Groupes --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-red-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Groupes</h2>
                </div>
                <p class="text-gray-600 mb-4">Créez et gérez les groupes de stagiaires avec leurs horaires.</p>
                <div class="flex justify-end">
                    <a href="{{ route('groupes.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-object-group mr-2"></i> Gérer les groupes
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour la gestion des Fichiers --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-yellow-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-folder-open fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Fichiers</h2>
                </div>
                <p class="text-gray-600 mb-4">Téléversez des attestations et gérez les fichiers des stagiaires.</p>
                <div class="flex justify-end">
                    <a href="{{ route('fichiers.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-file-upload mr-2"></i> Gérer les fichiers
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour les Demandes de Coéquipiers --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-orange-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-handshake fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Demandes de Coéquipiers</h2>
                </div>
                <p class="text-gray-600 mb-4">Consultez et gérez les demandes de coéquipiers des stagiaires.</p>
                <div class="flex justify-end">
                    <a href="{{ route('demande_coequipiers.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-bell mr-2"></i> Voir les demandes
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour la gestion des Réunions & Absences (Placeholder) --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-teal-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-teal-100 text-teal-600">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Réunions & Absences</h2>
                </div>
                <p class="text-gray-600 mb-4">Planifiez les réunions et suivez la présence des stagiaires.</p>
                <div class="flex justify-end">
                    <a href="/superviseur/reunions" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-clipboard-list mr-2"></i> Gérer
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte pour l'aperçu des Notes (Placeholder) --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-gray-100 transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-gray-100 text-blue-gray-600">
                        <i class="fas fa-clipboard fa-2x"></i>
                    </div>
                    <h2 class="text-2xl font-semibold text-gray-800 ml-4">Gestion des Notes</h2>
                </div>
                <p class="text-gray-600 mb-4">Ajoutez et consultez les notes attribuées aux stagiaires.</p>
                <div class="flex justify-end">
                    <a href="/superviseur/notes" class="inline-flex items-center px-4 py-2 bg-blue-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i> Gérer les notes
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
