@php
    // URL courante
    $currentUrl = '/'.trim(request()->path(), '/');

    // Génération récursive du sous-menu
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
            $active = ($currentUrl === $url) ? 'active' : '';

            $html .= '<div class="menu-item '. $hasSub .' '. $active .'">'
                   . '<a href="'. $url .'" class="menu-link">'. $text . $caret .'</a>'
                   . $submenu
                   . '</div>';
        }
        return $html;
    };
@endphp

<!-- BEGIN #sidebar -->
<div id="sidebar" class="app-sidebar">
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <div class="menu">

            <div class="menu-header">Navigation</div>

            <!-- Dashboard -->
            <div class="menu-item {{ ($currentUrl === route('dashboard')) ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-home"></i></span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </div>

           

            <!-- Utilisateurs -->
            <div class="menu-item {{ ($currentUrl === route('admin.index')) ? 'active' : '' }}">
                <a href="{{ route('admin.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-user"></i></span>
                    <span class="menu-text">Gestion des Administrateurs</span>
                </a>
            </div>

            <!-- Promotions -->
            <div class="menu-item {{ ($currentUrl === route('promotions.index')) ? 'active' : '' }}">
                <a href="{{ route('promotions.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-tags"></i></span>
                    <span class="menu-text">Promotions</span>
                </a>
            </div>

            <!-- Pays & Villes -->
            <div class="menu-item {{ ($currentUrl === route('pays.index')) ? 'active' : '' }}">
                <a href="{{ route('pays.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-globe"></i></span>
                    <span class="menu-text">Pays</span>
                </a>
            </div>
            <div class="menu-item {{ ($currentUrl === route('villes.index')) ? 'active' : '' }}">
                <a href="{{ route('villes.index') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-city"></i></span>
                    <span class="menu-text">Villes</span>
                </a>
            </div>

            <!-- Stagiaires -->
            <div class="menu-item {{ ($currentUrl === route('stagiaires.import.form')) ? 'active' : '' }}">
                <a href="{{ route('stagiaires.import.form') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-graduation-cap"></i></span>
                    <span class="menu-text">Importer Stagiaires</span>
                </a>
            </div>

            <!-- Superviseur -->
            <div class="menu-item {{ ($currentUrl === route('superviseur.dashboard')) ? 'active' : '' }}">
                <a href="{{ route('superviseur.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-user-tie"></i></span>
                    <span class="menu-text">Dashboard Superviseur</span>
                </a>
            </div>

            <!-- Stagiaire -->
            <div class="menu-item {{ ($currentUrl === route('stagiaire.dashboard')) ? 'active' : '' }}">
                <a href="{{ route('stagiaire.dashboard') }}" class="menu-link">
                    <span class="menu-icon"><i class="fas fa-user-graduate"></i></span>
                    <span class="menu-text">Dashboard Stagiaire</span>
                </a>
            </div>

            <!-- Bouton Documentation -->
            <div class="p-3 px-4 mt-auto hide-on-minified">
                <a href="https://seantheme.com/studio/documentation/index.html" class="btn btn-secondary d-block w-100 fw-600 rounded-pill">
                    <i class="fa fa-code-branch me-1 ms-n1 opacity-5"></i> Documentation
                </a>
            </div>

        </div>
    </div>
    <button class="app-sidebar-mobile-backdrop" data-dismiss="sidebar-mobile"></button>
</div>
<!-- END #sidebar -->