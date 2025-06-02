<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importer Stagiaires</title>
</head>
<body>
    <h1>Importer des Stagiaires</h1>

    {{-- Affichage des messages de session --}}
    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color: red;">{!! session('error') !!}</div>
    @endif

    @if (session('warning'))
        <div style="color: orange;">{!! session('warning') !!}</div>
    @endif

    {{-- Affichage des erreurs de validation du formulaire --}}
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulaire d'upload --}}
    <form action="{{ route('stagiaires.import') }}" method="POST" enctype="multipart/form-data">
        @csrf {{-- Jeton CSRF pour la sécurité --}}
        <label for="file">Sélectionnez un fichier Excel/CSV :</label><br>
        <input type="file" name="file" id="file" accept=".xlsx, .xls, .csv"> {{-- Accepte les formats courants --}}
        <br><br>
        <button type="submit">Importer le fichier</button>
    </form>
</body>
</html>