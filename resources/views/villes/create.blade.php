@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2>Ajouter une Ville</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('villes.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label class="form-label" for="code">Code de la Ville</label>
            <input type="text" class="form-control" id="code" name="codeville" placeholder="Ex: PAR, NYC, MTL">
        </div>

        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom de la Ville</label>
            <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: Paris, New York, Montréal">
        </div>

        <div class="form-group mb-3">
            <label class="form-label" for="pays_id">Choisir le Pays</label>
            <select class="form-control" id="pays_id" name="pays_id">
                @foreach($pays as $pay)
                    <option value="{{ $pay->id }}">{{ $pay->code }} - {{ $pay->nom }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>
@endsection
