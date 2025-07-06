@extends('layout.default')

@section('title', 'Gestion des Stagiaires')

@section('content')

<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">

    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">

        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-blue-600">Stagiaires</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez, ajoutez, modifiez et supprimez les comptes des stagiaires.
            </p>

            {{-- Messages de session --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
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

            {{-- Bouton Ajouter un Stagiaire --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus: outline-none focus:ring-2 focus:ring-offset-2 focus: ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un nouveau Stagiaire
                </button>
            </div>

            {{-- FORMULAIRE DE FILTRE --}}
            <div class="bg-gray-50 p-6 rounded-xl shadow-md mb-8 border border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Filtrer les Stagiaires</h3>
                <form action="{{ route('admin.users.stagiaires') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="filter_nom" class="block text-sm font-medium text-gray-700 mb-1">Code, Nom ou Prénom</label>
                        <input type="text" id="filter_nom" name="nom"
                               value="{{ request('nom') }}"
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus: outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="filter_statut_id" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select id="filter_statut_id" name="statut_id"
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                            <option value="">Tous les statuts</option>
                            @foreach($statuts as $statut)
                                <option value="{{ $statut->id }}" {{ request('statut_id') == $statut->id ? 'selected': '' }}>{{ $statut->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_groupe_id" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                        <select id="filter_groupe_id" name="groupe_id"
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus: outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                            <option value="">Tous les groupes</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}" {{ request('groupe_id') == $groupe->id? 'selected': ''}}>{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filter_promotion_id" class="block text-sm font-medium text-gray-700 mb-1">Promotion</label>
                        <select id="filter_promotion_id" name="promotion_id"
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus: outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                            <option value="">Toutes les promotions</option>
                            @foreach($promotions as $promotion)
                                <option value="{{ $promotion->id }}" {{ request('promotion_id') == $promotion->id? 'selected' : '' }}>{{ $promotion->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2 lg:col-span-4 flex justify-end space-x-3">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-semibold rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus: outline-none focus: ring-2 focus: ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <i class="fas fa-filter mr-2"></i> Appliquer les filtres
                        </button>
                        <a href="{{ route('admin.users.stagiaires') }}"
                           class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-base font-semibold rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <i class="fas fa-undo mr-2"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
            {{-- FIN FORMULAIRE DE FILTRE --}}

            {{-- Tableau des Stagiaires --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200 mb-8">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-50"> {{-- Couleur d'en-tête adaptée aux stagiaires --}}
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Prénom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">CIN</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Adresse</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Pays</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Ville</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Rôle</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Université</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Faculté</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Titre Formation</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Groupe</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Promotion</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Sujets</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Statut</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($admins as $stagiaire)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->prenom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->telephone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->cin }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->adresse }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->pays?->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->ville?->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($stagiaire->role?->nom == 'Stagiaire') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $stagiaire->role?->nom }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->universite }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->faculte }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->titre_formation }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->groupe?->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stagiaire->promotion?->titre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @forelse($stagiaire->sujets as $sujet)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mb-1 mr-1">{{ $sujet->titre }}</span>
                                    @empty
                                        <span class="px-2 bg-danger inline-flex text-light leading-5 font-semibold rounded-full">Non Inscrit</span>
                                    @endforelse
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($stagiaire->statut?->nom == 'Actif') bg-blue-100 text-blue-800
                                        @elseif ($stagiaire->statut?->nom == 'Terminé') bg-indigo-100 text-indigo-800
                                        @elseif($stagiaire->statut?->nom == 'Abandonné') bg-red-100 text-red-800
                                        @elseif ($stagiaire->statut?->nom == 'Archivé') bg-gray-200 text-gray-700
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $stagiaire->statut?->nom }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        {{-- Nouveau bouton Voir Détails --}}
                                        <a href="{{ route('admin.stagiaires.show', $stagiaire->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out"
                                           title="Voir les détails du stagiaire">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus: outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                                            data-id="{{ $stagiaire->id }}"
                                            data-nom="{{ $stagiaire->nom }}"
                                            data-prenom="{{ $stagiaire->prenom }}"
                                            data-email="{{ $stagiaire->email }}"
                                            data-telephone="{{ $stagiaire->telephone }}"
                                            data-cin="{{ $stagiaire->cin }}"
                                            data-code="{{ $stagiaire->code }}"
                                            data-adresse="{{ $stagiaire->adresse }}"
                                            data-pays_id="{{ $stagiaire->pays_id }}"
                                            data-ville_id="{{ $stagiaire->ville_id }}"
                                            data-role_id="{{ $stagiaire->role_id }}"
                                            data-universite="{{ $stagiaire->universite }}"
                                            data-faculte="{{ $stagiaire->faculte }}"
                                            data-titre_formation="{{ $stagiaire->titre_formation }}"
                                            data-statut_id="{{ $stagiaire->statut_id }}"
                                            data-groupe_id="{{ $stagiaire->groupe_id}}"
                                            data-promotion_id="{{ $stagiaire->promotion_id }}"
                                            data-sujet_ids="{{ json_encode($stagiaire->sujets->pluck('id')->toArray()) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                            data-bs-toggle="modal" data-bs-target="#deleteUserModal"
                                            data-user-id="{{ $stagiaire->id }}"
                                            data-user-name="{{ $stagiaire->prenom }} {{ $stagiaire->nom }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                       
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-gray-500" colspan="19">Aucun stagiaire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="mt-8">
                <div class="flex justify-center">
                    {{-- Utilise la pagination par défaut de Bootstrap 5 en lui passant les paramètres de filtre actuels --}}
                    {{ $admins->appends (request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Ajouter un Utilisateur (vos modals existants) --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="addUserModalLabel">Ajouter un nouveau stagiaire</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="addUserForm" action="{{ route('admin.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom_add" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="nom_add" name="nom" value="{{ old('nom') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus: outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="prenom_add" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="prenom_add" name="prenom" value="{{ old('prenom') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus: outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email_add" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email_add" name="email" value="{{ old('email') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus: outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="password_add" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" id="password_add" name="password" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="telephone_add" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" id="telephone_add" name="telephone" value="{{ old('telephone') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cin_add" class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                            <input type="text" id="cin_add" name="cin" value="{{ old('cin') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2"> {{-- Full width for address --}}
                            <label for="adresse_add" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" id="adresse_add" name="adresse" value="{{ old('adresse') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus: ring-blue-500 focus: border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="pays_id_add" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <select id="pays_id_add" name="pays_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus: outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un pays</option>
                                @foreach($pays as $country)
                                    <option value="{{ $country->id }}" {{ old('pays_id') == $country->id? 'selected' : '' }}>{{ $country->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="ville_id_add" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <select id="ville_id_add" name="ville_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus: outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un pays d'abord</option>
                            </select>
                        </div>
                        <div>
                            <label for="role_id_add" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            {{-- Le rôle est fixe à "Stagiaire" sur cette page --}}
                            <select id="role_id_add" name="role_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                                <option value="{{ $stagiaireId }}" selected>Stagiaire</option>
                            </select>
                            {{-- Champ caché pour s'assurer que role_id est envoyé --}}
                            <input type="hidden" name="role_id" value="{{ $stagiaireId }}">
                        </div>
                        <div>
                            <label for="statut_id_add" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select id="statut_id_add" name="statut_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus: border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuts as $statut)
                                    <option value="{{ $statut->id }}" {{ old('statut_id') == $statut->id ? 'selected' : '' }}>{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Champs spécifiques au Stagiaire --}}
                    <div id="stagiaire_fields_add" class="space-y-4 border border-blue-200 rounded-xl p-4 bg-blue-50 mt-6">
                        <p class="text-blue-800 font-semibold">Informations spécifiques au stagiaire :</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="universite_add" class="block text-sm font-medium text-gray-700 mb-1">Université</label>
                                <input type="text" id="universite_add" name="universite" value="{{ old('universite') }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="faculte_add" class="block text-sm font-medium text-gray-700 mb-1">Faculté</label>
                                <input type="text" id="faculte_add" name="faculte" value="{{ old('faculte') }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="titre_formation_add" class="block text-sm font-medium text-gray-700 mb-1">Titre de Formation</label>
                                <input type="text" id="titre_formation_add" name="titre_formation" value="{{ old('titre_formation') }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            {{-- Champ Promotion --}}
                            <div>
                                <label for="promotion_id_add" class="block text-sm font-medium text-gray-700 mb-1">Promotion</label>
                                <select name="promotion_id" id="promotion_id_add"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Sélectionner une promotion</option>
                                    @foreach($promotions as $promo)
                                        <option value="{{ $promo->id }}" {{ old('promotion_id') == $promo->id ? 'selected' : '' }}>{{ $promo->titre }}</option>
                                    @endforeach
                                </select>
                                @error('promotion_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            {{-- Champ Groupe --}}
                            <div>
                                <label for="groupe_id_add" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                                <select name="groupe_id" id="groupe_id_add"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groupes as $groupe)
                                        <option value="{{ $groupe->id }}" {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                                    @endforeach
                                </select>
                                @error('groupe_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            {{-- Champ Sujets (sélection multiple) --}}
                            <!-- <div class="md:col-span-2">
                                <label for="sujet_ids_add" class="block text-sm font-medium text-gray-700 mb-1">Sujets (sélection multiple)</label>
                                <select name="sujet_ids[]" id="sujet_ids_add" multiple
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    style="min-height: 120px;">
                                    @foreach($sujets as $sujet)
                                        <option value="{{ $sujet->id }}"
                                            {{ in_array($sujet->id, old('sujet_ids', [])) ? 'selected' : '' }}>
                                            {{ $sujet->titre }} (Promo: {{ $sujet->promotion->titre ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('sujet_ids') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                                @error('sujet_ids.*') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div> -->
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Modifier un Utilisateur (Unique) --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="editUserModalLabel">Modifier le stagiaire</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="editUserForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_edit" class="block text-sm font-medium text-gray-700 mb-1">ID Utilisateur</label>
                            <input type="text" id="id_edit" name="id" readonly
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">
                        </div>
                        <div>
                            <label for="code_edit" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                            <input type="text" id="code_edit" name="code" readonly
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">
                        </div>
                        <div>
                            <label for="nom_edit" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="nom_edit" name="nom" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="prenom_edit" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="prenom_edit" name="prenom" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email_edit" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email_edit" name="email" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="telephone_edit" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" id="telephone_edit" name="telephone" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cin_edit" class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                            <input type="text" id="cin_edit" name="cin" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="adresse_edit" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" id="adresse_edit" name="adresse" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="pays_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <select id="pays_id_edit" name="pays_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un pays</option>
                                @foreach($pays as $country)
                                    <option value="{{ $country->id }}">{{ $country->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="ville_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <select id="ville_id_edit" name="ville_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un pays d'abord</option>
                            </select>
                        </div>
                        <div>
                            <label for="role_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            {{-- Le rôle est fixe à "Stagiaire" sur cette page --}}
                            <select id="role_id_edit" name="role_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="{{ $stagiaireId }}" selected>Stagiaire</option>
                            </select>
                            <input type="hidden" name="role_id" value="{{ $stagiaireId }}">
                        </div>
                        <div>
                            <label for="statut_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select id="statut_id_edit" name="statut_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuts as $statut)
                                    <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Champs spécifiques au Stagiaire --}}
                    <div id="stagiaire_fields_edit" class="space-y-4 border border-yellow-200 rounded-xl p-4 bg-yellow-50 mt-6">
                        <p class="text-yellow-800 font-semibold">Informations spécifiques au stagiaire :</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="universite_edit" class="block text-sm font-medium text-gray-700 mb-1">Université</label>
                                <input type="text" id="universite_edit" name="universite"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="faculte_edit" class="block text-sm font-medium text-gray-700 mb-1">Faculté</label>
                                <input type="text" id="faculte_edit" name="faculte"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="titre_formation_edit" class="block text-sm font-medium text-gray-700 mb-1">Titre de Formation</label>
                                <input type="text" id="titre_formation_edit" name="titre_formation"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            {{-- Champ Promotion --}}
                            <div>
                                <label for="promotion_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Promotion</label>
                                <select name="promotion_id" id="promotion_id_edit"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                    <option value="">Sélectionner une promotion</option>
                                    @foreach($promotions as $promo)
                                        <option value="{{ $promo->id }}">{{ $promo->titre }}</option>
                                    @endforeach
                                </select>
                                @error('promotion_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            {{-- Champ Groupe --}}
                            <div>
                                <label for="groupe_id_edit" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                                <select name="groupe_id" id="groupe_id_edit"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groupes as $groupe)
                                        <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                                    @endforeach
                                </select>
                                @error('groupe_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div>
                            {{-- Champ Sujets (sélection multiple) --}}
                            <!-- <div class="md:col-span-2">
                                <label for="sujet_ids_edit" class="block text-sm font-medium text-gray-700 mb-1">Sujets (sélection multiple)</label>
                                <select name="sujet_ids[]" id="sujet_ids_edit" multiple
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm"
                                    style="min-height: 120px;">
                                    @foreach($sujets as $sujet)
                                        <option value="{{ $sujet->id }}">
                                            {{ $sujet->titre }} (Promo: {{ $sujet->promotion->titre ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('sujet_ids') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                                @error('sujet_ids.*') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                            </div> -->
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression (vos modals existants) --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="deleteUserModalLabel">Confirmer la Suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir supprimer l'utilisateur : <span id="userNameToDelete" class="font-semibold text-red-600"></span> ?</p>
                <p class="text-red-500 text-sm font-semibold">Cette action est irréversible.</p>
                <form id="deleteUserForm" method="POST" class="flex justify-end space-x-4 mt-8">
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
// Cache pour les villes par pays afin d'éviter les requêtes répétées
const cityCache = {};
/**
* Charge et peuple la liste déroulante des villes en fonction du pays sélectionné.
* Utilise un cache pour les données déjà récupérées.
* @param {string} countryId - L'ID du pays sélectionné.
* @param {jQuery} citySelect - L'élément jQuery de la liste déroulante des villes.
* @param {string|null} selectedCityId - L'ID de la ville à présélectionner (pour le mode édition).
*/
function loadCities(countryId, citySelect, selectedCityId = null) {
    if (!countryId) {
        citySelect.empty().append('<option value="">Sélectionner un pays d\'abord</option>').prop('disabled', true);
        return;
    }
    // Utiliser le cache si les données sont déjà là
    if (cityCache[countryId]) {
        populateCitySelect(cityCache[countryId], citySelect, selectedCityId);
        return;
    }
    citySelect.prop('disabled', true).html('<option>Chargement…</option>');
    // Requête AJAX pour récupérer les villes
    // IMPORTANT: La route doit correspondre à ce qui est défini dans routes/web.php
    $.getJSON(`/villes/by-pays/${countryId}`, function(data) {
        cityCache[countryId] = data; // Stocker dans le cache
        populateCitySelect(data, citySelect, selectedCityId);
    }).fail(function() {
        citySelect.html('<option>Erreur chargement des villes</option>').prop('disabled', false);
        console.error("Erreur lors du chargement des villes pour le pays ID: " + countryId);
    });
}
/**
* Peuple un élément select jQuery avec des options de ville.
* @param {Array} data - Tableau d'objets ville ({id, nom}).
* @param {jQuery} citySelect - L'élément jQuery de la liste déroulante des villes.
* @param {string|null} selectedCityId - L'ID de la ville à présélectionner.
*/
function populateCitySelect(data, citySelect, selectedCityId) {
    citySelect.empty().append('<option value="">Sélectionner une ville</option>');
    data.forEach(function(ville) {
        const option = `<option value="${ville.id}" ${ville.id == selectedCityId ? 'selected' : ''}>${ville.nom}</option>`;
        citySelect.append(option);
    });
    citySelect.prop('disabled', false);
}
// Récupération de l'ID du rôle Stagiaire depuis le Blade
const STAGIAIRE_ROLE_ID = @json($stagiaireId);
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le modal "Ajouter un utilisateur"
    $('#addUserModal').on('show.bs.modal', function() {
        const modal = $(this);
        const form = modal.find('form');
        // Nettoyer les erreurs de validation précédentes
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.text-red-500.text-sm.mt-1').remove();
        form[0].reset(); // Réinitialise le formulaire
        // Pré-sélectionner le rôle Stagiaire et cacher les champs spécifiques à Stagiaire
        // Dans cette page, le rôle est toujours 'Stagiaire', donc nous l'affichons toujours.
        modal.find('#role_id_add').val(STAGIAIRE_ROLE_ID);
        modal.find('#stagiaire_fields_add').show(); // Afficher les champs spécifiques au stagiaire
        // Réinitialiser les champs spécifiques au stagiaire
        modal.find('#universite_add').val('');
        modal.find('#faculte_add').val('');
        modal.find('#titre_formation_add').val('');
        modal.find('#groupe_id_add').val('');
        modal.find('#promotion_id_add').val('');
        // Gérer Select2 pour les sujets si le champ est présent et visible
        if ($('#sujet_ids_add').length && $('#stagiaire_fields_add').is(':visible')) {
            $('#sujet_ids_add').val(null).trigger('change'); // Réinitialiser Select2
            $('#sujet_ids_add').select2({
                dropdownParent: $('#addUserModal'),
                placeholder: "Sélectionner un ou plusieurs sujets",
                allowClear: true,
                width: 'resolve'
            });
        }
        // Charger les villes pour le pays présélectionné (s'il old('pays_id') existe)
        loadCities(modal.find('#pays_id_add').val(), modal.find('#ville_id_add'), {{ old('ville_id') ?? 'null' }});
    });
    // Écouteur de changement sur le pays (modal Ajouter)
    $('#pays_id_add').on('change', function() {
        loadCities($(this).val(), $('#ville_id_add'));
    });

    // Gérer le modal "Modifier l'utilisateur" (modale unique)
    $('#editUserModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Bouton qui a déclenché le modal
        const userId = button.data('id');
        const form = $('#editUserForm');
        // Nettoyer les erreurs de validation précédentes
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.text-red-500.text-sm.mt-1').remove();
        // Remplir les champs du formulaire avec les données de l'utilisateur
        form.attr('action', `/admin/update/${userId}`); // Mise à jour de l'action du formulaire
        $('#id_edit').val(button.data('id'));
        $('#nom_edit').val(button.data('nom'));
        $('#prenom_edit').val(button.data('prenom'));
        $('#email_edit').val(button.data('email'));
        $('#telephone_edit').val(button.data('telephone'));
        $('#cin_edit').val(button.data('cin'));
        $('#code_edit').val(button.data('code'));
        $('#adresse_edit').val(button.data('adresse'));
        $('#pays_id_edit').val(button.data('pays_id'));
        $('#role_id_edit').val(button.data('role_id'));
        $('#statut_id_edit').val(button.data('statut_id'));
        // Champs spécifiques au stagiaire
        $('#universite_edit').val(button.data('universite'));
        $('#faculte_edit').val(button.data('faculte'));
        $('#titre_formation_edit').val(button.data('titre_formation'));
        $('#groupe_id_edit').val(button.data('groupe_id')); // Présélectionner le groupe
        $('#promotion_id_edit').val(button.data('promotion_id')); // Présélectionner la promotion
        // Pour les sujets (multi-select)
        const sujetIds = button.data('sujet_ids'); // Récupérer le tableau d'IDs
        const sujetSelectEdit = $('#sujet_ids_edit');
        sujetSelectEdit.find('option').prop('selected', false); // Désélectionner toutes les options
        if (Array.isArray(sujetIds)) {
            sujetIds.forEach(function(sujetId) {
                sujetSelectEdit.find('option[value="' + sujetId + '"]').prop('selected', true);
            });
        }
        $('#stagiaire_fields_edit').show(); // Afficher les champs spécifiques au stagiaire

        // Gérer Select2 pour les sujets si le champ est présent et visible
        if ($('#sujet_ids_edit').length && $('#stagiaire_fields_edit').is(':visible')) {
            // Détruire et réinitialiser Select2 pour assurer que les valeurs sont bien appliquées
            if (sujetSelectEdit.data('select2')) {
                sujetSelectEdit.select2('destroy');
            }
            sujetSelectEdit.select2({
                dropdownParent: $('#editUserModal'),
                placeholder: "Sélectionner un ou plusieurs sujets",
                allowClear: true,
                width: 'resolve'
            });
            sujetSelectEdit.val(sujetIds).trigger('change'); // Appliquer la sélection après initialisation
        }

        // Charger les villes pour le pays sélectionné dans le modal d'édition
        loadCities(button.data('pays_id'), $('#ville_id_edit'), button.data('ville_id'));
    });
    // Écouteur de changement sur le pays (modal Modifier)
    $('#pays_id_edit').on('change', function() {
        loadCities($(this).val(), $('#ville_id_edit'));
    });
    // Gérer le modal "Supprimer l'utilisateur"
    $('#deleteUserModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget); // Bouton qui a déclenché le modal
        const userId = button.data('user-id');
        const userName = button.data('user-name');
        $('#userNameToDelete').text(userName); // Affiche le nom de l'utilisateur à supprimer
        const deleteForm = $('#deleteUserForm');
        deleteForm.attr('action', `/admin/delete/${userId}`); // Définit l'action du formulaire de suppression
    });
    // Gestion des erreurs de validation après soumission (si la page se recharge avec des erreurs)
    @if ($errors->any())
        var openAddModal = false;
        var openEditModal = false;
        var editUserId = null;
        // Déterminer si les erreurs proviennent du formulaire d'ajout ou d'édition
        @if(Session::has('open_add_modal') && Session::get('open_add_modal'))
            openAddModal = true;
        @elseif(Session::has('edit_user_id'))
            openEditModal = true;
            editUserId = "{{ Session::get('edit_user_id') }}";
        @endif
        if (openAddModal) {
            var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
            addUserModal.show();
            // Assurez-vous que les champs stagiaires sont visibles et pré-remplis avec old()
            $('#stagiaire_fields_add').show();
            // Pré-remplir les sujets à partir de old('sujet_ids')
            const oldSujetIdsAdd = @json(old('sujet_ids', []));
            const sujetSelectAdd = $('#sujet_ids_add');
            // Initialiser Select2 avant de définir la valeur
            if (sujetSelectAdd.length) {
                sujetSelectAdd.select2({
                    dropdownParent: $('#addUserModal'),
                    placeholder: "Sélectionner un ou plusieurs sujets",
                    allowClear: true,
                    width: 'resolve'
                });
                if (oldSujetIdsAdd.length > 0) {
                    sujetSelectAdd.val(oldSujetIdsAdd).trigger('change');
                } else {
                    sujetSelectAdd.val(null).trigger('change');
                }
            }

        } else if (openEditModal) {
            var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editUserModal.show();
            const form = $('#editUserForm');
            // Re-populating ID is crucial for the form action to be correct on re-submission
            const oldId = "{{ old('id') }}";
            $('#id_edit').val(oldId);
            form.attr('action', `/admin/update/${oldId}`);
            $('#nom_edit').val("{{ old('nom') }}");
            $('#prenom_edit').val("{{ old('prenom') }}");
            $('#email_edit').val("{{ old('email') }}");
            $('#telephone_edit').val("{{ old('telephone') }}");
            $('#cin_edit').val("{{ old('cin') }}");
            $('#code_edit').val("{{ old('code') }}");
            $('#adresse_edit').val("{{ old('adresse') }}");
            $('#pays_id_edit').val("{{ old('pays_id') }}");
            $('#role_id_edit').val("{{ old('role_id') }}"); // Ceci sera toujours Stagiaire sur cette page
            $('#statut_id_edit').val("{{ old('statut_id') }}");
            $('#universite_edit').val("{{ old('universite') }}");
            $('#faculte_edit').val("{{ old('faculte') }}");
            $('#titre_formation_edit').val("{{ old('titre_formation') }}");
            $('#groupe_id_edit').val("{{ old('groupe_id') }}"); // Pré-remplir groupe
            $('#promotion_id_edit').val("{{ old('promotion_id') }}"); // Pré-remplir promotion
            // Pré-remplir les sujets à partir de old('sujet_ids')
            const oldSujetIdsEdit = @json(old('sujet_ids', []));
            const sujetSelectEdit = $('#sujet_ids_edit');
            // Initialiser Select2 avant de définir la valeur
            if (sujetSelectEdit.length) {
                if (sujetSelectEdit.data('select2')) {
                    sujetSelectEdit.select2('destroy'); // Détruire l'instance existante
                }
                sujetSelectEdit.select2({
                    dropdownParent: $('#editUserModal'),
                    placeholder: "Sélectionner un ou plusieurs sujets",
                    allowClear: true,
                    width: 'resolve'
                });
                if (oldSujetIdsEdit.length > 0) {
                    sujetSelectEdit.val(oldSujetIdsEdit).trigger('change');
                } else {
                    sujetSelectEdit.val(null).trigger('change');
                }
            }

            $('#stagiaire_fields_edit').show(); // Afficher les champs spécifiques au stagiaire
            loadCities($('#pays_id_edit').val(), $('#ville_id_edit'), "{{ old('ville_id') }}");
        }
        // Afficher les erreurs de validation spécifiques aux champs du formulaire des modales
        @foreach ($errors->messages() as $field => $messages)
            let inputAdd = document.getElementById('{{ $field }}_add');
            if (inputAdd) {
                inputAdd.classList.add('is-invalid');
                let feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1');
                feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                inputAdd.parentNode.appendChild(feedbackDiv);
            }
            let inputEdit = document.getElementById('{{ $field }}_edit');
            if (inputEdit) {
                inputEdit.classList.add('is-invalid');
                let feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1');
                feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                inputEdit.parentNode.appendChild(feedbackDiv);
            }
        @endforeach
    @endif
});
</script>
@endsection
