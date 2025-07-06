<?php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str; // Pour Str::startsWith

    // Récupérer l'utilisateur connecté
    $user = Auth::user();
    // URL courante pour l'activation du menu
    $currentUrl = '/'.trim(request()->path(), '/');

    // La fonction renderSubMenu n'est plus pertinente pour la section modifiée,
    // mais elle reste si elle est utilisée ailleurs pour d'autres sous-menus.
    $renderSubMenu = function(array $items) use (&$renderSubMenu, $currentUrl) {
        $html = '';
        foreach ($items as $menu) {
            $hasSub  = !empty($menu['children']) ? 'has-sub' : '';
            $url     = $menu['url'] ?? '#';
            $text    = '<span class="menu-text">'. ($menu['text'] ?? '') .'</span>';
            $caret   = $hasSub ? '<span class="menu-caret"><b class="caret"></b></span>' : '';
            $submenu = '';

            if (!empty($menu['children'])) {
                $submenu = '<div class="menu-submenu">'
                          . $renderSubMenu($menu['children'])
                          . '</div>';
            }

            // Détection de l'état actif
            $active = (Str::startsWith($currentUrl, trim(parse_url($url, PHP_URL_PATH), '/'))) ? 'active' : '';

            $html .= '<div class="menu-item '. $hasSub .' '. $active .'">'
                    . '<a href="'. $url .'" class="menu-link">'. $text . $caret .'</a>'
                    . $submenu
                    . '</div>';
        }
        return $html;
    };
?>

