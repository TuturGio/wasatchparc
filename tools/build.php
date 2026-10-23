<?php
/**
 * Génère le site statique dans _site/ : chaque content/<chemin>.php devient _site/<chemin>/index.html,
 * puis public/ (images, CSS, JS, CNAME…) est copié tel quel. C'est ce dossier que GitHub Pages publie.
 *
 * Usage (depuis la racine du projet) : php tools/build.php [dossier de sortie]
 */
$proj = dirname(__DIR__);
$sortie = $argv[1] ?? $proj . '/_site';
require $proj . '/includes/config.php';
require $proj . '/includes/functions.php';
if (WEB3FORMS_CLE === '') {
    fwrite(STDERR, "Attention : WEB3FORMS_CLE est vide dans includes/config.php, le formulaire de contact n'enverra rien.\n");
}

function rendre(string $fichier, string $proj): string
{
    ob_start();
    include $fichier;              // définit $page et écrit le contenu de la zone #primary
    $contenu = ob_get_clean();
    ob_start();
    include $proj . '/includes/haut.php';
    echo $contenu;
    include $proj . '/includes/bas.php';
    return ob_get_clean();
}

function copier(string $de, string $vers): void
{
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($de, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $f) {
        $cible = $vers . substr($f->getPathname(), strlen($de));
        $f->isDir() ? @mkdir($cible, 0777, true) : copy($f->getPathname(), $cible);
    }
}

if (is_dir($sortie)) {
    exec('rm -rf ' . escapeshellarg($sortie));
}
mkdir($sortie, 0777, true);
copier($proj . '/public', $sortie);

$n = 0;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($proj . '/content', FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if ($f->getExtension() !== 'php') {
        continue;
    }
    $rel = substr($f->getPathname(), strlen($proj . '/content/'), -4);
    $dest = $rel === 'accueil' ? "$sortie/index.html" : ($rel === '404' ? "$sortie/404.html" : "$sortie/$rel/index.html");
    @mkdir(dirname($dest), 0777, true);
    file_put_contents($dest, rendre($f->getPathname(), $proj));
    $n++;
}
echo "$n pages générées dans $sortie\n";
