<?php
/** Réglages du site. */

// Dossier privé, hors du site et hors de git : ~/wasatchparc-prive/ (le projet est dans ~/domains/wasatchparc.com/).
// Il contient config.php (réglages SMTP du formulaire, clé secrète) et les fichiers anti-spam du formulaire.
define('DOSSIER_PRIVE', dirname(__DIR__, 4) . '/wasatchparc-prive');
const FICHIER_PRIVE = DOSSIER_PRIVE . '/config.php';

/** Réglage du fichier privé (null s'il manque : le site s'affiche, seul l'envoi du formulaire échoue). */
function prive(string $cle): mixed
{
    static $reglages = null;
    $reglages ??= is_file(FICHIER_PRIVE) ? (array) require FICHIER_PRIVE : [];
    return $reglages[$cle] ?? null;
}
