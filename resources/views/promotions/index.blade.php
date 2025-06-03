@extends('layout.default')

@section('title', 'Promotion')

@section('content')
    <h2> Gestion des promotions </h2>

    @if (session('success'))
        <div class="alert alert-success">{{session('success')}}</div>
    @endif
    <!-- Bouton ouvrir le modal -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#promotionModal" onclick="openPromotionModal()">Ajouter une Promotion</button>
    <!-- Table des promotions -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Status</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotions as $promotion)
                <tr>
                    <td>{{$promotion->titre}}</td>
                    <td>
                        @if($promotion->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Archivée</span>
                        @endif
                    </td>
                        <td>{{$promotion->created_at->format('d/m/Y')}}</td>
                        <td>
                        <!-- Bouton éditer -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#promotionModal"
                            onclick="openPromotionModal({{ $promotion->id }}, '{{ addslashes($promotion->titre) }}', '{{ $promotion->status }}')">
                            Modifier
                        </button>
                        <form method="POST" action="{{ route('promotions.destroy', $promotion) }}" style="display:inline-block;" onsubmit="return confirm('Voulez-vous vraiment supprime cette promotion ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Modal Promotion -->
    <div class="modal fade" id="promotionModal" tabindex="-1" aria-labelledby="promotionModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="promotionForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="promotionFormMethod" value="POST">
            <div class="modal-header">
              <h5 class="modal-title" id="promotionModalLabel">Ajouter une Promotion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="titre" class="form-label">Titre</label>
                <input type="text" class="form-control" id="titre" name="titre" required>
              </div>
              <div class="mb-3" id="statusField" style="display: none">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="active">Active</option>
                    <option value="archive">Archivée</option>
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
function openPromotionModal(id = null, titre = '', status = 'active') {
      // Réinitialise le formulaire
    let form = document.getElementById('promotionForm');
    form.reset();
        // Remplit le champ titre si une valeur est fournie
    document.getElementById('titre').value = titre || '';
    // Change le titre du modal selon le contexte
    document.getElementById('promotionModalLabel').textContent = id ? 'Modifier la Promotion' : 'Ajouter une Promotion';

    if(id) {
        // Mode modification : prépare le formulaire pour l'édition
        form.action = '/promotions/' + id;//la route update 
        document.getElementById('promotionFormMethod').value = 'PUT';//methode qui est put pour la modification 
        document.getElementById('statusField').style.display = '';//affiche le champ statut
        document.getElementById('status').value = status;//selectionne le status actuel
    } else {
        form.action = '/promotions';//la route store 
        document.getElementById('promotionFormMethod').value = 'POST';
        document.getElementById('statusField').style.display = 'none';//champ status cache 
    }
}
</script>
@endsection