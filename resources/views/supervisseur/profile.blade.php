@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Profil Superviseur</h2>

    @if(Session::has('success'))
        <div class="alert alert-success text-center">{{ Session::get('success') }}</div>
    @endif

    {{--  Conteneur centré avec largeur réduite --}}
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-xl border border-gray-200 p-6">

        {{--  Informations du superviseur --}}
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

        {{--  Formulaire de mise à jour du profil --}}
        @if(Auth::user()->id == $user->id)
            <form id="form_edit_profile" action="{{ route('superviseur.profile.update', $user->id) }}" method="POST">
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

                
<h4 class="text-lg font-medium text-gray-700 mb-3">Modifier mon mot de passe</h4>

<div class="mb-3">
    <label>Mot de passe actuel :</label>
    <input type="password" name="current_password" class="form-control">
</div>

<div class="mb-3">
    <label>Nouveau mot de passe :</label>
    <input type="password" name="new_password" class="form-control">
</div>

<div class="mb-3">
    <label>Confirmer le nouveau mot de passe :</label>
    <input type="password" name="new_password_confirmation" class="form-control">
</div>

  @if(Session::get('password_changed'))
    <div class="alert alert-success mt-3">Mot de passe modifié avec succès.</div>
@endif
         
                <button type="submit" class="btn btn-success w-full">Mettre à jour le profil</button>
            </form>
        @endif
    </div>
</div>
@endsection
