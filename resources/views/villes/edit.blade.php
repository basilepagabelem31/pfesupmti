@extends('layout.default')

@section('content')
<div class="container mt-5">
    <h2>Modifier une Ville</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('villes.update', $ville->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label class="form-label" for="code">Code de la Ville</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ $ville->code }}">
        </div>

        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom de la Ville</label>
            <input type="text" class="form-control" id="nom" name="nom" value="{{ $ville->nom }}">
        </div>

        <div class="form-group mb-3">
            <label class="form-label" for="pays_id">Choisir le Pays</label>
            <select class="form-control" id="pays_id" name="pays_id">
                @foreach($pays as $pay)
                    <option value="{{ $pay->id }}" {{ $ville->pays_id == $pay->id ? 'selected' : '' }}>
                        {{ $pay->code }} - {{ $pay->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
@endsection
