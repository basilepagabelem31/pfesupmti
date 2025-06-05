@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Bienvenue, {{ Auth::user()->prenom }} !</h1>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Carte pour le Profil --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Mon Profil</h2>
                <p class="text-gray-600 mb-4">Consultez et mettez à jour vos informations personnelles.</p>
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Gérer le Profil
                </a>
            </div>
        </div>

        {{-- Carte pour les Demandes de Coéquipiers --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Demandes de Coéquipiers</h2>
                <p class="text-gray-600 mb-4">Gérez vos demandes envoyées et reçues pour des coéquipiers.</p>
                <a href="{{ route('demande_coequipiers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Voir les demandes
                </a>
            </div>
        </div>

        {{-- Carte pour Mes Coéquipiers --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Mes Coéquipiers</h2>
                @php
                    $coequipiers = Auth::user()->getAllCoequipiers();
                @endphp

                @if ($coequipiers->isEmpty())
                    <p class="text-gray-600 mb-4">Vous n'avez pas encore de coéquipier.</p>
                @else
                    <p class="text-gray-600 mb-4">Voici vos coéquipiers :</p>
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach ($coequipiers as $coequipier)
                            <li>
                                {{ $coequipier->nom }} {{ $coequipier->prenom }}
                                ({{ $coequipier->email }})
                                @if ($coequipier->coequipier_data)
                                    {{-- Assurez-vous que 'date_association' est bien le nom de la colonne pivot --}}
                                    - Depuis le {{ \Carbon\Carbon::parse($coequipier->coequipier_data->date_association)->format('d/m/Y') }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Ajoutez d'autres cartes ou liens pertinents pour un stagiaire ici --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Mes Notes</h2>
                <p class="text-gray-600 mb-4">Consultez les notes et évaluations de vos superviseurs.</p>
                <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Voir les notes
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-700 mb-3">Mes Fichiers</h2>
                <p class="text-gray-600 mb-4">Gérez vos fichiers (convention de stage, rapports, etc.).</p>
                {{-- *************************************************************** --}}
                {{-- MISE À JOUR ICI : Le lien vers la page de gestion des fichiers --}}
                <a href="{{ route('fichiers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Gérer les fichiers
                </a>
                {{-- *************************************************************** --}}
            </div>
        </div>
    </div>
</div>
@endsection