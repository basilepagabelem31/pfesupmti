<?php
namespace App\Helpers;

class MenuHelper
{
    /**
     * Rendu récursif du sous-menu
     *
     * @param  array  $items
     * @param  string $currentUrl
     * @param  int    $level
     * @param  bool   $&parentActive
     * @return string
     */
    public static function renderSubMenu(array $items, string $currentUrl, int $level = 1, bool &$parentActive = false): string
    {
        $html = '';
        foreach ($items as $menu) {
            $hasSub    = ! empty($menu['children']) ? 'has-sub' : '';
            $url       = $menu['url'] ?? '#';
            $text      = '<span class="menu-text">'. ($menu['text'] ?? '') .'</span>';
            $caret     = $hasSub ? '<span class="menu-caret"><b class="caret"></b></span>' : '';
            $subHtml   = '';

            // Sous-sous-menu
            if (! empty($menu['children'])) {
                $childActive = false;
                $subHtml = '<div class="menu-submenu">'
                         . self::renderSubMenu($menu['children'], $currentUrl, $level + 1, $childActive)
                         . '</div>';
                if ($childActive) {
                    $parentActive = true;
                }
            }

            // Etat actif
            $active = ($currentUrl === $url) ? 'active' : '';
            if ($active) {
                $parentActive = true;
            }
            if ($parentActive && $level > 1) {
                // si un enfant est actif, on marque aussi le parent
                $active = 'active';
            }

            $html .= '<div class="menu-item '. $hasSub .' '. $active .'">'
                   . '<a href="'. $url .'" class="menu-link">'. $text . $caret .'</a>'
                   . $subHtml
                   . '</div>';
        }
        return $html;
    }
}
