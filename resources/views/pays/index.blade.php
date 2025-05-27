@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des Pays</h2>

    <div class="mb-3 text-end">
        <a href="{{ route('pays.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Ajouter un nouveau pays
        </a>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Formulaire de recherche --}}
    <form method="GET" action="{{ route('pays.index') }}" class="row g-2 mb-4">
        <div class="col-md-10">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                placeholder="Rechercher un pays par nom ou code...">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Rechercher
            </button>
        </div>
    </form>

    {{-- Table des pays --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
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
                            <td>{{ $pay->code ?? '-' }}</td>
                            <td>{{ $pay->nom }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('pays.edit', $pay->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                    <form action="{{ route('pays.destroy', $pay->id) }}" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pays ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Aucun pays trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination Bootstrap --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $pays->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