<!-- BEGIN #sidebar -->
<div id="sidebar" class="app-sidebar">
    {{-- Ajout de styles pour forcer le défilement si le contenu est trop long --}}
    <style>
        #sidebar .app-sidebar-content {
            overflow-y: auto; /* Active le défilement vertical si le contenu dépasse */
            height: 100%;     /* Assure que le conteneur prend toute la hauteur disponible de son parent */
        }
    </style>
    
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">


            <!-- --- Dashboard commun (redirige en fonction du rôle) --- -->
            

            @if ($user) {{-- S'assurer qu'un utilisateur est connecté --}}

                {{-- --- Menu pour le Super Administrateur --- --}}
                @if ($user->isAdministrateur())

                <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.dashboard'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="menu-text">Mon Tableau de Bord</span>
                </a>
            </div>
                    <div class="menu-header">Administration Système</div>

                    <!-- <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.dashboard'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                            <span class="menu-text">Mon Tableau de Bord</span>
                        </a>
                    </div> -->


                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('roles.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-id-badge"></i></span> {{-- Icône pour rôles --}}
                            <span class="menu-text">Rôles</span>
                        </a>
                    </div>


                    {{-- Nouveaux boutons directs pour la gestion des utilisateurs --}}
                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-user-shield"></i></span> {{-- Nouvelle icône pour admin/superviseur --}}
                            <span class="menu-text">Gestion Admin et Superviseur</span>
                        </a>
                    </div>
                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.users.stagiaires'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.users.stagiaires') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-user-graduate"></i></span> {{-- Icône pour stagiaires --}}
                            <span class="menu-text">Gestion Stagiaires</span>
                        </a>
                    </div>

                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('stagiaires.import.form'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('stagiaires.import.form') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-file-import"></i></span>
                            <span class="menu-text">Importer Stagiaires</span>
                        </a>
                    </div>
                   
                     {{--  Ajout du lien Profil --}}
    <!-- <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.profile'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
        <a href="{{ route('admin.profile') }}" class="menu-link">
            <span class="menu-icon"><i class="fas fa-user-circle"></i></span> {{-- Icône Profil --}}
            <span class="menu-text">Mon Profil</span>
        </a>
    </div> -->
                    {{-- FIN des nouveaux boutons directs pour la gestion des utilisateurs --}}

                  
                   



                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('promotions.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('promotions.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-graduation-cap"></i></span>
                            <span class="menu-text">Gestion des Promotions</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('groupes.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('groupes.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-layer-group"></i></span>
                            <span class="menu-text">Gestion des Groupes</span>
                        </a>
                    </div>


                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('reunions.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('reunions.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-chalkboard"></i></span>
                            <span class="menu-text">Gestion des Reunions</span>
                        </a>
                    </div>

                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('notes.liste_stagiaires'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('notes.liste_stagiaires') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-notes-medical"></i></span>
                            <span class="menu-text">Gestion des Notes</span>
                        </a>
                    </div>


                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('sujets.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('sujets.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-book-open"></i></span>
                            <span class="menu-text">Gestion des Sujets</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('fichiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('fichiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-file-alt"></i></span>
                            <span class="menu-text">Gestion des Fichiers</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('demande_coequipiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('demande_coequipiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-handshake"></i></span>
                            <span class="menu-text">Gestion des Coéquipiers</span>
                        </a>
                    </div>

                   

                   

                    <!-- <div class="menu-item {{ (Str::startsWith($currentUrl, ['pays', 'villes'])) ? 'active' : '' }} has-sub">
                        <a href="#" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-globe-americas"></i></span>
                            <span class="menu-text">Gestion Géographique</span>
                            <span class="menu-caret"><b class="caret"></b></span>
                        </a>
                        <div class="menu-submenu">
                            <div class="menu-item {{ ($currentUrl === trim(parse_url(route('pays.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                                <a href="{{ route('pays.index') }}" class="menu-link">
                                    <span class="menu-text">Pays</span>
                                </a>
                            </div>
                            <div class="menu-item {{ ($currentUrl === trim(parse_url(route('villes.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                                <a href="{{ route('villes.index') }}" class="menu-link">
                                    <span class="menu-text">Villes</span>
                                </a>
                            </div>
                        </div>
                    </div> -->

                    {{-- Section pour Réunions et Absences (nécessite des routes définies) --}}
                    <!-- <div class="menu-item {{ (Str::startsWith($currentUrl, ['reunions', 'absences'])) ? 'active' : '' }} has-sub">
                        <a href="#" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-calendar-alt"></i></span>
                            <span class="menu-text">Réunions & Absences</span>
                            <span class="menu-caret"><b class="caret"></b></span>
                        </a>
                        <div class="menu-submenu">
                            {{-- Placeholder: Ajoutez la route réelle pour la liste des réunions --}}
                            <div class="menu-item {{ ($currentUrl === '/reunions/index') ? 'active' : '' }}"> {{-- Exemple de route --}}
                                <a href="/reunions/index" class="menu-link"> {{-- Remplacez par route('reunions.index') si définie --}}
                                    <span class="menu-text">Planifier & Suivre Réunions</span>
                                </a>
                            </div>
                            {{-- Placeholder: Ajoutez la route réelle pour la gestion des absences --}}
                            <div class="menu-item {{ ($currentUrl === '/absences/manage') ? 'active' : '' }}"> {{-- Exemple de route --}}
                                <a href="/absences/manage" class="menu-link"> {{-- Remplacez par route('absences.manage') si définie --}}
                                    <span class="menu-text">Gestion des Absences</span>
                                </a>
                            </div>
                        </div>
                    </div> -->


                     



                       <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.email-settings.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.email-settings.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-envelope-circle-check"></i></span>
                            <span class="menu-text"> Gestion des Emails Settings</span>
                        </a>
                    </div>
                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.email_templates.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.email_templates.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-envelope"></i></span>
                            <span class="menu-text">Gestion des Emails Templates</span>
                        </a>
                    </div>


                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.email-settings.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.email_logs.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-cogs"></i></span>
                            <span class="menu-text"> Paramètres & Gestion des Emails </span>
                        </a>
                    </div>
                  

                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.logs.index'), PHP_URL_PATH), '/')) ? 'active' : '' }} has-sub">
                        <a href="{{ route('admin.logs.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-list-check"></i></span>
                            <span class="menu-text"> Logs Système</span>
                        </a>
                    </div>

                    {{-- Section pour Paramètres et Logs --}}
                    

                {{-- --- Menu pour le Superviseur --- --}}
                @elseif ($user->isSuperviseur())


                <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.dashboard'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                <a href="{{ route('superviseur.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span>
                    <span class="menu-text">Mon Tableau de Bord</span>
                </a>
            </div>
                    <div class="menu-header">Gestion</div>

                    <!-- <div class="menu-item {{ ($currentUrl === trim(parse_url(route('superviseur.dashboard'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('superviseur.dashboard') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-tachometer-alt"></i></span>
                            <span class="menu-text">Mon Tableau de Bord</span>
                        </a>
                    </div> -->

                        
                         <div class="menu-item {{ ($currentUrl === trim(parse_url(route('admin.users.stagiaires'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('admin.users.stagiaires') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-user-graduate"></i></span> {{-- Icône pour stagiaires --}}
                            <span class="menu-text">Gestion Stagiaires</span>
                        </a>
                    
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('stagiaires.import.form'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('stagiaires.import.form') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-file-import"></i></span>
                            <span class="menu-text">Importer Stagiaires</span>
                        </a>
                    </div>
                     

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('promotions.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('promotions.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-tags"></i></span>
                            <span class="menu-text">Gestion des Promotions</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('groupes.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('groupes.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-users"></i></span>
                            <span class="menu-text">Gestion des Groupes</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('sujets.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('sujets.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-lightbulb"></i></span>
                            <span class="menu-text">Gestion des Sujets</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('fichiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('fichiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-folder-open"></i></span>
                            <span class="menu-text">Gestion des Fichiers</span>
                        </a>
                    </div>

<div class="menu-item {{ ($currentUrl === trim(parse_url(route('reunions.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('reunions.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-chalkboard"></i></span>
                            <span class="menu-text">Gestion des Reunions</span>
                        </a>
                    </div>
                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('notes.liste_stagiaires'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('notes.liste_stagiaires') }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-notes-medical"></i></span>
                            <span class="menu-text">Gestion des Notes</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('demande_coequipiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('demande_coequipiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-user-friends"></i></span>
                            <span class="menu-text">Gestion de Coéquipiers</span>
                        </a>
                    </div>
                   <!-- <div class="menu-item {{ ($currentUrl === trim(parse_url(route('superviseur.profile'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
    <a href="{{ route('superviseur.profile') }}" class="menu-link">
        <span class="menu-icon"><i class="fas fa-user-circle"></i></span> {{-- Icône Profil --}}
        <span class="menu-text">Mon Profil</span>
    </a>
</div> -->

                    {{-- Section pour Réunions et Absences (nécessite des routes définies) --}}
                    <!-- <div class="menu-item {{ (Str::startsWith($currentUrl, ['reunions', 'absences'])) ? 'active' : '' }} has-sub">
                        <a href="#" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-calendar-check"></i></span>
                            <span class="menu-text">Réunions & Absences</span>
                            <span class="menu-caret"><b class="caret"></b></span>
                        </a>
                        <div class="menu-submenu">
                            {{-- Placeholder: Ajoutez la route réelle pour la liste des réunions --}}
                            <div class="menu-item {{ ($currentUrl === '/superviseur/reunions') ? 'active' : '' }}">
                                <a href="/superviseur/reunions" class="menu-link"> {{-- Remplacez par route('superviseur.reunions.index') si définie --}}
                                    <span class="menu-text">Planifier & Suivre</span>
                                </a>
                            </div>
                            {{-- Placeholder: Ajoutez la route réelle pour la gestion des absences --}}
                            <div class="menu-item {{ ($currentUrl === '/superviseur/absences') ? 'active' : '' }}">
                                <a href="/superviseur/absences" class="menu-link"> {{-- Remplacez par route('superviseur.absences.index') si définie --}}
                                    <span class="menu-text">Gérer les Absences</span>
                                </a>
                            </div>
                        </div>
                    </div> -->

                {{-- --- Menu pour le Stagiaire --- --}}
                @elseif ($user->isStagiaire())
                    <div class="menu-header">Mon Espace Stagiaire</div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('stagiaire.dashboard'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('stagiaire.dashboard') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-columns"></i></span>
                            <span class="menu-text">Mon Tableau de Bord</span>
                        </a>
                    </div>

                    <!-- <div class="menu-item {{ ($currentUrl === trim(parse_url(route('stagiaires.profiles'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
    <a href="{{ route('stagiaires.profiles') }}" class="menu-link">
        <span class="menu-icon"><i class="fas fa-user-circle"></i></span>
        <span class="menu-text">Mon Profil</span>
    </a>
</div> -->


                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('fichiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('fichiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-folder"></i></span>
                            <span class="menu-text">Mes Fichiers</span>
                        </a>
                    </div>

                    <div class="menu-item {{ ($currentUrl === trim(parse_url(route('demande_coequipiers.index'), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('demande_coequipiers.index') }}" class="menu-link">
                            <span class="menu-icon"><i class="fas fa-exchange-alt"></i></span>
                            <span class="menu-text">Demandes de Coéquipiers</span>
                        </a>
                    </div>



                     <div class="menu-item {{ ($currentUrl === trim(parse_url(route('notes.fiche_stagiaire', Auth::user()->id), PHP_URL_PATH), '/')) ? 'active' : '' }}">
                        <a href="{{ route('notes.fiche_stagiaire', Auth::user()->id) }}" class="menu-link">
                            <span class="menu-icon"><i class="fa-solid fa-notes-medical"></i></span>
                            <span class="menu-text">Mes Notes</span>
                        </a>
                    </div>

                    {{-- Placeholder: Mes Absences (nécessite une route définie) --}}
                    <!-- <div class="menu-item {{ ($currentUrl === '/stagiaire/absences') ? 'active' : '' }}">
                        <a href="/stagiaire/absences" class="menu-link"> {{-- Remplacez par route('stagiaire.absences.index') si définie --}}
                            <span class="menu-icon"><i class="fas fa-calendar-times"></i></span>
                            <span class="menu-text">Mes Absences</span>
                        </a>
                    </div> -->
                @endif
            @endif {{-- Fin du Auth::user() check --}}

            {{-- --- OPTION DE DÉCONNEXION --- --}}
            {{-- Cette section est visible pour tous les utilisateurs connectés --}}
            @if ($user)
            <div class="menu-item mt-4"> {{-- Ajoute une petite marge au-dessus --}}
                <form method="POST" action="{{ route('logout') }}" class="menu-link">
                    @csrf
                    <!-- <button type="submit" class="menu-link w-full text-left p-0 border-0 bg-transparent">
                        <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                        <span class="menu-text">Déconnexion</span>
                    </button> -->
                </form>
            </div>
            @endif

            <!-- Bouton Documentation (toujours visible) -->
            <!-- <div class="p-3 px-4 mt-auto hide-on-minified">
                <a href="https://seantheme.com/studio/documentation/index.html" class="btn btn-secondary d-block w-100 fw-600 rounded-pill">
                    <i class="fa fa-code-branch me-1 ms-n1 opacity-5"></i> Documentation
                </a>
            </div> -->

        </div>
    </div>
    <button class="app-sidebar-mobile-backdrop" data-dismiss="sidebar-mobile"></button>
</div>
<!-- END #sidebar -->
