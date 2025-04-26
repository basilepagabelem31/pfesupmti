@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des Pays</h2>
    <div class="mb-3">
        <a href="{{ route('pays.create') }}" class="btn btn-success">Ajouter un nouveau pays</a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pays as $pay)
                        <tr>
                            <td>{{ $pay->id }}</td>
                            <td>{{ $pay->code }}</td>
                            <td>{{ $pay->nom }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('pays.edit', $pay->id) }}" class="btn btn-sm btn-warning">Modifier</a>

                                    <form action="{{ route('pays.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pays ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun pays disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
