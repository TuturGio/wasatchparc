<?php
/**
 * Génère le site statique dans _site/ : chaque content/<chemin>.php devient _site/<chemin>/index.html,
 * puis public/ (images, CSS, JS, CNAME…) est copié tel quel, sauf le routeur PHP (index.php, .htaccess)
 * qui ne sert qu'à l'hébergement PHP. C'est ce dossier que GitHub Pages publie.
 *
 * Usage (depuis la racine du projet) : php tools/build.php [dossier de sortie]
 */
$proj = dirname(__DIR__);
$sortie = $argv[1] ?? $proj . '/_site';
require $proj . '/includes/rendu.php';

function copier(string $de, string $vers): void
{
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($de, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $f) {
        $rel = substr($f->getPathname(), strlen($de));
        if (in_array($rel, ['/index.php', '/.htaccess'], true)) {
            continue;
        }
        $cible = $vers . $rel;
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
    file_put_contents($dest, rendre($f->getPathname()));
    $n++;
}
echo "$n pages générées dans $sortie\n";
