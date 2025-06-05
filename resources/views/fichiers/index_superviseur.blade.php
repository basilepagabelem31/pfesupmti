@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gestion des Fichiers</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4 flex items-center space-x-4">
        <a href="{{ route('fichiers.create') }}" class="btn btn-primary">Téléverser un nouveau fichier</a>

        <form action="{{ route('fichiers.index') }}" method="GET" class="flex items-center space-x-2">
            <label for="filter_stagiaire_id" class="text-gray-700">Filtrer par Stagiaire:</label>
            <select name="stagiaire" id="filter_stagiaire_id" class="form-select rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">Tous les stagiaires</option>
                @foreach ($stagiaires as $s)
                    <option value="{{ $s->id }}" {{ $currentStagiaire && $currentStagiaire->id == $s->id ? 'selected' : '' }}>
                        {{ $s->nom }} {{ $s->prenom }} ({{ $s->email }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filtrer</button>
        </form>
    </div>

    @if ($currentStagiaire)
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Fichiers de {{ $currentStagiaire->prenom }} {{ $currentStagiaire->nom }}</h2>
    @endif

    @if ($fichiers->isEmpty())
        <p>Aucun fichier trouvé.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom du Fichier</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                        @if (!$currentStagiaire) {{-- N'affiche le stagiaire que si on n'est pas déjà sur sa page --}}
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Stagiaire</th>
                        @endif
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Téléversé par</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Modifiable</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Supprimable</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($fichiers as $fichier)
                        <tr>
                            <td class="py-4 px-4">{{ $fichier->nom_fichier }}</td>
                            <td class="py-4 px-4">{{ Str::limit($fichier->description, 50) }}</td>
                            <td class="py-4 px-4">{{ ucfirst($fichier->type_fichier) }}</td>
                            @if (!$currentStagiaire)
                            <td class="py-4 px-4">{{ $fichier->stagiaire->prenom }} {{ $fichier->stagiaire->nom }}</td>
                            @endif
                            <td class="py-4 px-4">{{ $fichier->televerseur->prenom }} {{ $fichier->televerseur->nom }}</td>
                            <td class="py-4 px-4">
                                <span class="badge {{ $fichier->peut_modifier ? 'bg-success' : 'bg-danger' }}">
                                    {{ $fichier->peut_modifier ? 'Oui' : 'Non' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="badge {{ $fichier->peut_supprimer ? 'bg-success' : 'bg-danger' }}">
                                    {{ $fichier->peut_supprimer ? 'Oui' : 'Non' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">{{ $fichier->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <a href="{{ route('fichiers.download', $fichier) }}" class="text-blue-600 hover:text-blue-900 mr-2">Télécharger</a>
                                <a href="{{ route('fichiers.edit', $fichier) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Modifier</a>
                                <form action="{{ route('fichiers.destroy', $fichier) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection