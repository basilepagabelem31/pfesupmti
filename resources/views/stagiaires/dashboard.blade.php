@extends('layout.default')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Bonjour, {{ Auth::user()->prenom }} !</h1>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    {{-- Disposition des cartes : 1 colonne sur mobile, 2 sur petits écrans, 3 sur moyens et grands écrans --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6">

        {{-- Carte : Mon Profil --}}
        <!-- <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                </div>
               <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mon Profil</h2>
                <p class="text-gray-600 mb-4 flex-grow">Consultez et mettez à jour vos informations personnelles.</p> -->
                <!-- <div class="flex justify-center mt-auto">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i> Gérer mon profil
                    </a>
                </div
            </div>
        </div> -->

        {{-- Carte : Mes Fichiers --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-purple-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-folder fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mes Fichiers</h2>
                <p class="text-gray-600 mb-4 flex-grow">Accédez à vos documents et attestations.</p>
                <div class="flex justify-center mt-auto">
                    <a href="{{ route('fichiers.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-eye mr-2"></i> Voir mes fichiers
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte : Mes Demandes de Coéquipiers --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-green-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-exchange-alt fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Demandes de Coéquipiers</h2>
                <p class="text-gray-600 mb-4 flex-grow">Gérez vos demandes de coéquipiers et leurs statuts.</p>
                <div class="flex justify-center mt-auto">
                    <a href="{{ route('demande_coequipiers.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-handshake mr-2"></i> Voir les demandes
                    </a>
                </div>
            </div>
        </div>

        {{-- Carte : Mes Absences --}}
        <!-- <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-red-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-calendar-times fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mes Absences</h2>
                <p class="text-gray-600 mb-4 flex-grow">Consultez votre historique d'absences.</p>
                <div class="flex justify-center mt-auto">
                    <a href="/stagiaire/absences" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-list-ul mr-2"></i> Voir
                    </a>
                </div>
            </div>
        </div> -->

        {{-- Carte : Mon Sujet --}}
        <!-- <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-yellow-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-lightbulb fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mon Sujet</h2>
                <p class="text-gray-600 mb-4 flex-grow">Détails sur votre sujet d'étude ou de projet.</p>
                <div class="flex justify-center mt-auto">
                    <a href="/stagiaire/sujet" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-info-circle mr-2"></i> Voir les détails
                    </a>
                </div>
            </div>
        </div> -->

        {{-- Carte : Mon Groupe --}}
        <!-- <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-orange-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mon Groupe</h2>
                <p class="text-gray-600 mb-4 flex-grow">Informations sur votre groupe et vos horaires.</p>
                <div class="flex justify-center mt-auto">
                    <a href="/stagiaire/groupe" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <i class="fas fa-clock mr-2"></i> Voir les détails
                    </a>
                </div>
            </div>
        </div> -->

        {{-- Carte : Mes Notes --}}
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-gray-100 flex flex-col transform hover:scale-105 transition duration-300 ease-in-out">
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-center items-center mb-4">
                    <div class="p-4 rounded-full bg-blue-gray-100 text-blue-gray-600">
                        <i class="fas fa-clipboard fa-3x"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">Mes Notes</h2>
                <p class="text-gray-600 mb-4 flex-grow">Consultez les notes attribuées à vos performances.</p>
                <div class="flex justify-center mt-auto">
                   <!-- Exemple dans le tableau de bord du stagiaire ou sa page de profil -->
<a href="{{ route('notes.fiche_stagiaire', Auth::user()->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
    <i class="fas fa-scroll mr-2"></i> Mes Notes
</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
