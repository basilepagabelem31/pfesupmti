@extends('layout.default')

@section('title', 'Gestion Admin & Superviseurs')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-indigo-600">Administrateurs</span> et <span class="text-purple-600">Superviseurs</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez, ajoutez, modifiez et supprimez les comptes administratifs et de supervision.
            </p>

            {{-- Bouton Ajouter --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#modal-add">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un Administrateur/Superviseur
                </button>
            </div>

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

            {{-- Tableau des Administrateurs/Superviseurs --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    {{-- Modification de la couleur de l'en-tête du tableau --}}
                    <thead class="bg-indigo-50"> 
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Prénom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">CIN</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Adresse</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Pays</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Ville</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Statut</th>

                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-800 uppercase tracking-wider">Rôle</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-800 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($admins as $admin)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->prenom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->telephone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->cin }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->adresse }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->pays?->nom  }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $admin->ville?->nom }}</td>
                            
                            
                            
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($admin->statut?->nom == 'Actif') bg-blue-100 text-blue-800
                                        @elseif ($admin->statut?->nom == 'Terminé') bg-indigo-100 text-indigo-800
                                        @elseif($admin->statut?->nom == 'Abandonné') bg-red-100 text-red-800
                                        @elseif ($admin->statut?->nom == 'Archivé') bg-gray-200 text-gray-700
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $admin->statut?->nom }}
                                    </span>
                                </td>

                            
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">

            
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($admin->role?->nom == 'Administrateur') bg-indigo-100 text-indigo-800
                                    @elseif($admin->role?->nom == 'Superviseur') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $admin->role?->nom }}
                                </span>
                            </td>




                       

                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <button class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $admin->id }}">
                                         <i class="fas fa-edit"></i>
                                    </button>
<button type="button" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
    data-bs-toggle="modal"
    data-bs-target="#deleteConfirmationModal"
    data-item-id="{{ $admin->id }}"
    data-item-title="{{ $admin->prenom }} {{ $admin->nom }}">
    <i class="fas fa-trash-alt"></i>
