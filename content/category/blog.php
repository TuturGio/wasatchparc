<?php
$page = [
    'chemin' => '/category/blog/',
    'nom' => 'blog',
    'gabarit' => 'moderne',
    'seo' => <<<'HTML'
	<title>Blog - Wasatch Parc Snow Tubing</title>
	<style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>
	
		<!-- All in One SEO 4.9.2 - aioseo.com -->
	<meta name="robots" content="max-image-preview:large" />
	<link rel="canonical" href="https://wasatchparc.com/category/blog/" />
			<script type="application/ld+json" class="aioseo-schema">
			{"@context":"https:\/\/schema.org","@graph":[{"@type":"BreadcrumbList","@id":"https:\/\/wasatchparc.com\/category\/blog\/#breadcrumblist","itemListElement":[{"@type":"ListItem","@id":"https:\/\/wasatchparc.com#listItem","position":1,"name":"Home","item":"https:\/\/wasatchparc.com","nextItem":{"@type":"ListItem","@id":"https:\/\/wasatchparc.com\/category\/blog\/#listItem","name":"Blog"}},{"@type":"ListItem","@id":"https:\/\/wasatchparc.com\/category\/blog\/#listItem","position":2,"name":"Blog","previousItem":{"@type":"ListItem","@id":"https:\/\/wasatchparc.com#listItem","name":"Home"}}]},{"@type":"CollectionPage","@id":"https:\/\/wasatchparc.com\/category\/blog\/#collectionpage","url":"https:\/\/wasatchparc.com\/category\/blog\/","name":"Blog - Wasatch Parc Snow Tubing","inLanguage":"en-US","isPartOf":{"@id":"https:\/\/wasatchparc.com\/#website"},"breadcrumb":{"@id":"https:\/\/wasatchparc.com\/category\/blog\/#breadcrumblist"}},{"@type":"Organization","@id":"https:\/\/wasatchparc.com\/#organization","name":"Wasatch Parc Snow Tubing","description":"Best tubing park in Utah","url":"https:\/\/wasatchparc.com\/","logo":{"@type":"ImageObject","url":"\/wp-content\/uploads\/2024\/12\/logo-bracelets.png","@id":"https:\/\/wasatchparc.com\/category\/blog\/#organizationLogo","width":1000,"height":100,"caption":"logo wasatch parc"},"image":{"@id":"https:\/\/wasatchparc.com\/category\/blog\/#organizationLogo"}},{"@type":"WebSite","@id":"https:\/\/wasatchparc.com\/#website","url":"https:\/\/wasatchparc.com\/","name":"Wasatch Parc Snow Tubing","description":"Best tubing park in Utah","inLanguage":"en-US","publisher":{"@id":"https:\/\/wasatchparc.com\/#organization"}}]}
		</script>
		<!-- All in One SEO -->

HTML,
];
?>

<section class="m-photo m-article-hero">
	<div class="m-conteneur m-article-entete">
		<p class="m-surtitre">Blog</p>
		<h1>Snow tubing tips &amp; stories</h1>
		<p class="m-hero-texte">What snow tubing is, how safe it is, and why it makes a great day out with family and friends.</p>
	</div>
</section>

<section class="m-section" aria-label="Articles">
	<div class="m-conteneur">
		<div class="m-articles">
<?php foreach (ARTICLES as $a): ?>
			<?= article_carte($a) ?>

<?php endforeach ?>
		</div>
	</div>
</section>

<section class="m-section m-section-fond" aria-label="Book">
	<div class="m-conteneur">
		<div class="m-appel">
			<div><h2>Ready to ride?</h2><p>The only snow tubing in Utah with banked turns, in Ogden Valley.</p></div>
			<div class="m-boutons"><a class="m-bouton m-bouton-principal m-bouton-moyen" href="<?= LIEN_RESERVATION ?>">Book a session</a><a class="m-bouton m-bouton-blanc m-bouton-moyen" href="/information/">Plan your visit</a></div>
		</div>
	</div>
</section>
