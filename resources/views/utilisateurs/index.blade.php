@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Liste des Superviseurs</h2>
    <div class="mb-3">
        <a href="{{ route('utilisateurs.create') }}" class="btn btn-success">Ajouter un nouveau Utilisateur</a>
    </div>
   

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card custom-card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0" style="color:white; font-weight:bold">Superviseurs disponibles</h5>
        </div>

     

        <div class="card-body">
            <!-- Barre de progression si la liste est trop longue -->
            <div class="progress mb-4" style="height: 12px;">
                <div class="progress-bar" role="progressbar" style="width: 80%;" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover custom-table mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>CIN</th>
                            <th>Code</th>
                            <th>Adresse</th>
                            <th>Pays</th>
                            <th>Ville</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Email Log</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utilisateurs as $utilisateur)
                            <tr>
                                <td>{{ $utilisateur->id }}</td>
                                <td>{{ $utilisateur->nom }} {{ $utilisateur->prenom }}</td>
                                <td>{{ $utilisateur->email }}</td>
                                <td>{{ $utilisateur->telephone }}</td>
                                <td>{{ $utilisateur->cin }}</td>
                                <td>{{ $utilisateur->code }}</td>
                                <td>{{ $utilisateur->adresse }}</td>
                                <td>{{ $utilisateur->pays->nom }}</td>
                                <td>{{ $utilisateur->ville->nom }}</td>
                                <td>{{ $utilisateur->role->nom }}</td>
                                <td>{{ $utilisateur->statut->nom }}</td>
                                <td>{{ $utilisateur->emailLog->statut }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('utilisateurs.edit', $utilisateur->id) }}" class="btn btn-warning btn-sm">Modifier</a>

                                        <form action="{{ route('utilisateurs.destroy', $utilisateur->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce superviseur ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">Aucun superviseur disponible.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Style de la carte */
    .custom-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .custom-card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        background-color: #343a40;
        color: white;
    }

    /* Style de la barre de progression */
    .progress {
        background-color: #f1f1f1;
    }

    .progress-bar {
        background-color: #28a745;
    }

    /* Style du bouton */
    .btn-custom {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }

    .btn-custom:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    /* Style du tableau */
    .custom-table th,
    .custom-table td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .custom-table th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-weight: bold;
    }

    .custom-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Réactiver le tableau pour petits écrans */
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endpush
