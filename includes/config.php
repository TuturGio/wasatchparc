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

// Réglages affichés sur les pages (includes/haut.php, contenus).
const SAISON_FERMEE = true;   // bandeau « Closed for the 2025–2026 season » en haut des pages ; false pour le retirer
const LIEN_RESERVATION = 'https://fareharbor.com/embeds/book/wasatchparc/?full-items=yes';
const LIEN_CARTES_CADEAUX = 'https://fareharbor.com/embeds/book/wasatchparc/?full-items=yes&flow=5518';
