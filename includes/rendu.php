<?php
/** Rendu d'une page, commun à la génération statique (tools/build.php) et au site PHP (public/index.php). */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/contact.php';
require_once __DIR__ . '/blog.php';

/** HTML complet de la page content/<fichier>.php. */
function rendre(string $fichier): string
{
    ob_start();
    include $fichier;              // définit $page et écrit le contenu de la zone #primary
    $contenu = ob_get_clean();
    // Gabarit : « moderne » (nouvelle charte, includes/moderne/) ou celui repris de WordPress (Astra).
    $dossier = ($page['gabarit'] ?? '') === 'moderne' ? __DIR__ . '/moderne' : __DIR__;
    ob_start();
    include $dossier . '/haut.php';
    echo $contenu;
    include $dossier . '/bas.php';
    return ob_get_clean();
}
