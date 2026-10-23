<?php
$page = [
    'chemin' => '/404.html',
    'seo' => <<<'HTML'
	<title>Page not found &#8211; Wasatch Parc Snow Tubing</title>
	<style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>
	<meta name="robots" content="noindex" />

HTML,
    'mise_en_page' => 'pleine-largeur',
    'styles' => "<link rel='stylesheet' id='page-404-css' href='/assets/css/page-404.css?ver=1' media='all' />\n",
    'itemtype' => 'https://schema.org/WebPage',
    'classes' => 'error404 wp-custom-logo ast-desktop ast-page-builder-template ast-no-sidebar astra-4.8.7 ast-single-post ast-replace-site-logo-transparent ast-inherit-site-logo-transparent ast-theme-transparent-header ast-hfb-header',
    'entete_transparent' => true,
    'fin' => '				',
];
?>

					<main id="main" class="site-main">
<section class="w404" aria-labelledby="w404-titre">
	<div class="w404-neige" aria-hidden="true"><?php for ($i = 0; $i < 40; $i++): ?><span></span><?php endfor ?></div>
	<div class="w404-contenu">
		<p class="w404-code" aria-hidden="true">
			<span>4</span>
			<svg class="w404-bouee" viewBox="0 0 120 120" role="img">
				<defs>
					<radialGradient id="w404-tube" cx="38%" cy="32%" r="75%">
						<stop offset="0" stop-color="#ff8a5c"/>
						<stop offset=".55" stop-color="#e8401c"/>
						<stop offset="1" stop-color="#a3260c"/>
					</radialGradient>
				</defs>
				<circle cx="60" cy="60" r="44" fill="none" stroke="url(#w404-tube)" stroke-width="28"/>
				<path d="M33 40a34 34 0 0 1 22-14" fill="none" stroke="#fff" stroke-opacity=".55" stroke-width="6" stroke-linecap="round"/>
				<circle cx="60" cy="60" r="30" fill="none" stroke="#7a1c08" stroke-opacity=".35" stroke-width="2"/>
			</svg>
			<span>4</span>
		</p>
		<h1 id="w404-titre">Oops! You slid off the track.</h1>
		<p class="w404-texte">This run doesn&rsquo;t exist, or it has moved. Grab your tube and head back to the top of the hill. The banked turns are waiting for you.</p>
		<div class="w404-boutons">
			<a class="w404-bouton w404-bouton-plein" href="/">Back to home</a>
			<a class="w404-bouton w404-bouton-contour" href="https://fareharbor.com/embeds/book/wasatchparc/?full-items=yes">Book your ride</a>
		</div>
		<nav class="w404-liens" aria-label="Useful pages">
			<a href="/information/"><strong>Information</strong><span>Hours, address, parking &amp; groups</span></a>
			<a href="/contact/"><strong>Contact</strong><span>Questions? Write to us</span></a>
			<a href="/category/blog/"><strong>Blog</strong><span>Snow tubing tips &amp; safety</span></a>
		</nav>
	</div>
</section>
					</main><!-- #main -->
		
