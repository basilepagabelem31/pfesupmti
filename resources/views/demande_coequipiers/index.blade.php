@extends('layout.default')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-7xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-purple-600">Demandes</span> de Coéquipiers
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                @if(Auth::user()->isStagiaire())
                Gérez vos demandes de coéquipiers et les requêtes reçues.
                @else
                Consultez l'ensemble des demandes de coéquipiers dans le système.
                @endif
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
            @if (session('info'))
                <div class="bg-blue-100 text-blue-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-blue-200">
                    <svg class="h-7 w-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('info') }}</span>
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

            @if(Auth::user()->isStagiaire())
                {{-- Bouton pour envoyer une nouvelle demande (visible seulement pour les stagiaires) --}}
                <div class="flex justify-start mb-8">
                    <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider"
                        data-bs-toggle="modal" data-bs-target="#createDemandeModal">
                        <i class="fas fa-paper-plane mr-2"></i> Envoyer une nouvelle demande
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Demandes Reçues (pour les stagiaires) --}}
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-inner">
                        <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-inbox text-purple-600 mr-3"></i> Demandes Reçues
                        </h2>
                        @if ($demandesRecues->isEmpty())
                            <div class="text-gray-600 text-lg py-4 text-center">Aucune demande de coéquipier reçue pour l'instant.</div>
                        @else
                            <ul class="space-y-4">
                                @foreach ($demandesRecues as $demande)
                                    <li class="bg-white p-4 rounded-lg shadow border border-gray-100">
                                        <p class="text-gray-700 font-medium mb-1">De: <span class="text-indigo-700">{{ $demande->demandeur->nom }} {{ $demande->demandeur->prenom }}</span> ({{ $demande->demandeur->email }})</p>
                                        <p class="text-gray-600 text-sm mb-2">Envoyée le: {{ $demande->date_demande->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 font-semibold mb-3">Statut: 
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($demande->statut_demande === 'en_attente') bg-yellow-100 text-yellow-800
                                                @elseif($demande->statut_demande === 'acceptée') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $demande->statut_demande)) }}
                                            </span>
                                        </p>

                                        @if ($demande->statut_demande === 'en_attente')
                                            <div class="flex space-x-2 mt-3">
                                                <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out"
                                                    data-bs-toggle="modal" data-bs-target="#acceptDemandeModal"
                                                    data-accept-url="{{ route('demande_coequipiers.accept', $demande) }}" 
                                                    data-demandeur-name="{{ $demande->demandeur->prenom }} {{ $demande->demandeur->nom }}">
                                                    <i class="fas fa-check-circle mr-1"></i> Accepter
                                                </button>
                                                <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                                    data-bs-toggle="modal" data-bs-target="#refuseDemandeModal"
                                                    data-refuse-url="{{ route('demande_coequipiers.refuse', $demande) }}" 
                                                    data-demandeur-name="{{ $demande->demandeur->prenom }} {{ $demande->demandeur->nom }}">
                                                    <i class="fas fa-times-circle mr-1"></i> Refuser
                                                </button>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Demandes Envoyées (pour les stagiaires) --}}
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-inner">
                        <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                            <i class="fas fa-paper-plane text-blue-600 mr-3"></i> Demandes Envoyées
                        </h2>
                        @if ($demandesEnvoyees->isEmpty())
                            <div class="text-gray-600 text-lg py-4 text-center">Aucune demande de coéquipier envoyée pour l'instant.</div>
                        @else
                            <ul class="space-y-4">
                                @foreach ($demandesEnvoyees as $demande)
                                    <li class="bg-white p-4 rounded-lg shadow border border-gray-100">
                                        <p class="text-gray-700 font-medium mb-1">À: <span class="text-indigo-700">{{ $demande->receveur->nom }} {{ $demande->receveur->prenom }}</span> ({{ $demande->receveur->email }})</p>
                                        <p class="text-gray-600 text-sm mb-2">Envoyée le: {{ $demande->date_demande->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 font-semibold mb-3">Statut: 
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($demande->statut_demande === 'en_attente') bg-yellow-100 text-yellow-800
                                                @elseif($demande->statut_demande === 'acceptée') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $demande->statut_demande)) }}
                                            </span>
                                        </p>

                                        @if ($demande->statut_demande === 'en_attente')
                                            <div class="flex space-x-2 mt-3">
                                                <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-150 ease-in-out"
                                                    data-bs-toggle="modal" data-bs-target="#cancelDemandeModal"
                                                    data-cancel-url="{{ route('demande_coequipiers.cancel', $demande) }}" 
                                                    data-receveur-name="{{ $demande->receveur->prenom }} {{ $demande->receveur->nom }}">
                                                    <i class="fas fa-ban mr-1"></i> Annuler
                                                </button>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- MODALS (visible seulement pour les stagiaires) --}}

                {{-- Modal pour Envoyer une nouvelle Demande --}}
                <div class="modal fade" id="createDemandeModal" tabindex="-1" aria-labelledby="createDemandeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
                            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="createDemandeModalLabel">Envoyer une Demande de Coéquipier</h5>
                                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-6">
                                <form id="createDemandeForm" action="{{ route('demande_coequipiers.store') }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div>
                                        <label for="id_stagiaire_receveur_create" class="block text-sm font-medium text-gray-700 mb-1">Sélectionner le Stagiaire</label>
                                        <select id="id_stagiaire_receveur_create" name="id_stagiaire_receveur" required
                                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">-- Choisir un stagiaire --</option>
                                            @foreach ($stagiairesForNewRequest as $stagiaire)
                                                <option value="{{ $stagiaire->id }}" {{ old('id_stagiaire_receveur') == $stagiaire->id ? 'selected' : '' }}>
                                                    {{ $stagiaire->nom }} {{ $stagiaire->prenom }} (Code: {{ $stagiaire->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_stagiaire_receveur')<p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>@enderror
                                    </div>

                                    <div class="flex justify-end space-x-4 mt-8">
                                        <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">Envoyer la Demande</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal pour Accepter une Demande --}}
                <div class="modal fade" id="acceptDemandeModal" tabindex="-1" aria-labelledby="acceptDemandeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
                            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="acceptDemandeModalLabel">Accepter la Demande</h5>
                                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-6">
                                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir accepter la demande de coéquipier de <span id="demandeurNameToAccept" class="font-semibold text-green-600"></span> ?</p>
                                <form id="acceptDemandeForm" method="POST" class="flex justify-end space-x-4 mt-8">
                                    @csrf
                                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">Accepter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal pour Refuser une Demande --}}
                <div class="modal fade" id="refuseDemandeModal" tabindex="-1" aria-labelledby="refuseDemandeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
                            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="refuseDemandeModalLabel">Refuser la Demande</h5>
                                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-6">
                                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir refuser la demande de coéquipier de <span id="demandeurNameToRefuse" class="font-semibold text-red-600"></span> ?</p>
                                <form id="refuseDemandeForm" method="POST" class="flex justify-end space-x-4 mt-8">
                                    @csrf
                                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Refuser</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal pour Annuler une Demande Envoyée --}}
                <div class="modal fade" id="cancelDemandeModal" tabindex="-1" aria-labelledby="cancelDemandeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
                            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                                <h5 class="modal-title text-2xl font-extrabold text-gray-900 text-center flex-grow" id="cancelDemandeModalLabel">Annuler la Demande</h5>
                                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body space-y-6">
                                <p class="text-gray-700 text-lg mb-4">Êtes-vous sûr de vouloir annuler la demande de coéquipier envoyée à <span id="receveurNameToCancel" class="font-semibold text-red-600"></span> ?</p>
                                <form id="cancelDemandeForm" method="POST" class="flex justify-end space-x-4 mt-8">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="py-3 px-4 rounded-lg text-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Confirmer Annulation</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                {{-- Tableau de toutes les demandes (visible pour superviseurs et admins) --}}
                <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead class="bg-purple-50"> 
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-purple-800 uppercase tracking-wider">Demandeur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-purple-800 uppercase tracking-wider">Receveur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-purple-800 uppercase tracking-wider">Date de Demande</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-purple-800 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($allDemandes as $demande)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $demande->demandeur->prenom }} {{ $demande->demandeur->nom }} ({{ $demande->demandeur->email }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $demande->receveur->prenom }} {{ $demande->receveur->nom }} ({{ $demande->receveur->email }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $demande->date_demande->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($demande->statut_demande === 'en_attente') bg-yellow-100 text-yellow-800
                                        @elseif($demande->statut_demande === 'acceptée') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $demande->statut_demande)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune demande de coéquipier trouvée.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('my_js')


