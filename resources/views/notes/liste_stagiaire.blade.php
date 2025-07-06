@extends('layout.default')

@section('title', 'Liste des stagiaires avec notes')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-6xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-green-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                <span class="text-blue-600">Liste des Stagiaires</span> avec Notes
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Visualisez le résumé des notes pour chaque stagiaire.
            </p>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Filtre nom/prénom --}}
            <div class="mb-8 flex flex-col items-center">
                <form action="{{ route('notes.liste_stagiaires') }}" method="GET" class="flex gap-3 items-end">
                    <div>
                        <label for="filter_nom" class="block text-sm font-medium text-gray-700 mb-1">Nom ou Prénom</label>
                        <input type="text" id="filter_nom" name="nom" value="{{ request('nom') }}"
                            class="appearance-none block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent text-base font-semibold rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition">
                        <i class="fas fa-filter mr-2"></i> Filtrer
                    </button>
                    <a href="{{ route('notes.liste_stagiaires') }}" class="inline-flex items-center px-5 py-2.5 border border-gray-300 text-base font-semibold rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-undo mr-2"></i>  Réinitialiser
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto shadow-md rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-blue-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Prénom</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-blue-800 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($stagiaires as $stagiaire)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">{{ $stagiaire->nom }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">{{ $stagiaire->prenom }}</td>
                            
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('notes.fiche_stagiaire', $stagiaire->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                    <i class="fas fa-eye mr-2"></i> 
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-4 text-center text-gray-500" colspan="5">Aucun stagiaire trouvé avec des notes.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
