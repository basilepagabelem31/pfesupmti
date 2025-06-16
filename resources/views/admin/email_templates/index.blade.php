@extends('layout.default')

@section('title', 'Liste des Email Templates')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Modèles d'email</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Button trigger modal for add -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
        Nouveau modèle
    </button>

    <table class="table table-bordered align-middle bg-white shadow">
        <thead class="table-light">
            <tr>
                <th>Type</th>
                <th>Sujet</th>
                <th>Description</th>
                <th>Contenu</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($templates as $template)
            <tr>
                <td>{{ $template->type }}</td>
                <td>{{ $template->subject }}</td>
                <td>{{ $template->description }}</td>
                <td>
                    <!-- Voir contenu dans un modal -->
                    <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#viewModal-{{ $template->id }}">
                        Voir
                    </button>
                    <!-- Modal pour afficher le contenu -->
                    <div class="modal fade" id="viewModal-{{ $template->id }}" tabindex="-1" aria-labelledby="viewModalLabel-{{ $template->id }}" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content bg-white">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel-{{ $template->id }}">Contenu du modèle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body">
                                <pre style="white-space:pre-wrap;">{{ $template->content }}</pre>
                            </div>
                        </div>
                      </div>
                    </div>
                </td>
                <td>
                    <!-- Bouton pour ouvrir le modal de modification -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal-{{ $template->id }}">Éditer</button>
                </td>
            </tr>

            <!-- Modal d'édition, style BLANC et MODERNE -->
            <div class="modal fade" id="editModal-{{ $template->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $template->id }}" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white rounded shadow border-0">
                    <form method="POST" action="{{ route('admin.email_templates.update', $template->id) }}">
                        @csrf
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title" id="editModalLabel-{{ $template->id }}">Éditer le modèle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <input type="text" name="type" class="form-control" value="{{ old('type', $template->type) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sujet</label>
                                <input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contenu</label>
                                <textarea name="content" class="form-control" rows="4" required>{{ old('content', $template->content) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description', $template->description) }}">
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
        @empty
            <tr>
                <td colspan="5" class="text-center">Aucun modèle trouvé.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Modal de création - style identique -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-white rounded shadow border-0">
        <form method="POST" action="{{ route('admin.email_templates.store') }}">
            @csrf
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title" id="addModalLabel">Créer un modèle d'email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sujet</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Contenu</label>
                    <textarea name="content" class="form-control" rows="4" required>{{ old('content') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endsection