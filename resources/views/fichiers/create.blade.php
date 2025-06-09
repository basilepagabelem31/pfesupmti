@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Téléverser un Nouveau Fichier</h1>

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

    <form action="{{ route('fichiers.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf

        @if (Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
            <div class="mb-4">
                <label for="id_stagiaire" class="block text-gray-700 text-sm font-bold mb-2">Stagiaire :</label>
                <select name="id_stagiaire" id="id_stagiaire" class="form-select w-full" required>
                    <option value="">Sélectionner un stagiaire</option>
                    @foreach ($stagiaires as $s)
                        <option value="{{ $s->id }}" {{ old('id_stagiaire', $stagiaire ? $stagiaire->id : '') == $s->id ? 'selected' : '' }}>
                            {{ $s->nom }} {{ $s->prenom }} ({{ $s->email }})
                        </option>
                    @endforeach
                </select>
                @error('id_stagiaire')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
            </div>
        @else
            {{-- Pour les stagiaires, leur ID est automatiquement défini par Auth::id() dans le contrôleur --}}
            <input type="hidden" name="id_stagiaire" value="{{ Auth::id() }}">
        @endif

        <div class="mb-4">
            <label for="nom_fichier" class="block text-gray-700 text-sm font-bold mb-2">Nom du Fichier :</label>
            <input type="text" name="nom_fichier" id="nom_fichier" class="form-input w-full" value="{{ old('nom_fichier') }}" required>
            @error('nom_fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description :</label>
            <textarea name="description" id="description" rows="3" class="form-textarea w-full">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="fichier" class="block text-gray-700 text-sm font-bold mb-2">Fichier :</label>
            <input type="file" name="fichier" id="fichier" class="form-input w-full" required>
            @error('fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="type_fichier" class="block text-gray-700 text-sm font-bold mb-2">Type de Fichier :</label>
            <select name="type_fichier" id="type_fichier" class="form-select w-full" required>
                <option value="">Sélectionner un type</option>
                <option value="convention" {{ old('type_fichier') == 'convention' ? 'selected' : '' }}>Convention</option>
                <option value="rapport" {{ old('type_fichier') == 'rapport' ? 'selected' : '' }}>Rapport</option>
                <option value="attestation" {{ old('type_fichier') == 'attestation' ? 'selected' : '' }}>Attestation</option>
                <option value="autre" {{ old('type_fichier') == 'autre' ? 'selected' : '' }}>Autre</option>
            </select>
            @error('type_fichier')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="sujet_id" class="block text-gray-700 text-sm font-bold mb-2">Sujet Associé (Optionnel) :</label>
            <select name="sujet_id" id="sujet_id" class="form-select w-full">
                <option value="">Aucun sujet</option>
                @foreach ($sujets as $sujet)
                    <option value="{{ $sujet->id }}" {{ old('sujet_id') == $sujet->id ? 'selected' : '' }}>{{ $sujet->titre }}</option>
                @endforeach
            </select>
            @error('sujet_id')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
        </div>

        @if (Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Permissions pour le Stagiaire :</label>
                <div class="flex items-center">
                    {{-- REMOVED 'checked' attribute so it defaults to unchecked --}}
                    <input type="checkbox" name="peut_modifier" id="peut_modifier" class="form-checkbox h-5 w-5 text-indigo-600"> 
                    <label for="peut_modifier" class="ml-2 text-gray-700">Peut Modifier</label>
                </div>
                <div class="flex items-center mt-2">
                    {{-- REMOVED 'checked' attribute so it defaults to unchecked --}}
                    <input type="checkbox" name="peut_supprimer" id="peut_supprimer" class="form-checkbox h-5 w-5 text-indigo-600">
                    <label for="peut_supprimer" class="ml-2 text-gray-700">Peut Supprimer</label>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="btn btn-primary">Téléverser Fichier</button>
            <a href="{{ route('fichiers.index') }}" class="btn btn-secondary ml-4">Annuler</a>
        </div>
    </form>
</div>
@endsection
