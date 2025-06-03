@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2>Modifier un Rôle</h2>

    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Champ Nom -->
        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom du Rôle</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ $role->nom }}" required>
        </div>

        <!-- Champ Description -->
        <div class="form-group mb-3">
            <label class="form-label" for="description">Description du Rôle</label>
            <textarea class="form-control" id="description" name="description" required>{{ $role->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
