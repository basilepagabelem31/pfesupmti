@extends('layout.default')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white text-center">
            <h3>Ajouter un Superviseur</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('utilisateurs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Partie gauche (Colonne 1) -->
                    <div class="col-md-6">
                        <!-- Champ Nom -->
                        <div class="form-group mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control form-control-lg" id="nom" name="nom" placeholder="Nom du superviseur" required>
                        </div>

                        <!-- Champ Prénom -->
                        <div class="form-group mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control form-control-lg" id="prenom" name="prenom" placeholder="Prénom du superviseur" required>
                        </div>

                        <!-- Champ Email -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email du superviseur" required>
                        </div>

                        <!-- Champ Téléphone -->
                        <div class="form-group mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control form-control-lg" id="telephone" name="telephone" placeholder="Téléphone du superviseur" required>
                        </div>

                        <!-- Champ CIN -->
                        <div class="form-group mb-3">
                            <label for="cin" class="form-label">CIN</label>
                            <input type="text" class="form-control form-control-lg" id="cin" name="cin" placeholder="CIN du superviseur" required>
                        </div>

                        <!-- Champ Code -->
                        <div class="form-group mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control form-control-lg" id="code" name="code" placeholder="Code du superviseur" required>
                        </div>

                        <!-- Champ Adresse -->
                        <div class="form-group mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control form-control-lg" id="adresse" name="adresse" placeholder="Adresse du superviseur" required>
                        </div>
                    </div>

                    <!-- Partie droite (Colonne 2) -->
                    <div class="col-md-6">
                        <!-- Champ Mot de Passe -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Mot de Passe</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Mot de passe du superviseur" required>
                        </div>

                        <!-- Champ Pays -->
                        <div class="form-group mb-3">
                            <label for="pays_id" class="form-label">Pays</label>
                            <select class="form-control form-control-lg" id="pays_id" name="pays_id" required onchange="fetchVilles()">
                                <option value="">Sélectionner un pays</option>
                                @foreach ($pays as $pay)
                                    <option value="{{ $pay->id }}">{{ $pay->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Ville -->
                        <div class="form-group mb-3">
                            <label for="ville_id" class="form-label">Ville</label>
                            <select class="form-control form-control-lg" id="ville_id" name="ville_id" required>
                                <option value="">Sélectionner une ville</option>
                                @foreach ($villes as $ville)
                                    <option value="{{ $ville->id }}">{{ $ville->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Rôle -->
                        <div class="form-group mb-3">
                            <label for="role_id" class="form-label">Rôle</label>
                            <select class="form-control form-control-lg" id="role_id" name="role_id" required>
                                <option value="">Sélectionner un rôle</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Statut -->
                        <div class="form-group mb-3">
                            <label for="statut_id" class="form-label">Statut</label>
                            <select class="form-control form-control-lg" id="statut_id" name="statut_id" required>
                                <option value="">Sélectionner un statut</option>
                                @foreach ($statuts as $statut)
                                    <option value="{{ $statut->id }}">{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Email Log -->
                        <div class="form-group mb-3">
                            <label for="email_log_id" class="form-label">Email Log</label>
                            <select class="form-control form-control-lg" id="email_log_id" name="email_log_id" required>
                                <option value="">Sélectionner un Email Log</option>
                                @foreach ($emailLogs as $emailLog)
                                    <option value="{{ $emailLog->id }}">{{ $emailLog->statut }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bouton d'ajout -->
                <button type="submit" class="btn btn-success btn-lg mt-3">Ajouter Superviseur</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Fonction pour filtrer les villes par pays
    function fetchVilles() {
        var paysId = document.getElementById('pays_id').value;
        if (paysId) {
            fetch(`/villes/${paysId}`)
                .then(response => response.json())
                .then(data => {
                    var villeSelect = document.getElementById('ville_id');
                    villeSelect.innerHTML = '<option value="">Sélectionner une ville</option>';
                    data.villes.forEach(ville => {
                        var option = document.createElement('option');
                        option.value = ville.id;
                        option.textContent = ville.nom;
                        villeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    }
</script>
@endsection
