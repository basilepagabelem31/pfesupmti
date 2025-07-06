@extends('layout.default')

@section('title', 'Détails du Stagiaire : ' . $user->prenom . ' ' . $user->nom)

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">

    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">

        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Élément décoratif en haut --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Détails du <span class="text-blue-600">Stagiaire</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Informations complètes sur {{ $user->prenom }} {{ $user->nom }}
            </p>

            {{-- Bouton Retour --}}
            <div class="mb-8 text-center">
                <a href="{{ route('admin.users.stagiaires') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-base font-semibold rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Retour à la liste des stagiaires
                </a>
            </div>

            {{-- Section Informations Personnelles --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-3 text-blue-500"></i> Informations Personnelles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-lg">
                    <!-- <div><strong>ID :</strong> {{ $user->id }}</div> -->
                    <div><strong>Nom Complet :</strong> {{ $user->prenom }} {{ $user->nom }}</div>
                    <div><strong>Email :</strong> {{ $user->email }}</div>
                    <div><strong>Téléphone :</strong> {{ $user->telephone ?? 'Non renseigné' }}</div>
                    <div><strong>CIN :</strong> {{ $user->cin ?? 'Non renseigné' }}</div>
                    <div><strong>Code Stagiaire :</strong> {{ $user->code ?? 'Non renseigné' }}</div>
                    <div><strong>Adresse :</strong> {{ $user->adresse ?? 'Non renseignée' }}</div>
                    <div><strong>Ville :</strong> {{ $user->ville->nom ?? 'Non renseignée' }}</div>
                    <div><strong>Pays :</strong> {{ $user->pays->nom ?? 'Non renseigné' }}</div>
                    <div><strong>Rôle :</strong> {{ $user->role->nom ?? 'Non renseigné' }}</div>
                    <div><strong>Statut :</strong>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($user->statut?->nom == 'Actif') bg-blue-100 text-blue-800
                            @elseif ($user->statut?->nom == 'Terminé') bg-indigo-100 text-indigo-800
                            @elseif($user->statut?->nom == 'Abandonné') bg-red-100 text-red-800
                            @elseif ($user->statut?->nom == 'Archivé') bg-gray-200 text-gray-700
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $user->statut->nom ?? 'Non renseigné' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Section Informations de Formation --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap mr-3 text-purple-600"></i> Informations de Formation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 text-lg">
                    <div><strong>Université :</strong> {{ $user->universite ?? 'Non renseignée' }}</div>
                    <div><strong>Faculté :</strong> {{ $user->faculte ?? 'Non renseignée' }}</div>
                    <div><strong>Titre de Formation :</strong> {{ $user->titre_formation ?? 'Non renseigné' }}</div>
                    <div><strong>Promotion :</strong> {{ $user->promotion->titre ?? 'Non renseignée' }}</div>
                    <div><strong>Groupe :</strong> {{ $user->groupe->nom ?? 'Non renseigné' }}</div>
                </div>
            </div>

            {{-- Section Sujets --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-book-open mr-3 text-orange-500"></i> Sujets de Stage
                </h3>
                @if ($user->sujets->isEmpty())
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Aucun sujet assigné pour le moment.
                    </div>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($user->sujets as $sujet)
                            <span class="inline-block bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full font-semibold">
                                {{ $sujet->titre }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Section Coéquipiers --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-users mr-3 text-green-600"></i> Coéquipiers 
                </h3>
                @if ($coequipiers->isEmpty())
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Ce stagiaire n'a pas de coéquipiers dans son groupe ou n'appartient pas à un groupe.
                    </div>
                @else
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        @foreach ($coequipiers as $coequipier)
                            <li>
                                <a href="{{ route('admin.stagiaires.show', $coequipier->id) }}" class="text-blue-600 hover:underline">
                                    {{ $coequipier->prenom }} {{ $coequipier->nom }} ({{ $coequipier->email }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Section Notes --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-list mr-3 text-rose-500"></i> Notes du Stagiaire
                </h3>
                @if ($user->notes->isEmpty())
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Aucune note pour ce stagiaire pour le moment.
                    </div>
                @else
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        @foreach ($user->notes as $note)
                            <li class="border-b border-gray-100 pb-2 last:border-b-0">
                                {{-- Affiche le contenu de la note, ou un message si vide --}}
                                "{{ $note->valeur ?: 'Contenu de la note non spécifié.' }}"
                                <small class="text-gray-500 ml-2">({{ $note->created_at->format('d/m/Y') }})</small>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Section Fichiers Téléversés --}}
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200 mb-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-folder-open mr-3 text-yellow-500"></i> Fichiers Téléversés
                </h3>
                @if ($user->fichiersPossedes->isEmpty() && $user->fichiersTeleverses->isEmpty())
                    <div class="bg-blue-50 text-blue-800 p-4 rounded-xl text-center shadow-sm border border-blue-200">
                        <i class="fas fa-info-circle mr-2"></i> Aucun fichier lié à ce stagiaire.
                    </div>
                @else
                    <div class="space-y-4">
                        @if (!$user->fichiersPossedes->isEmpty())
                            <h4 class="text-lg font-semibold text-gray-700">Fichiers possédés (téléversés par le stagiaire ou qui lui sont attribués) :</h4>
                            <ul class="list-disc list-inside text-gray-700 space-y-2">
                                @foreach ($user->fichiersPossedes as $fichier)
                                    <li class="border-b border-gray-100 pb-2 last:border-b-0">
                                        {{-- Utilise la route de téléchargement pour le fichier --}}
                                        <a href="{{ route('fichiers.download', $fichier->id) }}" class="text-blue-600 hover:underline inline-flex items-center">
                                            <i class="fas fa-file-alt mr-2 text-blue-400"></i> {{ $fichier->nom_fichier }} ({{ ucfirst($fichier->type_fichier) }})
                                        </a>
                                        @if ($fichier->id_superviseur_televerseur != $user->id)
                                            <small class="text-gray-500 ml-2">par {{ $fichier->televerseur->prenom ?? 'N/A' }} {{ $fichier->televerseur->nom ?? '' }} ({{ $fichier->created_at->format('d/m/Y') }})</small>
                                        @else
                                            <small class="text-gray-500 ml-2">({{ $fichier->created_at->format('d/m/Y') }})</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Si fichiersTeleverses représente des fichiers téléversés PAR ce user (s'il est superviseur ou admin) --}}
                        @if (!$user->fichiersTeleverses->isEmpty() && ($user->isSuperviseur() || $user->isAdministrateur()))
                            <h4 class="text-lg font-semibold text-gray-700 mt-4">Fichiers téléversés par cet utilisateur (en tant que téléverseur) :</h4>
                            <ul class="list-disc list-inside text-gray-700 space-y-2">
                                @foreach ($user->fichiersTeleverses as $fichier)
                                    <li class="border-b border-gray-100 pb-2 last:border-b-0">
                                        {{-- Utilise la route de téléchargement pour le fichier --}}
                                        <a href="{{ route('fichiers.download', $fichier->id) }}" class="text-blue-600 hover:underline inline-flex items-center">
                                            <i class="fas fa-file-alt mr-2 text-blue-400"></i> {{ $fichier->nom_fichier }} ({{ ucfirst($fichier->type_fichier) }})
                                        </a>
                                        <small class="text-gray-500 ml-2">pour {{ $fichier->stagiaire->prenom ?? 'N/A' }} {{ $fichier->stagiaire->nom ?? '' }} ({{ $fichier->created_at->format('d/m/Y') }})</small>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>


            {{-- Bouton Retour en bas --}}
            <div class="mt-8 text-center">
                <a href="{{ route('admin.users.stagiaires') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-base font-semibold rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Retour à la liste des stagiaires
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