</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="px-6 py-4 text-center text-gray-500" colspan="12">Aucun administrateur ou superviseur trouvé.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                <div class="flex justify-center">
                    {{ $admins->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>
</div>







{{-- Modal Ajouter un Utilisateur --}}
<div class="modal fade" id="modal-add" tabindex="-1" aria-labelledby="modal-add-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="modal-add-label">Ajouter un Admin/Superviseur</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form id="addForm" action="{{ route('admin.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom_add" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="nom_add" name="nom" value="{{ old('nom') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="prenom_add" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="prenom_add" name="prenom" value="{{ old('prenom') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email_add" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email_add" name="email" value="{{ old('email') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="password_add" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                            <input type="password" id="password_add" name="password" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="telephone_add" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" id="telephone_add" name="telephone" value="{{ old('telephone') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cin_add" class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                            <input type="text" id="cin_add" name="cin" value="{{ old('cin') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="adresse_add" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" id="adresse_add" name="adresse" value="{{ old('adresse') }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="pays_id_add" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <select id="pays_id_add" name="pays_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un pays</option>
                                @foreach($pays as $country)
                                    <option value="{{ $country->id }}" {{ old('pays_id') == $country->id ? 'selected' : '' }}>{{ $country->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="ville_id_add" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <select id="ville_id_add" name="ville_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un pays d'abord</option>
                            </select>
                        </div>
                        <div>
                            <label for="role_id_add" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            <select id="role_id_add" name="role_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    @if($role->nom !== 'Stagiaire') {{-- Filtre pour exclure le rôle Stagiaire --}}
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="statut_id_add" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select id="statut_id_add" name="statut_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuts as $statut)
                                    <option value="{{ $statut->id }}" {{ old('statut_id') == $statut->id ? 'selected' : '' }}>{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Champs spécifiques au Stagiaire (cachés par défaut car cette page ne gère pas les stagiaires) --}}
                    <div id="fields_add" class="space-y-4 border border-blue-200 rounded-xl p-4 bg-blue-50 mt-6 hidden">
                        <p class="text-blue-800 font-semibold">Informations spécifiques au stagiaire (non applicables ici) :</p>
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
                            {{-- Champs Promotion, Groupe, Sujets sont également spécifiques aux stagiaires --}}
                            <div>
                                <label for="promotion_id_add" class="block text-sm font-medium text-gray-700 mb-1">Promotion</label>
                                <select name="promotion_id" id="promotion_id_add" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Sélectionner une promotion</option>
                                    @foreach($promotions as $promo)
                                        <option value="{{ $promo->id }}">{{ $promo->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="groupe_id_add" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                                <select name="groupe_id" id="groupe_id_add" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groupes as $groupe)
                                        <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="sujet_ids_add" class="block text-sm font-medium text-gray-700 mb-1">Sujets (sélection multiple)</label>
                                <select name="sujet_ids[]" id="sujet_ids_add" multiple class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" style="min-height: 120px;">
                                    @foreach($sujets as $sujet)
                                        <option value="{{ $sujet->id }}">{{ $sujet->titre }} (Promo: {{ $sujet->promotion->titre ?? 'N/A' }})</option>
                                    @endforeach
                                </select>
                            </div>
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

{{-- Modales Modifier un Utilisateur (une par admin/superviseur) --}}
@foreach($admins as $admin)
<div class="modal fade" id="modal-edit-{{ $admin->id }}" tabindex="-1" aria-labelledby="modal-edit-{{ $admin->id }}-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="modal-edit-{{ $admin->id }}-label">Modifier l'utilisateur</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <form action="{{ route('admin.update', $admin->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $admin->id }}"> {{-- Champ caché pour l'ID --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">ID Utilisateur</label>
                            <input type="text" id="id_edit_{{ $admin->id }}" name="id" value="{{ $admin->id }}" readonly
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">
                        </div>
                        <div>
                            <label for="code_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                            <input type="text" id="code_edit_{{ $admin->id }}" name="code" value="{{ $admin->code }}" readonly
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed sm:text-sm">
                        </div>
                        <div>
                            <label for="nom_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="nom_edit_{{ $admin->id }}" name="nom" value="{{ old('nom', $admin->nom) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="prenom_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                            <input type="text" id="prenom_edit_{{ $admin->id }}" name="prenom" value="{{ old('prenom', $admin->prenom) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email_edit_{{ $admin->id }}" name="email" value="{{ old('email', $admin->email) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="password_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" id="password_edit_{{ $admin->id }}" name="password"
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="telephone_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" id="telephone_edit_{{ $admin->id }}" name="telephone" value="{{ old('telephone', $admin->telephone) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="cin_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                            <input type="text" id="cin_edit_{{ $admin->id }}" name="cin" value="{{ old('cin', $admin->cin) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="adresse_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" id="adresse_edit_{{ $admin->id }}" name="adresse" value="{{ old('adresse', $admin->adresse) }}" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="pays_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                            <select id="pays_id_edit_{{ $admin->id }}" name="pays_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un pays</option>
                                @foreach($pays as $country)
                                    <option value="{{ $country->id }}" {{ old('pays_id', $admin->pays_id) == $country->id ? 'selected' : '' }}>{{ $country->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="ville_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <select id="ville_id_edit_{{ $admin->id }}" name="ville_id" required disabled
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un pays d'abord</option>
                            </select>
                        </div>
                        <div>
                            <label for="role_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                            <select id="role_id_edit_{{ $admin->id }}" name="role_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un rôle</option>
                                @foreach($roles as $role)
                                    @if($role->nom !== 'Stagiaire') {{-- Filtre pour exclure le rôle Stagiaire --}}
                                        <option value="{{ $role->id }}" {{ old('role_id', $admin->role_id) == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="statut_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                            <select id="statut_id_edit_{{ $admin->id }}" name="statut_id" required
                                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                <option value="">Sélectionner un statut</option>
                                @foreach($statuts as $statut)
                                    <option value="{{ $statut->id }}" {{ old('statut_id', $admin->statut_id) == $statut->id ? 'selected' : '' }}>{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Champs spécifiques au Stagiaire (cachés pour cette page) --}}
                    <div id="fields_edit_{{ $admin->id }}" class="space-y-4 border border-yellow-200 rounded-xl p-4 bg-yellow-50 mt-6 hidden">
                        <p class="text-yellow-800 font-semibold">Informations spécifiques au stagiaire (non applicables ici) :</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="universite_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Université</label>
                                <input type="text" id="universite_edit_{{ $admin->id }}" name="universite" value="{{ old('universite', $admin->universite) }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="faculte_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Faculté</label>
                                <input type="text" id="faculte_edit_{{ $admin->id }}" name="faculte" value="{{ old('faculte', $admin->faculte) }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="titre_formation_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Titre de Formation</label>
                                <input type="text" id="titre_formation_edit_{{ $admin->id }}" name="titre_formation" value="{{ old('titre_formation', $admin->titre_formation) }}"
                                    class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                            </div>
                            {{-- Champs Promotion, Groupe, Sujets sont également spécifiques aux stagiaires --}}
                            <div>
                                <label for="promotion_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Promotion</label>
                                <select name="promotion_id" id="promotion_id_edit_{{ $admin->id }}" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                    <option value="">Sélectionner une promotion</option>
                                    @foreach($promotions as $promo)
                                        <option value="{{ $promo->id }}" {{ old('promotion_id', $admin->promotion_id) == $promo->id ? 'selected' : '' }}>{{ $promo->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="groupe_id_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                                <select name="groupe_id" id="groupe_id_edit_{{ $admin->id }}" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groupes as $groupe)
                                        <option value="{{ $groupe->id }}" {{ old('groupe_id', $admin->groupe_id) == $groupe->id ? 'selected' : '' }}>{{ $groupe->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="sujet_ids_edit_{{ $admin->id }}" class="block text-sm font-medium text-gray-700 mb-1">Sujets (sélection multiple)</label>
                                <select name="sujet_ids[]" id="sujet_ids_edit_{{ $admin->id }}" multiple class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" style="min-height: 120px;">
                                    @foreach($sujets as $sujet)
                                        <option value="{{ $sujet->id }}" {{ in_array($sujet->id, old('sujet_ids', $admin->sujets->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $sujet->titre }} (Promo: {{ $sujet->promotion->titre ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
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
@endforeach

{{-- Modal de confirmation de suppression --}}
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



{{--Modale de suppression    --}}

<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-2xl font-bold text-gray-900 text-center flex-grow" id="deleteConfirmationModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center text-gray-700">
                Êtes-vous sûr de vouloir supprimer l'utilisateur "<span id="itemTitleToDelete" class="font-semibold text-red-600"></span>" ? Cette action est irréversible.
            </div>
            <div class="modal-footer flex justify-center space-x-4 mt-8 border-t border-gray-100 pt-4">
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('my_js')
<!-- jQuery en premier si besoin -->

<script>
// Cache pour les villes par pays
const cityCache = {};

function loadCities(countryId, citySelect, selected = null) {
    if (!countryId) {
        citySelect.empty().append('<option value="">Sélectionner un pays d\'abord</option>').prop('disabled', true); // Désactiver si aucun pays
        return;
    }
    // Si données globales hydratées (optionnel)
    if (window._paysVilles) {
        const pays = window._paysVilles.find(p => p.id == countryId);
        const villes = pays ? pays.villes : [];
        populate(villes, citySelect, selected);
        return;
    }
    // Fallback AJAX avec cache
    if (cityCache[countryId]) {
        populate(cityCache[countryId], citySelect, selected);
        return;
    }
    citySelect.prop('disabled', true).html('<option>Chargement…</option>');
    $.getJSON(`/villes/by-pays/${countryId}`, data => {
        cityCache[countryId] = data;
        populate(data, citySelect, selected);
    }).fail(() => {
        citySelect.html('<option>Erreur chargement</option>').prop('disabled', false);
    });
}

function populate(data, citySelect, selected) {
    citySelect.empty().append('<option value="">Sélectionner une ville</option>');
    data.forEach(v => {
        citySelect.append(
            `<option value="${v.id}" ${v.id==selected?'selected':''}>${v.nom}</option>`
        );
    });
    citySelect.prop('disabled', false);
}

document.addEventListener('DOMContentLoaded', function() {
    // STAGIAIRE_ID est récupéré du contrôleur AdminController::index()
    // Il est utilisé pour masquer les champs spécifiques au stagiaire dans les modales Add/Edit pour cette page.
    const STAGIAIRE_ID = @json($stagiaireId ?? null); 

    // Fonction d'affichage/masquage des champs stagiaires
    function toggleStagiaireFields(roleSelect, container) {
        // Dans cette page (admin/superviseur), les champs stagiaires doivent toujours être cachés.
        // La logique est là pour la robustesse, mais ils ne devraient jamais s'afficher ici.
        if (STAGIAIRE_ID !== null && parseInt(roleSelect.val()) === parseInt(STAGIAIRE_ID)) {
            container.show();
        } else {
            container.hide().find('input, select').val(''); // Cache et vide les champs
        }
    }

    // Événements pour le modal « Ajouter »
    $('#modal-add').on('show.bs.modal', function() {
        const modal = $(this);
        const form = modal.find('form');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.text-red-500.text-sm.mt-1').remove();
        form[0].reset(); // Réinitialiser le formulaire
        
        // Cacher les champs stagiaires à l'ouverture (par défaut pour cette page)
        modal.find('#fields_add').hide().find('input, select').val('');
        
        loadCities(modal.find('#pays_id_add').val(), modal.find('#ville_id_add'));
    });
    // L'écouteur de changement de rôle est toujours là, mais pour cette page,
    // le sélecteur ne proposera pas "Stagiaire".
    $('#role_id_add').on('change', function() {
        toggleStagiaireFields($(this), $('#fields_add'));
    });
    $('#pays_id_add').on('change', function() {
        loadCities($(this).val(), $('#ville_id_add'));
    });

    // Gérer les erreurs de validation à la soumission du formulaire d'ajout
    @if(Session::has('open_add_modal') && Session::get('open_add_modal'))
        var addUserModal = new bootstrap.Modal(document.getElementById('modal-add'));
        addUserModal.show();
        // Si la modale s'ouvre avec des erreurs, assurer que les champs stagiaires sont cachés
        $('#fields_add').hide(); 
        // Re-charger les villes pour le pays sélectionné en cas d'erreur
        loadCities($('#pays_id_add').val(), $('#ville_id_add'), "{{ old('ville_id') }}");
    @endif

    // Événements pour chaque modal « Edit »
    @foreach($admins as $admin)
    $('#modal-edit-{{ $admin->id }}').on('show.bs.modal', function() {
        const m = $(this);
        const roleSel = m.find('#role_id_edit_{{ $admin->id }}');
        const fields = m.find('#fields_edit_{{ $admin->id }}');
        const currentVilleId = {{ $admin->ville_id ?? 'null' }};

        // Nettoyer les erreurs de validation précédentes pour cette modale spécifique
        m.find('.is-invalid').removeClass('is-invalid');
        m.find('.text-red-500.text-sm.mt-1').remove();

        // Cacher les champs stagiaires pour l'édition (par défaut pour cette page)
        fields.hide(); 
        // Pas besoin de vider les champs ici car ils seront remplis par old() ou les données de l'admin

        // Chargement des villes avec la ville déjà sélectionnée
        loadCities(
            m.find('#pays_id_edit_{{ $admin->id }}').val(),
            m.find('#ville_id_edit_{{ $admin->id }}'),
            currentVilleId
        );

        // Si des erreurs de validation sont présentes pour cet utilisateur en particulier
        @if(Session::has('edit_user_id') && Session::get('edit_user_id') == $admin->id)
            // Ré-appliquer la valeur du rôle à partir de old() pour gérer la persistance de l'erreur
            // Si le rôle n'est pas "Stagiaire", les champs sont cachés.
            let oldRoleId = "{{ old('role_id', $admin->role_id) }}";
            roleSel.val(oldRoleId);
            toggleStagiaireFields(roleSel, fields); // Appliquer la visibilité basée sur old value
            
            // Re-charger les villes avec la valeur old('ville_id') si une erreur est survenue
            loadCities(
                m.find('#pays_id_edit_{{ $admin->id }}').val(),
                m.find('#ville_id_edit_{{ $admin->id }}'),
                "{{ old('ville_id', $admin->ville_id) }}"
            );
        @endif
    });

    $('#role_id_edit_{{ $admin->id }}').on('change', function() {
        toggleStagiaireFields($(this), $('#fields_edit_{{ $admin->id }}'));
    });
    $('#pays_id_edit_{{ $admin->id }}').on('change', function() {
        loadCities($(this).val(), $('#ville_id_edit_{{ $admin->id }}'));
    });

    // Gestion des erreurs de validation spécifiques aux champs du formulaire d'édition
    @if(Session::has('edit_user_id') && Session::get('edit_user_id') == $admin->id && $errors->any())
        var editModal = new bootstrap.Modal(document.getElementById('modal-edit-{{ $admin->id }}'));
        editModal.show();
        // Afficher les erreurs spécifiques sur les inputs de la modale
        @foreach ($errors->messages() as $field => $messages)
            let inputEdit = document.getElementById('{{ $field }}_edit_{{ $admin->id }}');
            if (inputEdit) {
                inputEdit.classList.add('is-invalid');
                let feedbackDiv = document.createElement('div');
                feedbackDiv.classList.add('text-red-500', 'text-sm', 'mt-1');
                feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                inputEdit.parentNode.appendChild(feedbackDiv);
            }
        @endforeach
    @endif
    @endforeach

    // Gérer le modal "Supprimer l'utilisateur" (identique à avant)
    $('#deleteUserModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const userId = button.data('user-id');
        const userName = button.data('user-name');
        $('#userNameToDelete').text(userName);
        const deleteForm = $('#deleteUserForm');
        deleteForm.attr('action', `/admin/delete/${userId}`);
    });
});



document.addEventListener('DOMContentLoaded', function () {
    const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
    if (deleteConfirmationModal) {
        deleteConfirmationModal.addEventListener('show.bs.modal', function (event) {
            // Bouton qui a déclenché le modal
            const button = event.relatedTarget;

            // Récupère les informations de l'élément depuis les attributs data-*
            const itemId = button.getAttribute('data-item-id');
            const itemTitle = button.getAttribute('data-item-title');

            // Met à jour le contenu du modal
            const itemTitleSpan = deleteConfirmationModal.querySelector('#itemTitleToDelete');
            if (itemTitleSpan) {
                itemTitleSpan.textContent = itemTitle;
            }

            // Crée ou met à jour un formulaire de suppression caché dans le modal
            let deleteForm = deleteConfirmationModal.querySelector('#modalDeleteForm');
            if (!deleteForm) {
                deleteForm = document.createElement('form');
                deleteForm.setAttribute('id', 'modalDeleteForm');
                deleteForm.setAttribute('method', 'POST');
                deleteForm.style.display = 'none'; // Cache le formulaire
                deleteConfirmationModal.querySelector('.modal-body').appendChild(deleteForm);

                const csrfInput = document.createElement('input');
                csrfInput.setAttribute('type', 'hidden');
                csrfInput.setAttribute('name', '_token');
                csrfInput.setAttribute('value', '{{ csrf_token() }}');
                deleteForm.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.setAttribute('type', 'hidden');
                methodInput.setAttribute('name', '_method');
                methodInput.setAttribute('value', 'DELETE');
                deleteForm.appendChild(methodInput);
            }

            // Met à jour l'action du formulaire avec l'ID de l'élément
            // Utilise la route 'admin.delete' que vous avez dans web.php
            deleteForm.setAttribute('action', `/admin/delete/${itemId}`);

            // Gère le clic sur le bouton "Supprimer" du modal
            const confirmDeleteButton = deleteConfirmationModal.querySelector('#confirmDeleteButton');
            if (confirmDeleteButton) {
                confirmDeleteButton.onclick = function () {
                    deleteForm.submit(); // Soumet le formulaire de suppression
                };
            }
        });
    }
});
</script>
@endsection
