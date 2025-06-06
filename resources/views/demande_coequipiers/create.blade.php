@extends('layout.default')

@section('content')
<div class="container">
    <h1>Envoyer une Demande de Coéquipier</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <form action="{{ route('demande_coequipiers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="id_stagiaire_receveur" class="form-label">Sélectionner le Stagiaire</label>
            <select class="form-control" id="id_stagiaire_receveur" name="id_stagiaire_receveur" required>
                <option value="">-- Choisir un stagiaire --</option>
                @foreach ($stagiaires as $stagiaire)
                    <option value="{{ $stagiaire->id }}">{{ $stagiaire->nom }} {{ $stagiaire->prenom }} ({{ $stagiaire->email }})</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer la Demande</button>
        <a href="{{ route('demande_coequipiers.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection