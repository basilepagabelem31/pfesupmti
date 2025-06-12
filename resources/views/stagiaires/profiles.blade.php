@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Mon Profil</h2>

    @if(Session::has('success'))
        <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
    @endif

    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-xl border border-gray-200 p-6">

        {{-- ✅ Informations actuelles --}}
<div class="mb-6 text-gray-700">
    <p><strong>Nom :</strong> {{ $user->nom }}</p>
    <p><strong>Prénom :</strong> {{ $user->prenom }}</p>
    <p><strong>Email :</strong> {{ $user->email }}</p>
    <p><strong>Téléphone :</strong> {{ $user->telephone ?? 'Non renseigné' }}</p>
    <p><strong>CIN :</strong> {{ $user->cin ?? 'Non renseigné' }}</p>
    <p><strong>Adresse :</strong> {{ $user->adresse ?? 'Non renseigné' }}</p>
    <p><strong>Ville :</strong> {{ $user->ville->nom ?? 'Non renseigné' }}</p>
    <p><strong>Pays :</strong> {{ $user->pays->nom ?? 'Non renseigné' }}</p>
    <p><strong>Université :</strong> {{ $user->universite ?? 'Non renseignée' }}</p>
    <p><strong>Faculté :</strong> {{ $user->faculte ?? 'Non renseignée' }}</p>
    <p><strong>Titre de formation :</strong> {{ $user->formation ?? 'Non renseigné' }}</p>
</div>

{{-- ✅ Section : Notes --}}
@if($user->notes->isEmpty())
    <div class="alert alert-info text-center">Aucune note pour le moment.</div>
@else
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Mes Notes</h3>
        <ul class="list-disc list-inside text-gray-700">
            @foreach($user->notes as $note)
                <li>{{ $note->contenu }} <small class="text-gray-500">({{ $note->created_at->format('d/m/Y') }})</small></li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ✅ Section : Fichiers --}}
@if($user->fichiers->isEmpty())
    <div class="alert alert-info text-center">Aucun fichier disponible.</div>
@else
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Mes Fichiers</h3>
        <ul class="list-disc list-inside text-gray-700">
            @foreach($user->fichiers as $fichier)
                <li>
                    <a href="{{ asset('storage/' . $fichier->chemin) }}" target="_blank" class="text-blue-600 hover:underline">
                        {{ $fichier->titre }}
                    </a>
                    <small class="text-gray-500">({{ $fichier->created_at->format('d/m/Y') }})</small>
                </li>
            @endforeach
        </ul>
    </div>
@endif


        {{-- ✅ Formulaire de modification --}}
        <form method="POST" action="{{ route('stagiaires.profiles.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nom :</label>
                <input type="text" name="nom" class="form-control" value="{{ old('nom', $user->nom) }}" required>
            </div>
            <div class="mb-3">
                <label>Prénom :</label>
                <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $user->prenom) }}" required>
            </div>
            <div class="mb-3">
                <label>Email :</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="mb-3">
                <label>Téléphone :</label>
                <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->telephone) }}">
            </div>
            <div class="mb-3">
                <label>CIN :</label>
                <input type="text" name="cin" class="form-control" value="{{ old('cin', $user->cin) }}">
            </div>
            <div class="mb-3">
                <label>Adresse :</label>
                <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $user->adresse) }}">
            </div>
            <div class="mb-3">
                <label>Pays :</label>
                <select name="pays_id" class="form-select">
                    <option value="">Sélectionner un pays</option>
                    @foreach($pays as $p)
                        <option value="{{ $p->id }}" {{ old('pays_id', $user->pays_id) == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Ville :</label>
                <select name="ville_id" class="form-select">
                    <option value="">Sélectionner une ville</option>
                    @foreach($villes as $v)
                        <option value="{{ $v->id }}" {{ old('ville_id', $user->ville_id) == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success w-full">Mettre à jour mon profil</button>
        </form>
    </div>
</div>
@endsection
