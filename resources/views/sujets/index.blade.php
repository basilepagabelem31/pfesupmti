@extends('layout.default')

@section('title', 'Sujet')

@section('content')
<div class="container py-4">
    <h2>Gestion des Sujets</h2>

     @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

      <!-- Bouton ouvrir le modal d'ajout -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#sujetModal" onclick="openSujetModal()">Ajouter un Sujet</button>

    <!-- Table des sujets -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Promotion</th>
                <th>Groupe</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($sujets as $sujet)
            <tr>
                <td>{{ $sujet->titre }}</td>
                <td>{{ $sujet->description }}</td>
                <td>{{ $sujet->promotion->titre ?? '' }}</td>
                <td>{{ $sujet->groupe->nom ?? '' }}</td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        data-bs-toggle="modal" data-bs-target="#sujetModal"
                        onclick="openSujetModal({{ $sujet->id }}, '{{ addslashes($sujet->titre) }}', `{{ addslashes($sujet->description) }}`, {{ $sujet->promotion_id }}, {{ $sujet->groupe_id }},'{{ route('sujets.update', $sujet->id) }}')">
                        Modifier
                    </button>
                    <form method="POST" action="{{ route('sujets.destroy', $sujet) }}" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce sujet ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            Supprimer
                        </button>
                    </form>
                      <!-- Bouton pour ouvrir le modal pour l'inscription  -->
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#inscriptionsModal{{ $sujet->id }}">
                        Gérer les inscriptions des Stagiaires
                    </button>
                </td>
            </tr>

              <!-- Modal pour l'inscription du stagiaire  -->
                <div class="modal fade" id="inscriptionsModal{{ $sujet->id }}" tabindex="-1" aria-labelledby="inscriptionsModalLabel{{ $sujet->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inscriptionsModalLabel{{ $sujet->id }}">
                            Gérer les inscriptions pour : {{ $sujet->titre }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Liste des stagiaires inscrits -->
                        <h6>Stagiaires inscrits :</h6>
                        <ul>
                            @foreach($sujet->stagiaires as $stagiaire)
                                <li>
                                    {{ $stagiaire->prenom }} {{ $stagiaire->nom }}<br>
                                    <form method="POST" action="{{ route('sujets.desinscrire', [$sujet, $stagiaire->id]) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment désinscrire ce stagiaire ?')">Désinscrire</button>
                                    </form>
                                </li>
                            @endforeach
                            @if($sujet->stagiaires->isEmpty())
                                <li>Aucun stagiaire inscrit à ce sujet .</li>
                            @endif
                        </ul>

                        <!-- Formulaire d'inscription -->
                        <h6>Inscrire un stagiaire :</h6>
                        <form method="POST" action="{{ route('sujets.inscrire', $sujet) }}">
                            @csrf
                            <select name="stagiaire_id" class="form-select" required>
                                <option value="">Sélectionner un stagiaire</option>
                                @foreach($stagiaires->diff($sujet->stagiaires) as $stagiaire)
                                    <option value="{{ $stagiaire->id }}">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Inscrire</button>
                        </form>
                    </div>
                    </div>
                </div>
                </div>          
        @endforeach
    </tbody>
</table>

      <!-- Modal Sujet -->
    <div class="modal fade" id="sujetModal" tabindex="-1" aria-labelledby="sujetModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="sujetForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="sujetFormMethod" value="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="sujetModalLabel">Ajouter un Sujet</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
              </div>
              <div class="mb-3">
                <label for="promotion_id" class="form-label">Promotion (active seulement)</label>
                <select class="form-select" id="promotion_id" name="promotion_id" required>
                  <option value="">Sélectionner</option>
                  @foreach($promotions as $promotion)
                    <option value="{{ $promotion->id }}">{{ $promotion->titre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label for="groupe_id" class="form-label">Groupe</label>
                <select class="form-select" id="groupe_id" name="groupe_id" required>
                  <option value="">Sélectionner...</option>
                  @foreach($groupes as $groupe)
                    <option value="{{ $groupe->id }}">{{ $groupe->nom }}</option>
                  @endforeach
                </select>
              </div>
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

@section('my_js')
<script>
function openSujetModal(id = null, titre = '', description = '', promotion_id = '', groupe_id = '',updateUrl = '') {
    let form = document.getElementById('sujetForm');
    form.reset();
    document.getElementById('titre').value = titre || '';
    document.getElementById('description').value = description || '';
    document.getElementById('sujetModalLabel').textContent = id ? 'Modifier le Sujet' : 'Ajouter un Sujet';

    if(id) {
        form.action = updateUrl;
        document.getElementById('sujetFormMethod').value = 'PUT';
        document.getElementById('promotion_id').value = promotion_id;
        document.getElementById('groupe_id').value = groupe_id;
    } else {
        form.action = "{{ route('sujets.store') }}";
        document.getElementById('sujetFormMethod').value = 'POST';
        document.getElementById('promotion_id').value = '';
        document.getElementById('groupe_id').value = '';
    }
}
</script>
@endsection