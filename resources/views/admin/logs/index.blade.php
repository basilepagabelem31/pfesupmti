@extends('layout.default')

@section('title', 'Logs du Système')

@section('content')
    <div class="container-fluid py-4">
        <h1 class="mb-4">Logs du Système</h1>

        {{-- Formulaire de filtres --}}
        <form action="{{ route('admin.logs.index') }}" method="GET" class="mb-4 bg-white p-4 rounded shadow-sm">
            <div class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-4">
                    <label for="search" class="form-label visually-hidden">Rechercher par titre/objet</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher par titre/objet" value="{{ request('search') }}">
                </div>
                {{-- Un seul champ de date pour le filtrage (Date de début seulement) --}}
                <div class="col-md-3 col-lg-3">
                    <label for="start_date" class="form-label visually-hidden">Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync-alt me-1"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Utilisateur</th>
                                <th>Titre</th>
                                <th>Objet</th>
                                {{-- Suppression de la colonne 'Créé le' de l'en-tête --}}
                                {{-- <th>Créé le</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    {{-- Formatage de la date du log --}}
                                    <td>{{ $log->date ? \Carbon\Carbon::parse($log->date)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                    <td>
                                        @if ($log->user)
                                            {{ $log->user->prenom }} {{ $log->user->nom }} (ID: {{ $log->user->id }})
                                        @else
                                            Utilisateur inconnu (ID: {{ $log->user_id ?? 'N/A' }})
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $icon = '';
                                            switch (trim($log->titre)) {
                                                case 'Création de compte utilisateur':
                                                    $icon = 'fas fa-user-plus';
                                                    break;
                                                case 'Mise à jour profil superviseur':
                                                    $icon = 'fas fa-user-edit';
                                                    break;
                                                case 'Mise à jour profil stagiaire':
                                                    $icon = 'fas fa-user-edit';
                                                    break;
                                                case 'Suppression de fichier':
                                                    $icon = 'fas fa-trash';
                                                    break;
                                                case "Téléversement d'un fichier":
                                                case "Téléversement d’un fichier":
                                                    $icon = 'fas fa-file-upload';
                                                    break;
                                                case 'Connexion utilisateur':
                                                    $icon = 'fas fa-sign-in-alt';
                                                    break;
                                                case 'Déconnexion utilisateur':
                                                    $icon = 'fas fa-sign-out-alt';
                                                    break;
                                                default:
                                                    $icon = 'fas fa-info-circle';
                                                    break;
                                            }
                                        @endphp
                                        <span style="display: inline-block; white-space: nowrap; color: #333 !important; font-size: 1rem !important; opacity: 1 !important; visibility: visible !important;">
                                            <i class="{{ $icon }} me-2 text-info" style="color: #0d6efd !important; font-size: 1rem !important;"></i>
                                            {{ $log->titre }}
                                        </span>
                                    </td>
                                    <td>
                                        {!! \Illuminate\Support\Str::replaceFirst('Changements:', '<strong>Changements:</strong><br>', $log->object) !!}
                                    </td>
                                    {{-- Suppression de la cellule 'Créé le' du corps du tableau --}}
                                    {{-- <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    {{-- colspan est maintenant 4 car il y a 4 colonnes restantes (Date, Utilisateur, Titre, Objet) --}}
                                    <td colspan="4" class="text-center py-4">
                                        <div class="alert alert-info mb-0" role="alert">
                                            <i class="fas fa-info-circle me-2"></i> Aucun log trouvé pour les critères de recherche actuels.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Incluez Font Awesome pour les icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush