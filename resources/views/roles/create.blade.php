@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2>Ajouter un Rôle</h2>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf

        <!-- Champ Nom -->
        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom du Rôle</label>
            <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Administrateur, Utilisateur">
        </div>

        <!-- Champ Description -->
        <div class="form-group mb-3">
            <label class="form-label" for="description">Description du Rôle</label>
            <textarea class="form-control" id="description" name="description" placeholder="Décrire le rôle ici..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>
@endsection
