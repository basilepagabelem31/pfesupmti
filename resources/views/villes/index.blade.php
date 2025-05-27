@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des Villes</h2>

    {{-- Ajouter une nouvelle ville --}}
    <div class="mb-3 text-end">
        <a href="{{ route('villes.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Ajouter une nouvelle ville
        </a>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    {{-- Formulaire de recherche --}}
    <form method="GET" action="{{ route('villes.index') }}" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                   placeholder="Rechercher une ville...">
        </div>
        <div class="col-md-5">
            <select name="pays_id" class="form-select">
                <option value="">-- Tous les pays --</option>
                @foreach($pays as $p)
                    <option value="{{ $p->id }}" {{ request('pays_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filtrer
            </button>
        </div>
    </form>

    {{-- Table des villes --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
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
                                    <a href="{{ route('villes.edit', $ville->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                    <form action="{{ route('villes.destroy', $ville->id) }}" method="POST"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ville ?');">
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
                            <td colspan="4">Aucune ville disponible.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $villes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
