@extends('layouts.app')

@section('content')
    <h1>Liste des Groupes</h1>
    <a href="{{ route('groupes.create') }}" class="btn btn-primary">Ajouter un Groupe</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupes as $groupe)
                <tr>
                    <td>{{ $groupe->code }}</td>
                    <td>{{ $groupe->nom }}</td>
                    <td>{{ $groupe->description }}</td>
                    <td>{{ $groupe->date }}</td>
                    <td>
                        <a href="{{ route('groupes.edit', $groupe) }}" class="btn btn-warning">Modifier</a>
                        <form action="{{ route('groupes.destroy', $groupe) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce groupe ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
