@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Profil Administrateur</h2>

    @if(Session::has('success'))
        <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
    @endif

    {{-- ✅ Conteneur centré avec largeur réduite --}}
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-xl border border-gray-200 p-6">

        {{-- ✅ Informations de l'Administrateur --}}
        <div class="mb-6 text-gray-700">
            <p><strong>Nom :</strong> {{ Auth::user()->nom }}</p>
            <p><strong>Prénom :</strong> {{ Auth::user()->prenom }}</p>
            <p><strong>Email :</strong> {{ Auth::user()->email }}</p>
            <p><strong>Téléphone :</strong> {{ Auth::user()->telephone ?? 'Non renseigné' }}</p>
            <p><strong>CIN :</strong> {{ Auth::user()->cin ?? 'Non renseigné' }}</p>
            <p><strong>Adresse :</strong> {{ Auth::user()->adresse ?? 'Non renseigné' }}</p>
            <p><strong>Ville :</strong> {{ Auth::user()->ville->nom ?? 'Non renseigné' }}</p>
            <p><strong>Pays :</strong> {{ Auth::user()->pays->nom ?? 'Non renseigné' }}</p>
        </div>

        {{-- ✅ Formulaire de mise à jour du profil --}}
        <form id="form_edit_profile" action="{{ route('admin.profile.update', $user->id) }}" method="POST">
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
                <input type="text" name="cin" class="form-control" value="{{ old('cin', $user->cin) }}" required>
            </div>
            <div class="mb-3">
                <label>Adresse :</label>
                <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $user->adresse) }}">
            </div>

            {{-- ✅ Sélection dynamique des pays et villes --}}
            <div class="mb-3">
                <label>Pays :</label>
                <select name="pays_id" class="form-select" required>
                    <option value="">Sélectionner un pays</option>
                    @foreach($pays as $p)
                        <option value="{{ $p->id }}" {{ old('pays_id', $user->pays_id) == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                 <label>Ville :</label>
                    <select name="ville_id" class="form-select" required>
                        <option value="">Sélectionner une ville</option>
                        @foreach($villes as $v)
                            <option value="{{ $v->id }}" {{ old('ville_id', $user->ville_id) == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>
                        @endforeach
                    </select>
            </div>

            <button type="submit" class="btn btn-success w-full">Mettre à jour le profil</button>
        </form>
    </div>
</div>

{{-- ✅ Script AJAX pour charger les villes dynamiquement --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch('/api/villes')
    .then(response => response.json())
    .then(data => {
        let villeSelect = document.getElementById("ville-select");
        villeSelect.innerHTML = '<option value="">Sélectionner une ville</option>'; // Reset options
        data.forEach(ville => {
            let option = document.createElement("option");
            option.value = ville.id;
            option.textContent = ville.nom;
            villeSelect.appendChild(option);
        });
    })
    .catch(error => console.error("Erreur lors du chargement des villes :", error));
});
</script>

@endsection
