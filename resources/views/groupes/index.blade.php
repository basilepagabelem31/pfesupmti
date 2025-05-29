@extends('layout.default')

@section('title', 'Gestion des Groupes')

@section('content')
<!-- Bouton Ajouter un Groupe -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#groupeModal" onclick="resetForm()">
    Ajouter un Groupe
</button>

@if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif

<!-- Tableau des groupes -->
<table class="table">
    <thead>
        <tr>
            <th>Code</th>
            <th>Nom</th>
            <th>Jour</th>
            <th>Heure Début</th>
            <th>Heure Fin</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupes as $groupe)
        <tr>
            <td>{{ $groupe->code }}</td>
            <td>{{ $groupe->nom }}</td>
            <td>{{ $groupe->jour }}</td>
            <td>{{ $groupe->heure_debut }}</td>
            <td>{{ $groupe->heure_fin }}</td>
            <td>
                
                <form action="{{ route('groupes.edit', $groupe->id) }}" method="GET" style="display:inline;">
    <button type="submit" class="btn btn-warning">Modifier</button>
</form>



                <form action="{{ route('groupes.destroy', $groupe) }}" method="POST" style="display:inline;" onsubmit="return confirmDeletion({{ $groupe->id }})">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
function editGroupe(groupeId) {
    fetch("/groupes/" + groupeId)
    .then(response => response.json())
    .then(groupe => {
        document.getElementById("groupe_id").value = groupe.id;
        document.getElementById("code").value = groupe.code;
        document.getElementById("nom").value = groupe.nom;
        document.getElementById("jour").value = groupe.jour;
        document.getElementById("heure_debut").value = groupe.heure_debut;
        document.getElementById("heure_fin").value = groupe.heure_fin;

        document.getElementById("groupeModalLabel").textContent = "Modifier un Groupe";
        let modal = new bootstrap.Modal(document.getElementById("groupeModal"));
        modal.show();
    })
    .catch(error => console.error("Erreur de récupération des données :", error));
}

// Gestion de la soumission du formulaire sans redirection
document.getElementById("groupeForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let groupeId = document.getElementById("groupe_id").value;
    let actionUrl = groupeId ? "/groupes/" + groupeId : "{{ route('groupes.store') }}";
    let methodType = groupeId ? "POST" : "POST";

    let formData = new FormData(this);

    fetch(actionUrl, {
        method: methodType,
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Rafraîchir la liste après mise à jour
        }
    })
    .catch(error => console.error("Erreur lors de l'enregistrement :", error));
});
</script>


<!-- Modal Ajouter/Modifier Groupe -->
 
<div class="modal fade" id="groupeModal" tabindex="-1" aria-labelledby="groupeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupeModalLabel">Gérer un Groupe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="groupeForm" method="POST">
                   @csrf
                     <input type="hidden" id="groupe_id" name="groupe_id">

                    <div class="mb-3">
    <label for="code" class="form-label">Code :</label>
    <input type="text" class="form-control" id="code" name="code" readonly>
</div>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom :</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="jour" class="form-label">Jour :</label>
                        <input type="number" class="form-control" id="jour" name="jour" required>
                    </div>
                    <div class="mb-3">
                        <label for="heure_debut" class="form-label">Heure Début :</label>
                        <input type="time" class="form-control" id="heure_debut" name="heure_debut" required>
                    </div>
                    <div class="mb-3">
                        <label for="heure_fin" class="form-label">Heure Fin :</label>
                        <input type="time" class="form-control" id="heure_fin" name="heure_fin" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
