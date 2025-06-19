@extends('layout.default')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="max-w-3xl w-full bg-white rounded-3xl shadow-xl overflow-hidden animate-fade-in">
        <div class="relative p-8 md:p-10 lg:p-12">
            {{-- Decorative element at the top --}}
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-t-3xl"></div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 text-center mb-6 mt-4 leading-tight">
                Importation Rapide des <span class="text-blue-600">Stagiaires</span>
            </h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Téléchargez votre fichier CSV pour ajouter de nouveaux stagiaires en masse.
            </p>

            {{-- Messages de session --}}
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-green-200">
                    <svg class="h-7 w-7 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-yellow-100 text-yellow-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-yellow-200">
                    <svg class="h-7 w-7 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.3 2.647-1.3 3.412 0l7.258 12.49A1.5 1.5 0 0118.067 17H1.933a1.5 1.5 0 01-1.317-2.201l7.258-12.49zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{!! session('warning') !!}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded-xl mb-6 flex items-center space-x-3 shadow-md border border-red-200">
                    <svg class="h-7 w-7 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold text-lg">{{ session('error') }}</span>
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

            <form action="{{ route('stagiaires.import') }}" method="POST" enctype="multipart/form-data" class="mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-200 shadow-inner">
                @csrf
                <div class="mb-6">
                    <label for="file" class="block text-gray-800 text-lg font-semibold mb-3">
                        <i class="fas fa-file-csv text-blue-500 mr-2"></i> Sélectionnez votre fichier CSV :
                    </label>
                    <input type="file" name="file" id="file" accept=".csv, .txt"
                           class="block w-full text-lg text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-white file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-lg file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-300 ease-in-out shadow-sm"
                           required>
                    <p class="mt-2 text-sm text-gray-500">
                        Seuls les fichiers .csv ou .txt sont acceptés. Taille maximale : 10 Mo.
                    </p>
                </div>
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-purple-700 text-white font-extrabold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-opacity-75 tracking-wider uppercase text-lg">
                    <i class="fas fa-upload mr-3"></i> Importer les stagiaires
                </button>
            </form>

            <hr class="my-10 border-t-2 border-gray-100">

            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-inner">
                <h2 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                    <i class="fas fa-info-circle text-purple-600 mr-3"></i> Format du fichier CSV attendu
                </h2>
                <p class="text-gray-600 text-base mb-4 leading-relaxed">
                    Votre fichier CSV doit impérativement inclure les colonnes suivantes. Les autres champs de stagiaires sont facultatifs et peuvent être ajoutés si nécessaire.
                </p>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-gray-700 text-base mb-6">
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">email</code> <span class="ml-2 text-red-500 text-xs">(Obligatoire, unique)</span></li>
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">cin</code> <span class="ml-2 text-red-500 text-xs">(Obligatoire, unique)</span></li>
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">nom</code> <span class="ml-2 text-red-500 text-xs">(Obligatoire)</span></li>
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">prenom</code> <span class="ml-2 text-red-500 text-xs">(Obligatoire)</span></li>
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">telephone</code> <span class="ml-2 text-red-500 text-xs">(Obligatoire)</span></li>
                    <li class="flex items-center"><i class="fas fa-check-circle text-green-500 mr-2"></i> <code class="font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-sm font-semibold">code_groupe</code> <span class="ml-2 text-red-500 text-xs">(Optionnel)</span></li>
                </ul>
                <p class="text-gray-600 text-base mb-3 font-semibold">Exemple de ligne CSV :</p>
                <pre class="bg-gray-100 p-5 rounded-xl text-gray-800 text-sm overflow-x-auto whitespace-pre-wrap break-words border border-gray-300 shadow-inner">
<span class="text-indigo-600">email</span>,<span class="text-indigo-600">cin</span>,<span class="text-indigo-600">nom</span>,<span class="text-indigo-600">prenom</span>,<span class="text-indigo-600">telephone</span>,<span class="text-indigo-600">code_groupe</span>
jean.dupont@example.com,AB12345,Dupont,Jean,0612345678,G001</pre>
                <p class="text-gray-600 text-sm mt-5 leading-relaxed">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i> Assurez-vous que les groupes (via leur `code_groupe`) et les rôles (`Stagiaire` en particulier) existent dans votre base de données avant de procéder à l'importation pour éviter les erreurs.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Si vous avez des scripts JS spécifiques à cette page, vous pouvez les pousser ici --}}
{{-- @push('scripts')
<script>
    // Votre JavaScript ici
</script>
@endpush --}}

@endsection

