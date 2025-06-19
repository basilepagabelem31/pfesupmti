<meta charset="utf-8" />
<title>Gestionnaire Stagiaires | @yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="description" content="@yield('metaDescription')" />
<meta name="author" content="@yield('metaAuthor')" />
<meta name="keywords" content="@yield('metaKeywords')" />

@stack('metaTag')

<!-- ================== BEGIN BASE CSS STYLE (De votre nouveau template) ================== -->
<link href="/assets/css/vendor.min.css" rel="stylesheet" />
<link href="/assets/css/app.min.css" rel="stylesheet" />
<link href="/assets/css/style.css" rel="stylesheet">
<!-- ================== END BASE CSS STYLE ================== -->

{{-- Début des imports CSS et polices depuis l'ancien app.blade.php --}}

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

{{-- L'instruction @vite gère l'inclusion de vos fichiers CSS et JS compilés par Vite. --}}
{{-- Elle est placée dans le head car c'est la pratique courante pour le CSS et le JS avec defer. --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Fin des imports CSS et polices depuis l'ancien app.blade.php --}}


@stack('css') {{-- Ce stack est pour le CSS spécifique à la page --}}