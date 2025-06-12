@extends('layout.default')

@section('title', 'Modification de la note')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-2xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-t-3xl"></div>

            <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                <i class="fas fa-edit text-yellow-600 mr-3"></i> Modifier la note
            </h2>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Mettez à jour le contenu et la visibilité de cette note.
            </p>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 shadow-md border border-red-200">
                    <p class="font-bold mb-3 flex items-center space-x-2 text-lg">
                        <svg class="h-6 w-6 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                        <span>Erreurs de validation :</span>
                    </p>
                    <ul class="list-disc list-inside text-base mt-2 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('notes.update', $note->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="valeur" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <textarea name="valeur" id="valeur" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" required rows="4" placeholder="Contenu de la note">{{ old('valeur', $note->valeur) }}</textarea>
                    @error('valeur') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="visibilite" class="block text-sm font-medium text-gray-700 mb-1">Visibilité</label>
                    <select name="visibilite" id="visibilite" class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm" required>
                        <option value="all" {{ old('visibilite', $note->visibilite) == 'all' ? 'selected' : '' }}>Visible par tous</option>
                        <option value="donneur" {{ old('visibilite', $note->visibilite) == 'donneur' ? 'selected' : '' }}>Donneur uniquement</option>
                        <option value="donneur + stagiaire" {{ old('visibilite', $note->visibilite) == 'donneur + stagiaire' ? 'selected' : '' }}>Donneur et Stagiaire</option>
                        <option value="superviseurs- stagiaire" {{ old('visibilite', $note->visibilite) == 'superviseurs- stagiaire' ? 'selected' : '' }}>Superviseurs seulement (Privée au Stagiaire)</option>
                    </select>
                    @error('visibilite') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex justify-between space-x-4 mt-8">
                    <a href="{{ route('notes.fiche_stagiaire', $note->stagiaire_id) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-semibold rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">
                        <i class="fas fa-check mr-2"></i> Modifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
