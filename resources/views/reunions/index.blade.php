@extends('layout.default')
@section('title', 'Réunions')
@section('content')
<div class="container mx-auto px-4 py-8"> {{-- Centrer le contenu et ajouter du padding --}}
    {{-- En-tête avec filtre et bouton Nouvelle réunion --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
        {{-- Formulaire de filtre --}}
        <form method="GET" class="w-full md:w-auto flex flex-col md:flex-row items-end gap-3" action="{{ route('reunions.index') }}">
            <div class="w-full md:w-48"> {{-- Largeur fixe pour le champ de date --}}
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" name="date" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('date', $date ?? now()->toDateString()) }}">
            </div>
           <div class="flex gap-3 text-center"> {{-- Conteneur pour les deux boutons : Filtrer et Réinitialiser --}}
                        <button type="submit" class="w-full md:w-auto flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                            <i class="bi bi-search mr-2"></i> Rechercher
                        </button>
                        <button type="button" onclick="resetFilter()" class="w-full md:w-auto flex items-center justify-center px-5 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                            <i class="bi bi-arrow-counterclockwise mr-2"></i> Réinitialiser
                        </button>
           </div>
        </form>
        {{-- Bouton Nouvelle réunion --}}
        <div class="w-full md:w-auto text-right">
            <button type="button" class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" data-bs-toggle="modal" data-bs-target="#modalCreerReunion">
                <i class="bi bi-plus-circle mr-2"></i> Nouvelle réunion
            </button>
        </div>
    </div>
    {{-- Message de succès --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Succès!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif
    
    {{-- Carte des réunions --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden"> {{-- Ombre et bords arrondis --}}
        <div class="p-5 border-b border-gray-200"> {{-- En-tête de la carte --}}
            <h1 class="text-xl font-semibold text-gray-800">Liste des <span class="text-blue-600">réunions</span></h1>
        </div>
        <div class="p-5"> {{-- Corps de la carte --}}
            @if($reunions->isEmpty())
                <div class="text-center py-10">
                    <p class="text-gray-500 text-lg">Aucune réunion prévue pour ce jour.</p>
                </div>
            @else
                <div class="overflow-x-auto"> {{-- Pour la réactivité du tableau sur petits écrans --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Groupe</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaires</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reunions as $reunion)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $reunion->groupe->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reunion->date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reunion->heure_debut }} - {{ $reunion->heure_fin }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $reunion->note ?? 'aucune note donné' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($reunion->status)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Clôturée</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En cours</span>
                                        @endif
                                    </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ route('reunions.show', $reunion->id) }}"
                                        class="inline-flex items-center justify-center p-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                                            <i class="bi bi-person-check-fill text-lg"></i> {{-- Icône seule, texte enlevé, taille augmentée --}}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        {{-- Pagination --}}
        <div class="p-5 bg-gray-50 border-t border-gray-200 flex justify-center"> {{-- Centrer la pagination et ajouter un peu de style --}}
            {{-- Utilise la pagination par défaut de Bootstrap 5 en lui passant les paramètres de filtre actuels --}}
            {{ $reunions->links('pagination::tailwind') }} {{-- Note: J'ai changé 'pagination::bootstrap-5' par 'pagination::tailwind' si vous avez une vue de pagination Tailwind --}}
        </div>
    </div>
</div>
<div class="modal fade" id="modalCreerReunion" tabindex="-1" aria-labelledby="modalCreerReunionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('reunions.store') }}">
            @csrf
            <div class="modal-content bg-white rounded-lg shadow-xl"> {{-- Styles de carte modernes pour le modal --}}
                <div class="modal-header p-5 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="modal-title text-xl font-semibold text-gray-800" id="modalCreerReunionLabel">Créer une Nouvelle Réunion</h5>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out" data-bs-dismiss="modal" aria-label="Close">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body p-5">
                    <div class="mb-4"> {{-- Espacement des champs --}}
                        <label for="groupe_id" class="block text-sm font-medium text-gray-700 mb-1">Groupe</label>
                        <select id="groupe_id" name="groupe_id" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required onchange="fetchStagiaires(this.value)">
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="stagiaires" class="block text-sm font-medium text-gray-700 mb-1">Stagiaires</label>
                        <ul id="stagiaires" class="border border-gray-300 rounded-md divide-y divide-gray-200 bg-white max-h-40 overflow-y-auto"> {{-- Styles pour la liste des stagiaires --}}
                            </ul>
                    </div>
                    <div class="mb-4">
                        <label for="date_modal" class="block text-sm font-medium text-gray-700 mb-1">Date</label> {{-- Renommé pour éviter le conflit d'ID --}}
                        <input type="date" id="date_modal" name="date" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"> {{-- Deux colonnes pour les heures sur desktop --}}
                        <div>
                            <label for="heure_debut" class="block text-sm font-medium text-gray-700 mb-1">Heure de Début</label>
                            <input type="time" id="heure_debut" name="heure_debut" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        <div>
                            <label for="heure_fin" class="block text-sm font-medium text-gray-700 mb-1">Heure de Fin</label>
                            <input type="time" id="heure_fin" name="heure_fin" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                        <textarea id="note" name="note" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" rows="3" placeholder="Description de la réunion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-5 border-t border-gray-200 flex justify-end gap-3"> {{-- Boutons au pied du modal --}}
                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="bi bi-save mr-2"></i> Créer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('my_js')
<script>
    // Définir une variable JavaScript pour stocker l'URL de la route
    const stagiairesActifsRoute = "{{ route('groupes.stagiairesActifs', ['id' => '__GROUP_ID__']) }}";

    function fetchStagiaires(groupeId) {
        const stagiairesList = document.getElementById('stagiaires');
        if (!groupeId) {
            stagiairesList.innerHTML = '';
            return;
        }

        // Remplacer le placeholder par l'ID réel du groupe
        const url = stagiairesActifsRoute.replace('__GROUP_ID__', groupeId);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                stagiairesList.innerHTML = '';
                if (data.length === 0) {
                    const li = document.createElement('li');
                    li.textContent = 'Aucun stagiaire trouvé dans ce groupe.';
                    li.className = 'px-4 py-2 text-sm text-gray-500';
                    stagiairesList.appendChild(li);
                } else {
                    data.forEach(stagiaire => {
                        const li = document.createElement('li');
                        li.textContent = `${stagiaire.nom} ${stagiaire.prenom}`;
                        li.className = 'px-4 py-2 text-sm text-gray-700';
                        stagiairesList.appendChild(li);
                    });
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des stagiaires :', error);
                stagiairesList.innerHTML = '<li class="px-4 py-2 text-sm text-red-500">Erreur de chargement des stagiaires pour ce groupe.</li>';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dateModalInput = document.getElementById('date_modal');
        if (dateModalInput) {
            dateModalInput.valueAsDate = new Date();
        }
    });

    function resetFilter() {
        document.getElementById('date').value = '';
        document.querySelector("form[action='{{ route('reunions.index') }}']").submit();
    }
</script>
@endsection
