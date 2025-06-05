@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Mes Fichiers</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('fichiers.create') }}" class="btn btn-primary">Téléverser un nouveau fichier</a>
    </div>

    @if ($fichiers->isEmpty())
        <p>Vous n'avez pas encore téléversé de fichiers.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom du Fichier</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Téléversé par</th>
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
                            <td class="py-4 px-4">{{ $fichier->televerseur->prenom }} {{ $fichier->televerseur->nom }}</td>
                            <td class="py-4 px-4">{{ $fichier->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <a href="{{ route('fichiers.download', $fichier) }}" class="text-blue-600 hover:text-blue-900 mr-2">Télécharger</a>
                                @if (Auth::user()->id === $fichier->id_stagiaire && $fichier->peut_modifier)
                                    <a href="{{ route('fichiers.edit', $fichier) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Modifier</a>
                                @endif
                                @if (Auth::user()->id === $fichier->id_stagiaire && $fichier->peut_supprimer)
                                    <form action="{{ route('fichiers.destroy', $fichier) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection