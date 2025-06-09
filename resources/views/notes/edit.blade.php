<!--Modification  --->
@extends('layout.default')

@section('title', 'Modification de la note')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h2 class="mb-0"><i class="fa fa-edit"></i> Modifier la note</h2>
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

            <form method="POST" action="{{ route('notes.update', $note->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="valeur" class="form-label">Note</label>
                    <textarea name="valeur" class="form-control" required rows="4" placeholder="Contenu de la note">{{ old('valeur', $note->valeur) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="visibilite" class="form-label">Visibilité</label>
                    <select name="visibilite" class="form-select" required>
                        <option value="all" {{ $note->visibilite == 'all' ? 'selected' : '' }}>Visible par tous</option>
                        <option value="donneur" {{ $note->visibilite == 'donneur' ? 'selected' : '' }}>Donneur uniquement</option>
                        <option value="donneur + stagiaire" {{ $note->visibilite == 'donneur + stagiaire' ? 'selected' : '' }}>Donneur + stagiaire</option>
                        <option value="superviseurs- stagiaire" {{ $note->visibilite == 'superviseurs- stagiaire' ? 'selected' : '' }}>Superviseurs uniquement</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check"></i> Modifier
                    </button>
                    <a href="{{ route('notes.fiche_stagiaire', $note->stagiaire_id) }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection