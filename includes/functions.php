<?php
/** Fonctions communes aux gabarits includes/haut.php et includes/bas.php. */

const MENU = [
    ['id' => 1092, 'page' => 3, 'url' => '/information/', 'titre' => 'Information', 'classes' => ' menu-item-privacy-policy', 'rel' => 'privacy-policy'],
    ['id' => 1093, 'page' => 916, 'url' => '/contact/', 'titre' => 'Contact', 'classes' => '', 'rel' => ''],
];

/** Logo de l'en-tête : version « transparente » (accueil, contact) ou normale. Écrit la ligne entière.
 *  $ordinateur : en-tête ordinateur (image prioritaire) ou en-tête mobile. */
function logo(array $page, bool $ordinateur): void
{
    $img = '<img width="%1$d" height="%2$d" src="/wp-content/uploads/2024/12/%3$s" class="custom-logo" alt="logo wasatch parc" decoding="async"%4$s srcset="/wp-content/uploads/2024/12/logo-bracelets.png 1x, /wp-content/uploads/2024/12/logo-bracelets.png 2x" sizes="(max-width: %1$dpx) 100vw, %1$dpx" />';
    if (!empty($page['entete_transparent'])) {
        $petit = sprintf($img, 250, 25, 'logo-bracelets-250x25.png', '');
        $html = '<a href="/" class="custom-logo-link transparent-custom-logo" rel="home" itemprop="url" aria-label="Wasatch Parc Snow Tubing">' . $petit . '</a>'
              . '<a href="/" class="custom-logo-link ast-transparent-mobile-logo" rel="home" itemprop="url">' . $petit . '</a>';
    } else {
        $html = '<a href="/" class="custom-logo-link" rel="home">' . sprintf($img, 1000, 100, 'logo-bracelets.png', $ordinateur ? ' fetchpriority="high"' : '') . '</a>';
    }
    echo "\t\t\t\t\t<span class=\"site-logo-img\">" . $html . "</span>\t\t\t\t</div>\n";
}

/** Entrées du menu principal ; $avec_id : menu ordinateur (les <li> portent un id) ou menu mobile. */
function menu(array $page, bool $avec_id): void
{
    foreach (MENU as $m) {
        $actif = $m['url'] === $page['chemin'];
        $classes = 'menu-item menu-item-type-post_type menu-item-object-page' . $m['classes']
                 . ($actif ? ' current-menu-item page_item page-item-' . $m['page'] . ' current_page_item' : '')
                 . ' menu-item-' . $m['id'];
        echo '<li' . ($avec_id ? ' id="menu-item-' . $m['id'] . '"' : '') . ' class="' . $classes . '">'
           . '<a' . ($m['rel'] ? ' rel="' . $m['rel'] . '"' : '') . ' href="' . $m['url'] . '"' . ($actif ? ' aria-current="page"' : '') . ' class="menu-link">'
           . $m['titre'] . "</a></li>\n";
    }
}
