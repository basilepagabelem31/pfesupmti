<form method="POST" action="{{ route('notes.store') }}" class="mb-4">
    @csrf
    <input type="hidden" name="stagiaire_id" value="{{ $stagiaire->id }}">
    <div class="mb-2">
        <label for="valeur">Note</label>
        <textarea name="valeur" class="form-control" required placeholder="Contenu de la note"></textarea>
    </div>
    <div class="mb-2">
        <label for="visibilite">Visibilité</label>
        <select name="visibilite" class="form-control" required>
            <option value="all">Visible par tous</option>
            <option value="donneur">Donneur uniquement</option>
            <option value="donneur + stagiaire">Donneur + stagiaire</option>
            <option value="superviseurs- stagiaire">Superviseurs uniquement</option>
        </select>
    </div>
    <div class="mb-2">
        <label>
            <input type="checkbox" name="propager" value="1">
            Propager à tous les coéquipiers
        </label>
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fa fa-plus"></i> Ajouter la note
    </button>
</form>