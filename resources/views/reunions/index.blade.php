@extends('layout.default')

@section('title', 'Réunions ')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" class="row g-3 align-items-end" action="{{ route('reunions.index') }}">
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" id="date" name="date" class="form-control"
                           value="{{ request('date', $date ?? now()->toDateString()) }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCreerReunion">
                <i class="bi bi-plus-circle me-2"></i>Nouvelle réunion
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="card-title mb-0">Liste des réunions</h4>
        </div>
        <div class="card-body">
            @if($reunions->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Aucune réunion prévue pour ce jour.</p>
                </div>
            @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Groupe</th>
                            <th>Date</th>
                            <th>Horaires</th>
                            <th>Note</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reunions as $reunion)
                            <tr>
                                <td>{{ $reunion->groupe->nom }}</td>
                                <td>{{ $reunion->date->format('d/m/Y') }}</td>
                                <td>{{ $reunion->heure_debut }} - {{ $reunion->heure_fin }}</td>
                                <td>{{ $reunion->note ?? '-' }}</td>
                                <td>
                                    @if($reunion->status)
                                        <span class="badge bg-success">Clôturée</span>
                                    @else
                                        <span class="badge bg-warning">En cours</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('reunions.show', $reunion->id) }}" class="btn btn-sm btn-primary">Présences</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<!-- Modal for creating a new meeting -->
<div class="modal fade" id="modalCreerReunion" tabindex="-1" aria-labelledby="modalCreerReunionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('reunions.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreerReunionLabel">Créer une Nouvelle Réunion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="groupe_id" class="form-label">Groupe</label>
                        <select id="groupe_id" name="groupe_id" class="form-select" required onchange="fetchStagiaires(this.value)">
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stagiaires" class="form-label">Stagiaires</label>
                        <ul id="stagiaires" class="list-group">
                            <!-- Les stagiaires liés au groupe seront affichés ici -->
                        </ul>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="heure_debut" class="form-label">Heure de Début</label>
                            <input type="time" id="heure_debut" name="heure_debut" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="heure_fin" class="form-label">Heure de Fin</label>
                            <input type="time" id="heure_fin" name="heure_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea id="note" name="note" class="form-control" rows="3" placeholder="Description de la réunion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Créer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
<script>
    function fetchStagiaires(groupeId) {
        if (!groupeId) {
            document.getElementById('stagiaires').innerHTML = '';
            return;
        }

        fetch(`/groupes/${groupeId}/stagiaires`)
            .then(response => response.json())
            .then(data => {
                const stagiairesList = document.getElementById('stagiaires');
                stagiairesList.innerHTML = '';

                if (data.length === 0) {
                    const li = document.createElement('li');
                    li.textContent = 'Aucun stagiaire trouvé.';
                    li.className = 'list-group-item text-muted';
                    stagiairesList.appendChild(li);
                } else {
                    data.forEach(stagiaire => {
                        const li = document.createElement('li');
                        li.textContent = `${stagiaire.nom} ${stagiaire.prenom}`;
                        li.className = 'list-group-item';
                        stagiairesList.appendChild(li);
                    });
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des stagiaires :', error));
    }
</script>