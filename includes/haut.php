<?php
/**
 * Début de chaque page : <head>, bandeau de saison, en-tête et menu.
 * L'en-tête est posé sur la photo de la première section de la page (classe .m-photo).
 * @var array $page
 */
$menu = [
    ['url' => '/#prices', 'titre' => 'Prices'],
    ['url' => '/information/', 'titre' => 'Plan your visit'],
    ['url' => '/category/blog/', 'titre' => 'Blog'],
    ['url' => '/contact/', 'titre' => 'Contact'],
];
?>
<!DOCTYPE html>
<html lang="en-US" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?= $page['seo'] ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap">
<link rel="stylesheet" href="/assets/css/moderne.css?ver=<?= filemtime(dirname(__DIR__) . '/public/assets/css/moderne.css') ?>">
<?= $page['styles'] ?? '' ?>
<link rel="icon" href="/wp-content/uploads/2024/12/cropped-cropped-favicon-1-32x32.jpg" sizes="32x32">
<link rel="icon" href="/wp-content/uploads/2024/12/cropped-cropped-favicon-1-192x192.jpg" sizes="192x192">
<link rel="apple-touch-icon" href="/wp-content/uploads/2024/12/cropped-cropped-favicon-1-180x180.jpg">
<meta name="msapplication-TileImage" content="/wp-content/uploads/2024/12/cropped-cropped-favicon-1-270x270.jpg">
</head>
<body class="m-page m-<?= htmlspecialchars($page['nom'] ?? 'page') ?>">
<a class="m-lien-evitement" href="#contenu">Skip to content</a>
<?php if (SAISON_FERMEE): ?>
<div class="m-saison" role="status">
	<p><span class="m-saison-point" aria-hidden="true"></span><strong>Closed for the 2026–2027 season</strong> <span class="m-saison-detail">We're making improvements &amp; upgrades. See you next winter!</span></p>
</div>
<?php endif ?>
<header class="m-entete">
	<div class="m-conteneur m-entete-ligne">
		<a class="m-logo" href="/" rel="home"><img src="/wp-content/uploads/2024/12/logo-bracelets.png" width="300" height="30" alt="Wasatch Parc Snow Tubing"></a>
		<button class="m-menu-bouton" type="button" aria-expanded="false" aria-controls="m-menu" aria-label="Open menu">
			<svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
		</button>
		<nav class="m-menu" id="m-menu" aria-label="Main">
<?php foreach ($menu as $m): ?>
			<a href="<?= $m['url'] ?>"<?= $m['url'] === $page['chemin'] ? ' aria-current="page"' : '' ?>><?= $m['titre'] ?></a>
<?php endforeach ?>
			<a class="m-bouton m-bouton-principal" href="<?= LIEN_RESERVATION ?>">Book now</a>
		</nav>
	</div>
</header>
<main id="contenu">
