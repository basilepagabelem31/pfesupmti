<div class="bg-blue-50 p-6 rounded-xl shadow-md mb-8 border border-blue-200">
    <h3 class="text-2xl font-bold text-blue-800 mb-4">Ajouter une nouvelle note</h3>
    <form action="{{ route('notes.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="stagiaire_id" value="{{ $stagiaire->id }}">
        
        <div>
            <label for="valeur" class="block text-sm font-medium text-gray-700">Contenu de la note</label>
            <textarea name="valeur" id="valeur" rows="4" required
                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('valeur') }}</textarea>
            @error('valeur') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        
        <div>
            <label for="visibilite" class="block text-sm font-medium text-gray-700">Visibilité de la note</label>
            <select name="visibilite" id="visibilite" required
                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="all" {{ old('visibilite') == 'all' ? 'selected' : '' }}>Visible par tous</option>
                <option value="donneur" {{ old('visibilite') == 'donneur' ? 'selected' : '' }}>Donneur uniquement</option>
                <option value="donneur + stagiaire" {{ old('visibilite') == 'donneur + stagiaire' ? 'selected' : '' }}>Donneur et Stagiaire</option>
                <option value="superviseurs- stagiaire" {{ old('visibilite') == 'superviseurs- stagiaire' ? 'selected' : '' }}>Superviseurs seulement (Privée au Stagiaire)</option>
            </select>
            @error('visibilite') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label for="date_note" class="block text-sm font-medium text-gray-700">Date de la note (optionnel)</label>
            <input type="date" name="date_note" id="date_note" value="{{ old('date_note', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @error('date_note') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" name="propager" id="propager" value="1"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                {{ old('propager') ? 'checked' : '' }}>
            <label for="propager" class="ml-2 block text-sm text-gray-900">Propager aux coéquipiers</label>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-semibold rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                <i class="fas fa-plus mr-2"></i> Ajouter la note
            </button>
        </div>
    </form>
</div>
