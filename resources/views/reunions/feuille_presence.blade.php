@extends('layout.default')

@section('title', 'Feuille de Présence')

@section('content')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


    <div class="container py-4">
        <h3 class="mb-4">Feuille de Présence - Réunion du {{ $reunion->date->format('d/m/Y') }}</h3>
        <p><strong>Groupe :</strong> {{ $reunion->groupe->nom }}</p>
        <p><strong>Horaires :</strong> {{ $reunion->heure_debut }} - {{ $reunion->heure_fin }}</p>
        <p><strong>Note :</strong> {{ $reunion->note ?? 'Aucune note ajoutée.' }}</p>
        <div class="text-end mt-4">
        <a href="{{ route('reunions.index') }}" class="btn btn-secondary">Retour à la liste des réunions</a>
        </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h4 class="card-title mb-0">Liste des présences</h4>
        </div>
        <div class="card-body">
            @if(empty($presences))
                <div class="text-center py-5">
                    <p class="text-muted">Aucun stagiaire trouvé pour ce groupe.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Statut</th>
                                <th>Note</th>
                                <th>Validé par</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($presences as $presence)
                                <tr>
                                    <td>{{ $presence['stagiaire']->nom }} {{ $presence['stagiaire']->prenom }}</td>
                                    <td>
                                        <select name="statut" class="form-select form-select-sm" onchange="updatePresence({{ $presence['stagiaire']->id }}, '{{ $reunion->id }}', this.value)">
                                            <option value="Présent" @selected($presence['absence'] && $presence['absence']->statut === 'Présent')>Présent</option>
                                            <option value="Assisté" @selected($presence['absence'] && $presence['absence']->statut === 'Assisté')>Assisté</option>
                                            <option value="Absent" @selected($presence['absence'] && $presence['absence']->statut === 'Absent')>Absent</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" value="{{ $presence['absence']->note ?? '' }}" placeholder="Ajouter une note..." onblur="updateNote({{ $presence['stagiaire']->id }}, '{{ $reunion->id }}', this.value)">
                                    </td>
                                    <td>
                                        @if($presence['absence'] && $presence['absence']->valideur)
                                            {{ $presence['absence']->valideur->nom }}
                                        @else
                                            non-defini
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-end mt-4">
                        <form action="{{ route('reunions.cloture', $reunion->id) }}" method="POST" onsubmit="return confirm('La reunion est-elle bien fini ?');">
                            @csrf
                            <button type="submit" class="btn btn-danger">Clôturer la réunion</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function updatePresence(stagiaireId, reunionId, statut) {
        fetch(`/reunions/${reunionId}/presence/${stagiaireId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ statut })
        }).then(response => response.json())
          .then(data => {
              if (!data.success) {
                  alert('Erreur lors de la mise à jour du statut.');
              }
          }).catch(error => {
              console.error('Erreur :', error);
          });
    }

    function updateNote(stagiaireId, reunionId, note) {
        fetch(`/reunions/${reunionId}/presence/${stagiaireId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ note })
        }).then(response => response.json())
          .then(data => {
              if (!data.success) {
                  alert('Erreur lors de la mise à jour de la note.');
              }
          }).catch(error => {
              console.error('Erreur :', error);
          });
    }
</script>
@endsection