@extends('layout.default')

@section('content')

<div class="modal fade" id="createGroupeModal" tabindex="-1" aria-labelledby="createGroupeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupeModalLabel">Ajouter un nouveau Groupe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('groupes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du Groupe</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="jour" class="form-label">Jour</label>
                        <input type="date" class="form-control @error('jour') is-invalid @enderror" id="jour" name="jour" value="{{ old('jour') }}" required>
                        @error('jour')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="heure_debut" class="form-label">Heure de Début</label>
                        <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" value="{{ old('heure_debut') }}" required>
                        @error('heure_debut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="heure_fin" class="form-label">Heure de Fin</label>
                        <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" value="{{ old('heure_fin') }}" required>
                        @error('heure_fin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter Groupe</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection