@extends('layout.default')

@section('title', 'Notes du Stagiaire - ' . $stagiaire->prenom . ' ' . $stagiaire->nom)

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-full w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in md:max-w-4xl">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-500 to-teal-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Notes pour <span class="text-green-600">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Gérez et consultez les notes du stagiaire.
            </p>

            {{-- Messages de session --}}
            @if(Session::has('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('success') }}</span>
                </div>
            @endif
            @if(Session::has('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                    <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ Session::get('error') }}</span>
                </div>
            @endif
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

            {{-- Section pour ajouter une note (visible uniquement par Superviseurs/Admins) --}}
            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                @include('notes.partials.form', ['stagiaire' => $stagiaire])
            @endif

            <hr class="my-8 border-gray-300">
            <h4 class="text-2xl font-bold text-gray-800 mb-4">Historique des notes</h4>
            <div class="space-y-4">
            @forelse($notes as $note)
                @php
                    $peutVoir = false;
                    $user = Auth::user();
                    
                    if ($note->visibilite === 'all') {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'donneur' && $note->donneur_id === $user->id) {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'donneur + stagiaire' && ($note->donneur_id === $user->id || $note->stagiaire_id === $user->id)) {
                        $peutVoir = true;
                    } elseif ($note->visibilite === 'superviseurs- stagiaire' && ($user->isSuperviseur() || $user->isAdministrateur())) {
                        $peutVoir = true;
                    }
                @endphp
                @if($peutVoir)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex-grow mb-3 sm:mb-0">
                            <p class="text-gray-700 text-base mb-2">
                                <strong>{{ \Carbon\Carbon::parse($note->date_note)->format('d/m/Y') }}</strong> : {{ $note->valeur }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Ajoutée par: <span class="font-semibold">{{ $note->donneur->prenom }} {{ $note->donneur->nom }}</span>
                                @if($note->is_propagated)
                                    <span class="ml-2 text-purple-600 font-semibold">(Propagée de {{ $note->originalStagiaire->prenom ?? 'N/A' }} {{ $note->originalStagiaire->nom ?? '' }})</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex-shrink-0 ml-0 sm:ml-4 text-sm font-semibold mt-2 sm:mt-0">
                            @php
                                $visibilityText = '';
                                $visibilityClass = '';
                                switch ($note->visibilite) {
                                    case 'all':
                                        $visibilityText = 'Visible par tous';
                                        $visibilityClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 'donneur':
                                        $visibilityText = 'Donneur uniquement';
                                        $visibilityClass = 'bg-red-100 text-red-800';
                                        break;
                                    case 'donneur + stagiaire':
                                        $visibilityText = 'Donneur et Stagiaire';
                                        $visibilityClass = 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'superviseurs- stagiaire':
                                        $visibilityText = 'Superviseurs seulement (Privée au Stagiaire)';
                                        $visibilityClass = 'bg-yellow-100 text-yellow-800';
                                        break;
                                    default:
                                        $visibilityText = 'Visibilité inconnue';
                                        $visibilityClass = 'bg-gray-100 text-gray-800';
                                        break;
                                }
                            @endphp
                            <span class="px-2 py-1 rounded-full {{ $visibilityClass }}">
                                {{ $visibilityText }}
                            </span>
                        </div>
                        {{-- Actions Modifier/Supprimer (visibles uniquement par le donneur de la note) --}}
                        @if(Auth::id() === $note->donneur_id)
                            <div class="flex-shrink-0 ml-0 sm:ml-4 mt-3 sm:mt-0 flex space-x-2">
                                <a href="{{ route('notes.edit', $note->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition duration-150 ease-in-out">
                                    <i class="fas fa-edit mr-1"></i> Modifier
                                </a>
                                <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out"
                                            onclick="return confirm('Voulez-vous vraiment supprimer cette note ?')">
                                        <i class="fas fa-trash-alt mr-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            @empty
                <p class="text-gray-500 text-center py-4">Aucune note trouvée pour ce stagiaire.</p>
            @endforelse
            </div>

            @if(Auth::user()->isSuperviseur() || Auth::user()->isAdministrateur())
                <div class="mt-6 text-center">
                    <a href="{{ route('notes.liste_stagiaires') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-semibold rounded-xl shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
