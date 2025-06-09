@extends('layout.default')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-4xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Gestion des <span class="text-blue-600">Rôles</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Gérez les différents rôles d'utilisateurs de votre application.
            </p>

            {{-- Bouton Ajouter un nouveau rôle --}}
            <div class="mb-8 text-center">
                <button type="button" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out uppercase tracking-wider" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter un nouveau rôle
                </button>
            </div>

            {{-- Messages de session --}}
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Tableau des Rôles --}}
            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-50"> {{-- Nouvelle couleur pour l'en-tête du tableau --}}
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-blue-800 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($roles as $role)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $role->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $role->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $role->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" class="px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out" data-bs-toggle="modal" data-bs-target="#editRoleModal-{{ $role->id }}">Modifier</button>

                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun rôle disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Modal Ajouter un rôle -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="addRoleModalLabel">Ajouter un nouveau rôle</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="add_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle</label>
                        <input type="text" id="add_nom" name="nom" required
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="add_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="add_description" name="description" rows="3"
                                  class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
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

<!-- Modales Modifier un rôle (une par rôle) -->
@foreach($roles as $role)
<div class="modal fade" id="editRoleModal-{{ $role->id }}" tabindex="-1" aria-labelledby="editRoleModalLabel-{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white rounded-3xl shadow-2xl p-8 md:p-10 border border-gray-200">
            <div class="modal-header border-b border-gray-100 pb-4 mb-6">
                <h5 class="modal-title text-3xl font-extrabold text-gray-900 text-center flex-grow" id="editRoleModalLabel-{{ $role->id }}">Modifier le rôle : {{ $role->nom }}</h5>
                <button type="button" class="btn-close text-gray-400 hover:text-gray-600" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('roles.update', $role->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_nom_{{ $role->id }}" class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle</label>
                        <input type="text" id="edit_nom_{{ $role->id }}" name="nom" value="{{ old('nom', $role->nom) }}" required
                               class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="edit_description_{{ $role->id }}" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="edit_description_{{ $role->id }}" name="description" rows="3"
                                  class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">{{ old('description', $role->description) }}</textarea>
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

@section('my_js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Gérer les erreurs de validation pour les modales
        // Si des erreurs existent après une soumission de formulaire (par exemple, nom déjà pris)
        // et qu'elles concernent le formulaire d'ajout, rouvrir la modale d'ajout.
        @if ($errors->any())
            var addModalErrors = false;
            // Vérifiez si les erreurs sont liées à l'ajout (vous devrez affiner cette logique
            // si les noms des champs sont identiques entre ajout et modification)
            @foreach ($errors->keys() as $key)
                if (['nom', 'description'].includes('{{ $key }}') && !'{{ Str::startsWith($key, 'edit_') }}') {
                    addModalErrors = true;
                    break;
                }
            @endforeach

            if (addModalErrors) {
                var addModal = new bootstrap.Modal(document.getElementById('addRoleModal'));
                addModal.show();
            } else {
                // Tenter de rouvrir la modale d'édition si les erreurs ne sont pas pour l'ajout
                @foreach ($roles as $role)
                    @if ($errors->has('edit_nom_' . $role->id) || $errors->has('edit_description_' . $role->id))
                        var editModal = new bootstrap.Modal(document.getElementById('editRoleModal-{{ $role->id }}'));
                        editModal.show();
                        break;
                    @endif
                @endforeach
            }
        @endif
    });
</script>
@endsection
