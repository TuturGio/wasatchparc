<?php
/**
 * Routeur du site PHP (hébergement Hostinger). Les fichiers existants de public/ sont servis directement
 * par le serveur (voir .htaccess) ; toute autre adresse arrive ici et affiche content/<chemin>.php.
 * Mêmes règles que WordPress : / et /chemin/ avec la barre finale, /chemin redirigé en 301 vers /chemin/.
 */
require dirname(__DIR__) . '/includes/rendu.php';

$contenu = dirname(__DIR__) . '/content';
$chemin = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$fichier = null;

if ($chemin === '/') {
    $fichier = "$contenu/accueil.php";
} elseif (preg_match('#^/([a-z0-9-]+(?:/[a-z0-9-]+)*)(/?)$#', $chemin, $m) && !in_array($m[1], ['accueil', '404'], true)
          && is_file("$contenu/$m[1].php")) {
    if ($m[2] === '') {
        $requete = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        header('Location: /' . $m[1] . '/' . ($requete ? "?$requete" : ''), true, 301);
        exit;
    }
    $fichier = "$contenu/$m[1].php";
}

if ($fichier === null) {
    http_response_code(404);
    $fichier = "$contenu/404.php";
}
header('Content-Type: text/html; charset=UTF-8');
echo rendre($fichier);
