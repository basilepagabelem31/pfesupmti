@extends('layouts.app')

@section('content')
    <h1>Ajouter un Groupe</h1>
    <form action="{{ route('groupes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Code :</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description :</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label>Date :</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
    </form>
@endsection
