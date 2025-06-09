@extends('layout.default')
@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Modifier le Fichier : {{ $fichier->nom_fichier }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('fichiers.update', $fichier) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT') {{-- Indique que c'est une requête PUT --}}

        <div class="mb-4">
            <label for="nom_fichier" class="block text-gray-700 text-sm font-bold mb-2">Nom du Fichier :</label>
            <input type="text" name="nom_fichier" id="nom_fichier" class="form-input w-full" value="{{ old('nom_fichier', $fichier->nom_fichier) }}" required>
            @error('nom_fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description :</label>
            <textarea name="description" id="description" rows="3" class="form-textarea w-full">{{ old('description', $fichier->description) }}</textarea>
            @error('description')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="fichier" class="block text-gray-700 text-sm font-bold mb-2">Nouveau Fichier (Optionnel) :</label>
            <input type="file" name="fichier" id="fichier" class="form-input w-full">
            <p class="text-gray-600 text-sm mt-1">Laissez vide si vous ne souhaitez pas changer le fichier actuel.</p>
            @error('fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="type_fichier" class="block text-gray-700 text-sm font-bold mb-2">Type de Fichier :</label>
            <select name="type_fichier" id="type_fichier" class="form-select w-full" required>
                <option value="">Sélectionner un type</option>
                <option value="convention" {{ old('type_fichier', $fichier->type_fichier) == 'convention' ? 'selected' : '' }}>Convention</option>
                <option value="rapport" {{ old('type_fichier', $fichier->type_fichier) == 'rapport' ? 'selected' : '' }}>Rapport</option>
                <option value="attestation" {{ old('type_fichier', $fichier->type_fichier) == 'attestation' ? 'selected' : '' }}>Attestation</option>
                <option value="autre" {{ old('type_fichier', $fichier->type_fichier) == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
            @error('type_fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="sujet_id" class="block text-gray-700 text-sm font-bold mb-2">Sujet Associé (Optionnel) :</label>
            <select name="sujet_id" id="sujet_id" class="form-select w-full">
                <option value="">Aucun sujet</option>
                @foreach ($sujets as $sujet)
                    <option value="{{ $sujet->id }}" {{ old('sujet_id', $fichier->sujet_id) == $sujet->id ? 'selected' : '' }}>{{ $sujet->titre }}</option>
                @endforeach
            </select>
            @error('sujet_id')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        {{-- Afficher les permissions seulement pour les Superviseurs/Administrateurs --}}
        @if (Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Permissions pour le Stagiaire :</label>
                <div class="flex items-center">
                    {{-- CHAMP CACHÉ AJOUTÉ ICI --}}
                    <input type="hidden" name="peut_modifier" value="0">
                    <input type="checkbox" name="peut_modifier" id="peut_modifier" class="form-checkbox h-5 w-5 text-indigo-600" {{ old('peut_modifier', $fichier->peut_modifier) ? 'checked' : '' }}>
                    <label for="peut_modifier" class="ml-2 text-gray-700">Peut Modifier</label>
                </div>
                <div class="flex items-center mt-2">
                    {{-- CHAMP CACHÉ AJOUTÉ ICI --}}
                    <input type="hidden" name="peut_supprimer" value="0">
                    <input type="checkbox" name="peut_supprimer" id="peut_supprimer" class="form-checkbox h-5 w-5 text-indigo-600" {{ old('peut_supprimer', $fichier->peut_supprimer) ? 'checked' : '' }}>
                    <label for="peut_supprimer" class="ml-2 text-gray-700">Peut Supprimer</label>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="btn btn-primary">Mettre à Jour Fichier</button>
            <a href="{{ route('fichiers.index') }}" class="btn btn-secondary ml-4">Annuler</a>
        </div>
    </form>
</div>
@endsection
