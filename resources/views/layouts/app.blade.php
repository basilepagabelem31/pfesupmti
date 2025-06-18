<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Laissez ce lien Font Awesome CDN si vous préférez ne pas l'importer via Vite dans app.css --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Assurez-vous que ces lignes pour Bootstrap CDN sont COMMENTÉES ou SUPPRIMÉES si vous utilisez Vite --}}
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    </head>
    <body class="font-sans antialiased">
        {{-- SECTION DE NOTIFICATION BANNIÈRE --}}
        @if(Auth::check() && Auth::user()->unreadNotifications->count())
            {{-- Utilisation des mêmes classes Tailwind pour le positionnement et le style --}}
            <div class="fixed top-0 left-0 w-full z-50 bg-blue-100 text-blue-800 p-4 shadow-lg text-center animate-slide-down border-b border-blue-300" id="notification-alert">
                <h5 class="font-bold text-lg mb-2">Notifications :</h5>
                <ul class="list-none p-0 m-0 space-y-2">
                    @foreach(Auth::user()->unreadNotifications as $notification)
                        <li class="flex items-center justify-center text-base">
                            @if(isset($notification->data['type']) && $notification->data['type'] === 'ajout')
                                <span class="bg-green-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">Ajout</span>
                            @elseif(isset($notification->data['type']) && $notification->data['type'] === 'modification')
                                <span class="bg-yellow-500 text-gray-900 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">Modification</span>
                            @endif
                            {{ $notification->data['message'] }}
                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline ml-3">
                                @csrf
                                <button type="submit" class="text-blue-700 hover:text-blue-900 font-semibold text-sm px-3 py-1 rounded-md bg-blue-200 hover:bg-blue-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Voir
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- FIN DE LA SECTION DE NOTIFICATION BANNIÈRE --}}

        {{-- Ajout de la classe mt-XX au div principal pour pousser le contenu vers le bas --}}
        <div class="min-h-screen bg-gray-100 {{ Auth::check() && Auth::user()->unreadNotifications->count() ? 'mt-32 sm:mt-24 md:mt-20 lg:mt-16' : '' }}">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                @yield('content')
            </main>
        </div>

        {{-- Assurez-vous que ce script pour Bootstrap CDN est COMMENTÉ ou SUPPRIMÉ si vous utilisez Vite --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

        @stack('scripts')
         {{-- Add a specific style block for the notification animation (copied from default layout) --}}
        <style>
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            .animate-slide-down {
                animation: slideDown 0.5s ease-out forwards;
            }
        </style>
    </body>
</html>
