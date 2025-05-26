@extends('layouts.app')

@section('content')
    <h1>Modifier le Groupe</h1>
    <form action="{{ route('groupes.update', $groupe) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Code :</label>
            <input type="text" name="code" class="form-control" value="{{ $groupe->code }}" required>
        </div>
        <div class="mb-3">
            <label>Nom :</label>
            <input type="text" name="nom" class="form-control" value="{{ $groupe->nom }}" required>
        </div>
        <div class="mb-3">
            <label>Description :</label>
            <textarea name="description" class="form-control">{{ $groupe->description }}</textarea>
        </div>
        <div class="mb-3">
            <label>Date :</label>
            <input type="date" name="date" class="form-control" value="{{ $groupe->date }}" required>
        </div>
        <button type="submit" class="btn btn-warning">Mettre à jour</button>
    </form>
@endsection
