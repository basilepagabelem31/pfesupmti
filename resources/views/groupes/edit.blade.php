@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="fw-bold text-center">Modifier un Groupe</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('groupes.update', $groupe->id) }}">
                @csrf
                @method('PUT') 
                <input type="hidden" id="groupe_id" name="groupe_id" value="{{ old('groupe_id', $groupe->id) }}">


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label fw-bold">Nom :</label>
                        <input type="text" class="form-control shadow-sm" name="nom" value="{{ $groupe->nom }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jour" class="form-label fw-bold">Jour :</label>
                        <input type="number" class="form-control shadow-sm" name="jour" value="{{ $groupe->jour }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="heure_debut" class="form-label fw-bold">Heure Début :</label>
                        <input type="time" class="form-control shadow-sm" name="heure_debut" value="{{ $groupe->heure_debut }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="heure_fin" class="form-label fw-bold">Heure Fin :</label>
                        <input type="time" class="form-control shadow-sm" name="heure_fin" value="{{ $groupe->heure_fin }}" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-success px-4 fw-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
