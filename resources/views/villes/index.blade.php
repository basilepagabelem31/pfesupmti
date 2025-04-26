@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des Villes</h2>

    <div class="mb-3 ">
        <a href="{{ route('villes.create') }}" class="btn btn-success">Ajouter une nouvelle ville</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Pays</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villes as $ville)
                        <tr>
                            <td>{{ $ville->code }}</td>
                            <td>{{ $ville->nom }}</td>
                            <td>{{ $ville->pays->nom ?? 'Non attribué' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('villes.edit', $ville->id) }}" class="btn btn-sm btn-warning">Modifier</a>

                                    <form action="{{ route('villes.destroy', $ville->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ville ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucune ville disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
