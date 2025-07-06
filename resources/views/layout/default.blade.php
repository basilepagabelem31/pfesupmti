<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"{{ (!empty($htmlAttribute)) ? $htmlAttribute : '' }}>
<head>




<!-- Select2 CSS -->

<!-- jQuery (si pas déjà inclus) -->

<!-- Select2 JS -->




<!-- Dans la section <head> de layout.default -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Rend la variable d'environnement de Laravel disponible en JavaScript
    window.APP_ENV = "{{ app()->environment() }}";
</script>
    @include('partial.head')
    {{--
        IMPORTANT : Assurez-vous que la directive @vite est appelée ICI ou DANS partial.head.
        Elle est cruciale pour charger Alpine.js et votre CSS principal.
        Exemple : @vite(['resources/css/app.css', 'resources/js/app.js'])
        Si vous utilisez un CDN pour Tailwind en développement, vous pouvez retirer 'resources/css/app.css' de la liste de @vite.
        Assurez-vous aussi que Font Awesome est bien inclus dans partial.head.
    --}}
</head>
<body class="{{ (!empty($bodyClass)) ? $bodyClass : '' }}">
    <!-- Notification section - Ensure Tailwind CSS classes are used for styling if possible -->
    <!-- @if(Auth::check() && Auth::user()->unreadNotifications->count())
        {{-- FIX: Added fixed positioning, high z-index, and Tailwind styling --}}
        <div class="fixed top-0 left-0 w-full z-50 bg-blue-100 text-blue-800 p-4 shadow-lg text-center animate-slide-down border-b border-blue-300" id="notification-alert">
            <h5 class="font-bold text-lg mb-2">Notifications :</h5>
            <ul class="list-none p-0 m-0 space-y-2"> -->
                <!-- @foreach(Auth::user()->unreadNotifications as $notification)
                    <li class="flex items-center justify-center text-base">
                        @if(isset($notification->data['type']) && $notification->data['type'] === 'ajout')
                            <span class="bg-green-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">Ajout</span>
                        @elseif(isset($notification->data['type']) && $notification->data['type'] === 'modification')
                            <span class="bg-yellow-500 text-gray-900 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">Modification</span>
                        @endif -->
                        <!-- {{ $notification->data['message'] }} -->
                        <!-- {{-- FIX: Replaced Bootstrap classes with Tailwind for the button --}}
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
    @endif -->

    <!-- BEGIN #app -->
    {{-- FIX: Added mt-16 to #app to push content down if notification is fixed at top --}}
    <div id="app" class="app {{ (!empty($appClass)) ? $appClass : '' }} {{ Auth::check() && Auth::user()->unreadNotifications->count() ? 'mt-32 sm:mt-24 md:mt-20 lg:mt-16' : '' }}">
        @includeWhen(empty($appHeaderHide), 'partial.header')
        @includeWhen(empty($appSidebarHide), 'partial.sidebar')
        @includeWhen(!empty($appTopNav), 'partial.top-nav')

        @if (empty($appContentHide))
            <!-- BEGIN #content -->
            <div id="content" class="app-content {{ (!empty($appContentClass)) ? $appContentClass : '' }}">
                @yield('content')
            </div>
            <!-- END #content -->
        @else
            @yield('content')
            {{-- Injection du JSON pré-calculé --}}
            <script>
                window._paysVilles = @json($paysVilles ?? []) ;
            </script>
        @endif

        @includeWhen(!empty($appFooter), 'partial.footer')
    </div>
    <!-- END #app -->

    @yield('outter_content')
    @include('partial.scroll-top-btn')
    @include('partial.theme-panel')
    
    @include('partial.scripts')
    @yield('my_js') {{-- Custom JS for specific pages, loaded AFTER general scripts --}}

    {{-- Add a specific style block for the notification animation --}}
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



<!-- Juste avant la balise </body> de fermeture dans layout.default -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
</body>
</html>
