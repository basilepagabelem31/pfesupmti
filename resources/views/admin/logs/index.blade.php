{{-- resources/views/admin/logs/index.blade.php --}}

@extends('layout.default') {{-- Assurez-vous que c'est votre layout admin --}}

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-5xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Journaux d'<span class="text-blue-600">Activités</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Consultez l'historique des actions des utilisateurs sur la plateforme.
            </p>

            {{-- Formulaire de filtre --}}
            <div class="bg-white shadow-lg rounded-xl p-6 mb-8 border border-gray-100">
                <h3 class="text-2xl font-semibold text-gray-800 mb-5 border-b pb-4">Filtrer les journaux</h3>
                <form action="{{ route('admin.logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-6 items-end">
                    <div class="md:col-span-2">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Utilisateur</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <option value="">Tous les utilisateurs</option>
                            @foreach($usersForFilter as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->nom }} {{ $user->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                        <select name="action" id="action" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <option value="">Toutes les actions</option>
                            @foreach($actionsForFilter as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ Str::replace('_', ' ', Str::title($action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" name="start_date" id="start_date" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" value="{{ request('start_date') }}">
                    </div>
                    <div class="md:col-span-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" name="end_date" id="end_date" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out" value="{{ request('end_date') }}">
                    </div>
                    
                    {{-- Section des boutons de filtre et réinitialisation, alignés à droite --}}
                    <div class="md:col-span-1 flex justify-end space-x-3"> {{-- Ajout de justify-end et space-x-3 --}}
                     
                    </div>
                       <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider">Filtrer</button>
                        <a href="{{ route('admin.logs.index') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out uppercase tracking-wider">Réinitialiser</a>
                </form>
            </div>

            {{-- Tableau des journaux d'activités --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">ID Log</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Utilisateur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Message</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $log->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($log->user)
                                    <span class="font-medium">{{ $log->user->nom }} {{ $log->user->prenom }}</span> (ID: <span class="text-gray-500">{{ $log->user->id }}</span>)
                                @else
                                    <span class="text-gray-500 italic">Utilisateur Inconnu</span> (ID: <span class="text-gray-500">{{ $log->user_id }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ Str::replace('_', ' ', Str::title($log->action)) }}</td>
                            <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-700" title="{{ $log->log_message }}">{{ $log->log_message }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                @if($log->object_snapshot)
                                    {{-- Bouton pour ouvrir la modale Bootstrap pour les détails --}}
                                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 transition duration-150 ease-in-out uppercase tracking-wider"
                                        data-bs-toggle="modal" data-bs-target="#logDetailsModal"
                                        onclick="openLogDetailsModal({{ json_encode($log->object_snapshot, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }})">
                                        Voir détails
                                    </button>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500 italic">Aucun log trouvé pour les critères sélectionnés.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $logs->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

{{-- Modal pour afficher les détails du log (snapshot JSON) --}}
<div class="modal fade" id="logDetailsModal" tabindex="-1" aria-labelledby="logDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Modale plus large pour le JSON --}}
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="logDetailsModalLabel">Détails du Log</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-6">
                <p class="text-gray-700 mb-4">Voici le snapshot de l'objet au moment de l'action :</p>
                <div class="bg-gray-50 rounded-lg p-4 font-mono text-xs text-gray-800 overflow-auto max-h-96 border border-gray-200 shadow-inner">
                    <pre id="logSnapshotContent" class="whitespace-pre-wrap break-words"></pre>
                </div>
            </div>
            <div class="modal-footer flex justify-end space-x-4 mt-8 border-t border-gray-100 pt-4">
                <button type="button" class="py-3 px-4 rounded-lg text-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('my_js')
<script>
    // Déclare une variable pour stocker l'instance du modal.
    // Cela empêche de créer plusieurs instances du même modal, ce qui peut causer des problèmes de backdrop.
    let logDetailsModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        // Obtenez l'élément du modal
        const modalElement = document.getElementById('logDetailsModal');
        
        // Crée une seule instance du modal Bootstrap une fois que le DOM est chargé
        logDetailsModalInstance = new bootstrap.Modal(modalElement);

        // Cette fonction est appelée par le bouton "Voir détails"
        window.openLogDetailsModal = function(snapshot) {
            // Formate le JSON pour un affichage propre
            const formattedSnapshot = JSON.stringify(snapshot, null, 2);
            document.getElementById('logSnapshotContent').textContent = formattedSnapshot;

            // Utilise l'instance existante pour afficher le modal
            logDetailsModalInstance.show();
        };

        // Supprime l'ancien écouteur si il existait, car il est géré par l'instance unique
        // Si le problème persistait avec un backdrop après ces changements, 
        // c'est qu'il y a un conflit externe plus profond ou un problème d'environnement
        // que Bootstrap ne peut pas gérer seul. Mais dans la plupart des cas, cette approche est suffisante.
    });
</script>
@endsection
