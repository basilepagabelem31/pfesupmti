@extends('layout.default')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <center><h3>Modifier un Superviseur</h3></center>
        </div>
        <div class="card-body">
            <form action="{{ route('utilisateurs.update', $utilisateur->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Partie gauche (Colonne 1) -->
                    <div class="col-md-6">
                        <!-- Champ Nom -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="nom">Nom</label>
                            <input type="text" class="form-control form-control-lg" id="nom" name="nom" value="{{ old('nom', $utilisateur->nom) }}" placeholder="Nom du superviseur" required>
                        </div>

                        <!-- Champ Prénom -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input type="text" class="form-control form-control-lg" id="prenom" name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}" placeholder="Prénom du superviseur" required>
                        </div>

                        <!-- Champ Email -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email', $utilisateur->email) }}" placeholder="Email du superviseur" required>
                        </div>

                        <!-- Champ Téléphone -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="telephone">Téléphone</label>
                            <input type="text" class="form-control form-control-lg" id="telephone" name="telephone" value="{{ old('telephone', $utilisateur->telephone) }}" placeholder="Téléphone du superviseur" required>
                        </div>

                        <!-- Champ CIN -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="cin">CIN</label>
                            <input type="text" class="form-control form-control-lg" id="cin" name="cin" value="{{ old('cin', $utilisateur->cin) }}" placeholder="CIN du superviseur" required>
                        </div>

                        <!-- Champ Code -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="code">Code</label>
                            <input type="text" class="form-control form-control-lg" id="code" name="code" value="{{ old('code', $utilisateur->code) }}" placeholder="Code du superviseur" required>
                        </div>

                        <!-- Champ Adresse -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="adresse">Adresse</label>
                            <input type="text" class="form-control form-control-lg" id="adresse" name="adresse" value="{{ old('adresse', $utilisateur->adresse) }}" placeholder="Adresse du superviseur" required>
                        </div>
                    </div>

                    <!-- Partie droite (Colonne 2) -->
                    <div class="col-md-6">
                        <!-- Champ Mot de Passe -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="password">Mot de Passe</label>
                            <input type="text" class="form-control form-control-lg" id="password" name="password" value="{{ old('password') }}" placeholder="Mot de passe du superviseur">
                        </div>

                        <!-- Champ Pays -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="pays_id">Pays</label>
                            <select class="form-control form-control-lg" id="pays_id" name="pays_id" required>
                                <option value="">Sélectionner un pays</option>
                                @foreach ($pays as $pay)
                                    <option value="{{ $pay->id }}" {{ $utilisateur->pays_id == $pay->id ? 'selected' : '' }}>{{ $pay->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Ville -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="ville_id">Ville</label>
                            <select class="form-control form-control-lg" id="ville_id" name="ville_id" required>
                                <option value="">Sélectionner une ville</option>
                                @foreach ($villes as $ville)
                                    <option value="{{ $ville->id }}" {{ $utilisateur->ville_id == $ville->id ? 'selected' : '' }}>{{ $ville->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Rôle -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="role_id">Rôle</label>
                            <select class="form-control form-control-lg" id="role_id" name="role_id" required>
                                <option value="">Sélectionner un rôle</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $utilisateur->role_id == $role->id ? 'selected' : '' }}>{{ $role->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Statut -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="statut_id">Statut</label>
                            <select class="form-control form-control-lg" id="statut_id" name="statut_id" required>
                                <option value="">Sélectionner un statut</option>
                                @foreach ($statuts as $statut)
                                    <option value="{{ $statut->id }}" {{ $utilisateur->statut_id == $statut->id ? 'selected' : '' }}>{{ $statut->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Champ Email Log -->
                        <div class="form-group mb-3">
                            <label class="form-label" for="email_log_id">Email Log</label>
                            <select class="form-control form-control-lg" id="email_log_id" name="email_log_id" required>
                                <option value="">Sélectionner un Email Log</option>
                                @foreach ($emailLogs as $emailLog)
                                    <option value="{{ $emailLog->id }}" {{ $utilisateur->email_log_id == $emailLog->id ? 'selected' : '' }}>{{ $emailLog->statut }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bouton de modification -->
                <button type="submit" class="btn btn-success btn-lg mt-3">Modifier Superviseur</button>
            </form>
        </div>
    </div>
</div>
@endsection
