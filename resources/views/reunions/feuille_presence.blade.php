@extends('layout.default')

@section('title', 'Feuille de Présence')

@section('content')
<div class="min-h-screen p-6 sm:p-10"> {{-- Fond blanc pur, padding responsif --}}
    <div class="max-w-7xl mx-auto"> {{-- Conteneur centré pour le contenu principal --}}

        {{-- En-tête de la page avec bouton de retour et titre --}}
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('reunions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                <i class="bi bi-arrow-left mr-2"></i> Retour aux réunions
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900 ml-4">
                Feuille de Présence - Réunion du <span class="text-blue-600">{{ $reunion->date->format('d/m/Y') }}</span>
            </h1>
        </div>

        {{-- Message de succès (géré par Laravel session, non AJAX) --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg relative mb-6 shadow-md" role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg>
                    </div>
                    <div>
                        <strong class="font-bold">Succès!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.closest('.bg-green-50').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        {{-- Section des détails de la réunion --}}
        <div class="bg-white shadow-lg rounded-xl p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <p class="text-xl font-semibold text-gray-800 mb-2">
                    Groupe: <span class="font-normal text-blue-600">{{ $reunion->groupe->nom }}</span>
                </p>
                <p class="text-md text-gray-600 mb-1">
                    Date: <span class="font-medium">{{ $reunion->date->format('d/m/Y') }}</span>
                </p>
                <p class="text-md text-gray-600 mb-1">
                    Horaires: <span class="font-medium">{{ $reunion->heure_debut }} - {{ $reunion->heure_fin }}</span>
                </p>
                <p class="text-md text-gray-600">
                    Statut:
                    @if($reunion->status)
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Clôturée</span>
                    @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En cours</span>
                    @endif
                </p>
            </div>
            @if($reunion->note)
            <div class="mt-4 md:mt-0 md:ml-6 p-4 bg-gray-50 rounded-lg border border-gray-200 shadow-sm flex-grow">
                <p class="text-sm font-medium text-gray-700 mb-1">Note de la réunion:</p>
                <p class="text-gray-600 text-sm italic">{{ $reunion->note }}</p>
            </div>
            @endif
        </div>

        {{-- Section de la table des présences --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h4 class="text-2xl font-semibold text-gray-800">Liste des présences</h4>
            </div>
            <div class="p-6">
                @php
                    // Filtre les présences pour n'afficher que les stagiaires actifs (statut_id != '2')
                    $activePresences = collect($presences)->filter(function($presence) {
                        return !(isset($presence['stagiaire']->statut_id) && $presence['stagiaire']->statut_id == '2');
                    });
                @endphp

                @if($activePresences->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg font-medium">Aucun stagiaire actif trouvé pour ce groupe.</p>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-6 sm:mx-0">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Nom du Stagiaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Note</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Validé par</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($activePresences as $presence)
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out" id="row-{{ $presence['stagiaire']->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $presence['stagiaire']->nom }} {{ $presence['stagiaire']->prenom }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <select
                                                id="statut-{{ $presence['stagiaire']->id }}"
                                                name="statut"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm py-2 px-3"
                                                onchange="updatePresence({{ $presence['stagiaire']->id }}, '{{ $reunion->id }}')"
                                                @if($reunion->status) disabled @endif {{-- Désactiver si réunion clôturée --}}
                                            >
                                                <option value="Présent" @selected(isset($presence['absence']) && $presence['absence']->statut === 'Présent')>Présent</option>
                                                <option value="Assisté" @selected(isset($presence['absence']) && $presence['absence']->statut === 'Assisté')>Assisté</option>
                                                <option value="Absent" @selected(isset($presence['absence']) && $presence['absence']->statut === 'Absent')>Absent</option>
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <input
                                                type="text"
                                                id="note-{{ $presence['stagiaire']->id }}"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm py-2 px-3"
                                                value="{{ $presence['absence']->note ?? '' }}"
                                                placeholder="Ajouter une note..."
                                                onblur="updateNote({{ $presence['stagiaire']->id }}, '{{ $reunion->id }}')"
                                                @if($reunion->status) disabled @endif {{-- Désactiver si réunion clôturée --}}
                                            >
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            @if(isset($presence['absence']) && $presence['absence']->valideur)
                                                {{ $presence['absence']->valideur->nom }}
                                            @else
                                                <span class="text-gray-400 italic">Non défini</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            {{-- Bouton de clôture de réunion --}}
            <div class="p-6 border-t border-gray-200 flex justify-end bg-gray-50">
                @if(!$reunion->status) {{-- Afficher le bouton seulement si la réunion n'est pas clôturée --}}
                    <form action="{{ route('reunions.cloture', $reunion->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir clôturer cette réunion ? ');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 rounded-lg bg-red-600 text-white font-semibold shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                            <i class="bi bi-x-circle-fill mr-2"></i> Clôturer la réunion
                        </button>
                    </form>
                @else
                    <p class="text-green-600 font-semibold text-lg">Cette réunion est clôturée.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Mise à jour de présence (statut)
    function updatePresence(stagiaireId, reunionId) {
        const statut = document.getElementById('statut-' + stagiaireId).value;
        const note = document.getElementById('note-' + stagiaireId).value;
        const row = document.getElementById('row-' + stagiaireId);

        fetch(`/reunions/${reunionId}/presence/${stagiaireId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ statut, note })
        }).then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                // Feedback visuel léger sur la ligne
                row.classList.add('bg-blue-50', 'ring-1', 'ring-blue-300');
                setTimeout(() => {
                    row.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-300');
                }, 1000);
            } else {
                alert(data.message || 'Erreur lors de la mise à jour de la présence.');
            }
        }).catch(error => {
            console.error('Erreur :', error);
            alert(error.message || 'Erreur serveur lors de la mise à jour.');
        });
    }

    // Mise à jour de note
    function updateNote(stagiaireId, reunionId) {
        const statut = document.getElementById('statut-' + stagiaireId).value;
        const note = document.getElementById('note-' + stagiaireId).value;
        const row = document.getElementById('row-' + stagiaireId);

        fetch(`/reunions/${reunionId}/presence/${stagiaireId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ statut, note })
        }).then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        }).then(data => {
            if (data.success) {
                // Feedback visuel léger sur la ligne
                row.classList.add('bg-blue-50', 'ring-1', 'ring-blue-300');
                setTimeout(() => {
                    row.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-300');
                }, 1000);
            } else {
                alert(data.message || 'Erreur lors de la mise à jour de la note.');
            }
        }).catch(error => {
            console.error('Erreur :', error);
            alert(error.message || 'Erreur serveur lors de la mise à jour.');
        });
    }
</script>
@endsection