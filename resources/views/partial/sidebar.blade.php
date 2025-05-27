@php
    // URL courante (avec leading slash)
    $currentUrl = '/'.trim(request()->path(), '/');

    // Closure récursive UNIQUE pour les sous-menus
    $renderSubMenu = function(array $items) use (&$renderSubMenu, $currentUrl) {
        $html = '';

        foreach ($items as $menu) {
            $hasSub  = ! empty($menu['children']) ? 'has-sub' : '';
            $url     = $menu['url']   ?? '#';
            $text    = '<span class="menu-text">'. ($menu['text'] ?? '') .'</span>';
            $caret   = $hasSub ? '<span class="menu-caret"><b class="caret"></b></span>' : '';
            $submenu = '';

            // Rendu récursif des enfants
            if (! empty($menu['children'])) {
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
  <!-- BEGIN scrollbar -->
  <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
    <!-- BEGIN menu -->
    <div class="menu">

      @foreach(config('sidebar.menu') as $menu)
        @php
            // Paramètres du menu principal
            $hasSub  = ! empty($menu['children']) ? 'has-sub' : '';
            $url     = $menu['url']   ?? '#';
            $icon    = '';
            if (! empty($menu['icon'])) {
                $icon = '<span class="menu-icon"><i class="'. $menu['icon'] .'"></i>'
                      . (! empty($menu['label'])
                         ? '<span class="menu-icon-label">'. $menu['label'] .'</span>'
                         : '')
                      . '</span>';
            }
            $text    = ! empty($menu['text'])
                      ? '<span class="menu-text">'. $menu['text'] .'</span>'
                      : '';
            $caret   = $hasSub ? '<span class="menu-caret"><b class="caret"></b></span>' : '';
            $submenu = '';

            // Rendu des sous-menus si présents
            if (! empty($menu['children'])) {
                $submenu = '<div class="menu-submenu">'
                         . $renderSubMenu($menu['children'])
                         . '</div>';
            }

            // Actif au niveau principal
            $active = ($currentUrl === $url) ? 'active' : '';
        @endphp

        @if(! empty($menu['is_header']))
          <div class="menu-header">{!! $menu['text'] !!}</div>

        @elseif(! empty($menu['is_divider']))
          <div class="menu-divider"></div>

        @else
          <div class="menu-item {{ $hasSub }} {{ $active }}">
            <a href="{{ $url }}" class="menu-link">
              {!! $icon !!}
              {!! $text !!}
              {!! $caret !!}
            </a>
            {!! $submenu !!}
          </div>
        @endif

      @endforeach

      <div class="p-3 px-4 mt-auto hide-on-minified">
        <a href="https://seantheme.com/studio/documentation/index.html"
           class="btn btn-secondary d-block w-100 fw-600 rounded-pill">
          <i class="fa fa-code-branch me-1 ms-n1 opacity-5"></i> Documentation
        </a>
      </div>
    </div>
    <!-- END menu -->
  </div>
  <!-- END scrollbar -->
  <!-- BEGIN mobile-sidebar-backdrop -->
  <button class="app-sidebar-mobile-backdrop" data-dismiss="sidebar-mobile"></button>
  <!-- END mobile-sidebar-backdrop -->
</div>
<!-- END #sidebar -->
