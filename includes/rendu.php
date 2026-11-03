<?php
/** Rendu d'une page, commun à la génération statique (tools/build.php) et au site PHP (public/index.php). */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/contact.php';
require_once __DIR__ . '/blog.php';

/** HTML complet de la page content/<fichier>.php. */
function rendre(string $fichier): string
{
    ob_start();
    include $fichier;              // définit $page et écrit le contenu de la page
    $contenu = ob_get_clean();
    ob_start();
    include __DIR__ . '/haut.php';
    echo $contenu;
    include __DIR__ . '/bas.php';
    return ob_get_clean();
}
