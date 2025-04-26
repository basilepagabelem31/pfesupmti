<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Système de Gestion des Stagiaires')</title>
    
    <!-- Bootstrap CSS (ou autre framework que tu veux utiliser) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome (icônes) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- Ton fichier CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @stack('styles') <!-- Pour ajouter des styles spécifiques par page -->
</head>

<body>
    <div id="app">
        
        <!-- Barre de navigation -->
        @include('layouts.partials.navbar')

        <div class="container-fluid mt-4">
            <!-- Contenu principal -->
            @yield('content')
        </div>
        
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts') <!-- Pour ajouter des scripts spécifiques par page -->
</body>
</html>
