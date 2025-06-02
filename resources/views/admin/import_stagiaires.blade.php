<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importer des Stagiaires</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Custom animation for fade-in effect */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        /* Custom button gradient and shadow */
        .btn-gradient {
            background-image: linear-gradient(to right, #4F46E5 0%, #6366F1 50%, #4F46E5 100%);
            background-size: 200% auto;
            transition: background-position 0.3s ease-out, transform 0.2s ease-out;
        }
        .btn-gradient:hover {
            background-position: right center; /* Shift background for hover effect */
            transform: translateY(-2px); /* Slight lift effect */
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* More pronounced shadow on hover */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 via-purple-100 to-pink-100 p-4 min-h-screen flex items-center justify-center">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-2xl shadow-2xl w-full animate-fade-in transform hover:scale-105 transition duration-300 ease-in-out">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center tracking-tight">
            Importer des Stagiaires
        </h1>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 border border-green-200 p-4 mb-5 rounded-xl text-base font-medium flex items-center space-x-3 shadow-sm">
                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-yellow-50 text-yellow-700 border border-yellow-200 p-4 mb-5 rounded-xl text-base font-medium flex items-center space-x-3 shadow-sm">
                <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span>{!! session('warning') !!}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 text-red-700 border border-red-200 p-4 mb-5 rounded-xl text-base font-medium flex items-center space-x-3 shadow-sm">
                <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 p-4 mb-5 rounded-xl text-base font-medium shadow-sm">
                <p class="font-bold mb-2 flex items-center space-x-2">
                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Erreurs de validation :</span>
                </p>
                <ul class="list-disc list-inside text-sm mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('stagiaires.import') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="mb-6">
                <label for="file" class="block text-gray-700 text-lg font-semibold mb-3">Sélectionnez le fichier CSV :</label>
                <input type="file" name="file" id="file" accept=".csv, .txt" class="block w-full text-base text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-3.5 shadow-sm">
            </div>
            <button type="submit" class="w-full btn-gradient text-white font-extrabold py-3 px-6 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transition duration-300 ease-in-out">
                Importer le fichier
            </button>
        </form>

        <hr class="my-8 border-t-2 border-gray-200">

        <h2 class="text-2xl font-bold text-gray-800 mb-4">Format CSV attendu :</h2>
        <p class="text-gray-600 text-base mb-4">Votre fichier CSV doit inclure les colonnes suivantes (les autres sont facultatives) :</p>
        <ul class="list-disc list-inside text-gray-700 text-base mb-5 space-y-1">
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">email</code> (obligatoire, unique)</li>
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">cin</code> (obligatoire, unique)</li>
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">nom</code> (obligatoire)</li>
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">prenom</code> (obligatoire)</li>
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">telephone</code> (obligatoire)</li>
            <li><code class="font-mono bg-gray-100 text-purple-700 px-2 py-1 rounded">code_groupe</code> (obligatoire)</li>
        </ul>
        <p class="text-gray-600 text-base mb-3">Exemple de ligne CSV :</p>
        <pre class="bg-gray-50 p-4 rounded-xl text-gray-800 text-sm overflow-x-auto whitespace-pre-wrap break-words border border-gray-200 shadow-inner">
<span class="text-blue-600">email</span>,<span class="text-blue-600">cin</span>,<span class="text-blue-600">nom</span>,<span class="text-blue-600">prenom</span>,<span class="text-blue-600">telephone</span>,<span class="text-blue-600">code_groupe</span>
jean.dupont@example.com,AB12345,Dupont,Jean,0612345678,G001</pre>
        <p class="text-gray-600 text-sm mt-4">
            Assurez-vous que les groupes et rôles (`Stagiaire`) existent dans votre base de données avant l'importation.
        </p>
    </div>
</body>
</html>