@if(Auth::user()->isStagiaire())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer le modal "Envoyer une nouvelle Demande"
        $('#createDemandeModal').on('show.bs.modal', function(event) {
            const form = $('#createDemandeForm');
            form[0].reset(); // Réinitialiser le formulaire
            form.find('.is-invalid').removeClass('is-invalid'); // Supprimer les classes d'erreur
            form.find('.text-red-500.text-xs.italic.mt-1').remove(); // Supprimer les messages d'erreur
        });

        // Gérer le modal "Accepter une Demande"
        $('#acceptDemandeModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            // On récupère l'URL complète directement depuis l'attribut data-accept-url
            const acceptUrl = button.data('accept-url'); 
            const demandeurName = button.data('demandeur-name');

            $('#demandeurNameToAccept').text(demandeurName);
            const form = $('#acceptDemandeForm');
            form.attr('action', acceptUrl); // Définit l'action du formulaire avec l'URL complète
        });
 
        // Gérer le modal "Refuser une Demande"
        $('#refuseDemandeModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            // On récupère l'URL complète directement depuis l'attribut data-refuse-url
            const refuseUrl = button.data('refuse-url');
            const demandeurName = button.data('demandeur-name');

            $('#demandeurNameToRefuse').text(demandeurName);
            const form = $('#refuseDemandeForm');
            form.attr('action', refuseUrl); // Définit l'action du formulaire avec l'URL complète
        });

        // Gérer le modal "Annuler une Demande Envoyée"
        $('#cancelDemandeModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            // On récupère l'URL complète directement depuis l'attribut data-cancel-url
            const cancelUrl = button.data('cancel-url');
            const receveurName = button.data('receveur-name');

            $('#receveurNameToCancel').text(receveurName);
            const form = $('#cancelDemandeForm');
            form.attr('action', cancelUrl); // Définit l'action du formulaire avec l'URL complète
        });

        // Gestion des erreurs de validation (si la page se recharge avec des erreurs)
        @if ($errors->any())
            var openCreateModal = false;
            // Vérifier si les erreurs proviennent du formulaire de création (basé sur le champ `id_stagiaire_receveur`)
            @if($errors->has('id_stagiaire_receveur'))
                openCreateModal = true;
            @endif

            if (openCreateModal) {
                var createDemandeModal = new bootstrap.Modal(document.getElementById('createDemandeModal'));
                createDemandeModal.show();
                // Assurez-vous que les anciennes valeurs sont restaurées
                $('#id_stagiaire_receveur_create').val("{{ old('id_stagiaire_receveur') }}");
            }
            
            // Afficher les messages d'erreur sous les champs
            @foreach ($errors->messages() as $field => $messages)
                let inputElement = document.getElementById('{{ $field }}_create');
                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                    let feedbackDiv = document.createElement('p');
                    feedbackDiv.classList.add('text-red-500', 'text-xs', 'italic', 'mt-1');
                    feedbackDiv.textContent = '{{ implode(", ", $messages) }}';
                    inputElement.parentNode.appendChild(feedbackDiv);
                }
            @endforeach
        @endif
    });
</script>
@endif
@endsection