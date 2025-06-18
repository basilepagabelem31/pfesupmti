<!-- BEGIN #header -->
<div id="header" class="app-header">
    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler" @if (!empty($appTopNav) && !empty($appSidebarHide)) data-toggle="top-nav-mobile" @else data-toggle="sidebar-mobile" @endif>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->

    <!-- BEGIN brand -->
    <div class="brand">
        <div class="desktop-toggler">
            <button type="button" class="menu-toggler" @if (empty($appSidebarHide))data-toggle="sidebar-minify"@endif>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>

        <a href="/" class="group block text-center py-4 px-2 no-underline">
    <h1 class="text-xl sm:text-xl lg:text-xl font-extrabold text-gray-900 leading-tight tracking-tight
               inline-block bg-clip-text text-transparent
               bg-gradient-to-r from-blue-600 to-teal-500
               group-hover:from-teal-500 group-hover:to-blue-600 transition-all duration-300 ease-in-out
               shadow-text-subtle">
        Gestionnaire <span class="text-blue-700 group-hover:text-teal-600 transition-colors duration-300 ease-in-out">Stagiaires</span>
    </h1>
    <!-- Effet de texte subtil (peut nécessiter un style CSS personnalisé si Tailwind ne gère pas 'text-shadow' directement) -->
    <style>
        .shadow-text-subtle {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</a>
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
        <form class="menu-search" method="POST" name="header_search_form">
           
        </form>

        {{-- Section des Notifications (pour tous les utilisateurs) --}}
        <div class="menu-item dropdown">
            <a href="#" data-bs-toggle="dropdown" data-display="static" class="menu-link">
                <div class="menu-icon"><i class="fa fa-bell nav-icon"></i></div>
                @if(Auth::check()) {{-- Vérifiez si un utilisateur est connecté --}}
                    @php
                        $unreadNotifications = Auth::user()->unreadNotifications;
                    @endphp
                    @if($unreadNotifications->count() > 0)
                        <div class="menu-label">{{ $unreadNotifications->count() }}</div>
                    @endif
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-notification">
                <h6 class="dropdown-header text-body mb-1">Notifications</h6>
                
                @if(Auth::check()) {{-- Vérifiez si un utilisateur est connecté avant de lister les notifications --}}
                    @forelse(Auth::user()->unreadNotifications as $notification)
                        {{-- Gérer les notifications de notes spécifiquement --}}
                        @if($notification->type === 'App\\Notifications\\NoteAdded' || $notification->type === 'App\\Notifications\\NoteUpdated')
                            {{-- Utilisation d'un formulaire pour soumettre la requête POST --}}
                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" style="display: none;" id="mark-notification-{{ $notification->id }}">
                                @csrf
                            </form>
                            <a href="#" class="dropdown-notification-item" 
                               onclick="event.preventDefault(); document.getElementById('mark-notification-{{ $notification->id }}').submit();">
                                <div class="dropdown-notification-icon">
                                    <i class="fas fa-clipboard-list fa-lg fa-fw text-info"></i> {{-- Icône pour les notes --}}
                                </div>
                                <div class="dropdown-notification-info">
                                    <div class="title">
                                        @if($notification->type === 'App\\Notifications\\NoteAdded')
                                            Nouvelle note ajoutée par {{ $notification->data['donneur_nom'] ?? 'un superviseur' }}
                                        @elseif($notification->type === 'App\\Notifications\\NoteUpdated')
                                            Note mise à jour par {{ $notification->data['donneur_nom'] ?? 'un superviseur' }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Note: "{{ Str::limit($notification->data['valeur'] ?? 'Contenu de la note', 50) }}"
                                    </div>
                                    <div class="time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="dropdown-notification-arrow">
                                    <i class="fa fa-chevron-right"></i>
                                </div>
                            </a>
                        @else
                            {{-- Gérer d'autres types de notifications génériques si elles existent --}}
                            <a href="#" class="dropdown-notification-item">
                                <div class="dropdown-notification-icon">
                                    <i class="fa fa-info-circle fa-lg fa-fw text-muted"></i>
                                </div>
                                <div class="dropdown-notification-info">
                                    <div class="title">{{ $notification->data['message'] ?? 'Notification générique' }}</div>
                                    <div class="time">{{ $notification->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="dropdown-notification-arrow">
                                    <i class="fa fa-chevron-right"></i>
                                </div>
                            </a>
                        @endif
                    @empty
                        <div class="p-3 text-center text-gray-500">
                            Aucune nouvelle notification.
                        </div>
                    @endforelse

                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <div class="p-2 text-center mb-n1">
                            {{-- Optionnel : lien pour marquer toutes les notifications comme lues --}}
                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-body text-opacity-50 text-decoration-none bg-transparent border-none cursor-pointer">
                                    Marquer toutes comme lues
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="p-3 text-center text-gray-500">
                        Connectez-vous pour voir les notifications.
                    </div>
                @endif
            </div>
        </div>

        {{-- Section du Profil Utilisateur et Déconnexion --}}
        <div class="menu-item dropdown">
            <a href="#" data-bs-toggle="dropdown" data-display="static" class="menu-link">
                <div class="menu-img online">
                    {{-- Assurez-vous que l'image de l'utilisateur est accessible ou utilisez une icône par défaut --}}
                    <img src="/assets/img/user/user.png" alt="User Image" class="ms-100 mh-100 rounded-circle" />
                </div>
                <div class="menu-text">@if(Auth::check()){{ Auth::user()->email }}@else Guest @endif</div> {{-- Affiche l'email de l'utilisateur connecté --}}
            </a>
            <div class="dropdown-menu dropdown-menu-end me-lg-3">

               @auth
    {{-- --- Menu pour le Super Administrateur --- --}}
    @if (Auth::user()->isAdministrateur())
        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile') }}">
            Profile <i class="fa fa-user-circle fa-fw ms-auto text-body text-opacity-50"></i>
        </a>
    @elseif (Auth::user()->isSuperviseur())
        <a class="dropdown-item d-flex align-items-center" href="{{ route('superviseur.profile') }}">
            Profile <i class="fa fa-user-circle fa-fw ms-auto text-body text-opacity-50"></i>
        </a>
    @elseif (Auth::user()->isStagiaire())
        <a class="dropdown-item d-flex align-items-center" href="{{ route('stagiaires.profiles') }}">
            Profile <i class="fa fa-user-circle fa-fw ms-auto text-body text-opacity-50"></i>
        </a>
    @endif
                 <div class="dropdown-divider"></div>

    {{-- Déconnexion --}}
    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}" 
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Déconnexion <i class="fa fa-sign-out-alt fa-fw ms-auto text-body text-opacity-50"></i>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
@else
    <a class="dropdown-item d-flex align-items-center" href="{{ route('login') }}">
        Connexion <i class="fa fa-sign-in-alt fa-fw ms-auto text-body text-opacity-50"></i>
    </a>
@endauth
            </div>
        </div>
    </div>
    <!-- END menu -->
</div>
<!-- END #header -->
