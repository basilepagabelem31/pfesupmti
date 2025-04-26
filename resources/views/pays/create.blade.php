 @extends('layout.default') 

@section('content') 
<div class="container mt-5">
    <h2>Ajouter un Pays</h2>
    <form action="{{ route('pays.store') }}" method="POST">
        @csrf

        <!-- Champ Code -->
        <div class="form-group mb-3">
            <label class="form-label" for="code">Code du Pays</label>
            <input type="text" class="form-control" id="code" name="code" placeholder="Ex: FR, US, CA">
        </div>

        <!-- Champ Nom -->
        <div class="form-group mb-3">
            <label class="form-label" for="nom">Nom du Pays</label>
            <input type="text" class="form-control" id="nom" name="nom" placeholder="Ex: France, USA, Canada">
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>
@endsection
