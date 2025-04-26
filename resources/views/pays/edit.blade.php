@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2>Modifier un Pays</h2>

    <form action="{{ route('pays.update', $pay->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Champ Code -->
        <div class="form-group mb-3">
            <label class="form-label" for="code">Code du Pays</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $pay->code }}" required>
        </div>

        <!-- Champ Nom -->
        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom du Pays</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ $pay->nom }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="{{ route('pays.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
