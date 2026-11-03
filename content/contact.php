<?php
$page = [
    'chemin' => '/contact/',
    'nom' => 'contact',
    'seo' => <<<'HTML'
	<title>Wasatch Parc Snow Tubing - Contact</title>
	<style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>
	
		<!-- All in One SEO 4.9.2 - aioseo.com -->
	<meta name="description" content="Contact us. Wasatch Parc is the only snow tubing with curved lanes. Come enjoy the snow and these beautiful mountains in Ogden Valley at Nordic Valley resort" />
	<meta name="robots" content="max-image-preview:large" />
	<link rel="canonical" href="https://wasatchparc.com/contact/" />
			<meta property="og:locale" content="en_US" />
		<meta property="og:site_name" content="Wasatch Parc Snow Tubing - Best tubing park in Utah" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="Wasatch Parc Snow Tubing - Contact" />
		<meta property="og:description" content="Contact us. Wasatch Parc is the only snow tubing with curved lanes. Come enjoy the snow and these beautiful mountains in Ogden Valley at Nordic Valley resort" />
		<meta property="og:url" content="https://wasatchparc.com/contact/" />
		<meta property="og:image" content="https://wasatchparc.com/wp-content/uploads/2024/12/logo-bracelets.png" />
		<meta property="og:image:secure_url" content="https://wasatchparc.com/wp-content/uploads/2024/12/logo-bracelets.png" />
		<meta property="article:published_time" content="2024-12-03T22:06:25+00:00" />
		<meta property="article:modified_time" content="2024-12-22T03:51:49+00:00" />
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:title" content="Wasatch Parc Snow Tubing - Contact" />
		<meta name="twitter:description" content="Contact us. Wasatch Parc is the only snow tubing with curved lanes. Come enjoy the snow and these beautiful mountains in Ogden Valley at Nordic Valley resort" />
		<meta name="twitter:image" content="https://wasatchparc.com/wp-content/uploads/2024/12/logo-bracelets.png" />
		<script type="application/ld+json" class="aioseo-schema">
			{"@context":"https:\/\/schema.org","@graph":[{"@type":"BreadcrumbList","@id":"https:\/\/wasatchparc.com\/contact\/#breadcrumblist","itemListElement":[{"@type":"ListItem","@id":"https:\/\/wasatchparc.com#listItem","position":1,"name":"Home","item":"https:\/\/wasatchparc.com","nextItem":{"@type":"ListItem","@id":"https:\/\/wasatchparc.com\/contact\/#listItem","name":"Contact"}},{"@type":"ListItem","@id":"https:\/\/wasatchparc.com\/contact\/#listItem","position":2,"name":"Contact","previousItem":{"@type":"ListItem","@id":"https:\/\/wasatchparc.com#listItem","name":"Home"}}]},{"@type":"Organization","@id":"https:\/\/wasatchparc.com\/#organization","name":"Wasatch Parc Snow Tubing","description":"Best tubing park in Utah","url":"https:\/\/wasatchparc.com\/","logo":{"@type":"ImageObject","url":"\/wp-content\/uploads\/2024\/12\/logo-bracelets.png","@id":"https:\/\/wasatchparc.com\/contact\/#organizationLogo","width":1000,"height":100,"caption":"logo wasatch parc"},"image":{"@id":"https:\/\/wasatchparc.com\/contact\/#organizationLogo"}},{"@type":"WebPage","@id":"https:\/\/wasatchparc.com\/contact\/#webpage","url":"https:\/\/wasatchparc.com\/contact\/","name":"Wasatch Parc Snow Tubing - Contact","description":"Contact us. Wasatch Parc is the only snow tubing with curved lanes. Come enjoy the snow and these beautiful mountains in Ogden Valley at Nordic Valley resort","inLanguage":"en-US","isPartOf":{"@id":"https:\/\/wasatchparc.com\/#website"},"breadcrumb":{"@id":"https:\/\/wasatchparc.com\/contact\/#breadcrumblist"},"datePublished":"2024-12-03T22:06:25+00:00","dateModified":"2024-12-22T03:51:49+00:00"},{"@type":"WebSite","@id":"https:\/\/wasatchparc.com\/#website","url":"https:\/\/wasatchparc.com\/","name":"Wasatch Parc Snow Tubing","description":"Best tubing park in Utah","inLanguage":"en-US","publisher":{"@id":"https:\/\/wasatchparc.com\/#organization"}}]}
		</script>
		<!-- All in One SEO -->

HTML,
];
?>

<section class="m-photo m-contact-hero">
	<div class="m-conteneur">
		<p class="m-surtitre">Contact us</p>
		<h1>Let's talk.</h1>
		<p class="m-hero-texte">A question about tickets, a group or a private session? Write to us or give us a call.</p>
	</div>
</section>

<section class="m-contact-corps" aria-label="Contact form and details">
	<div class="m-conteneur m-contact-grille">
<?php if (isset($_GET['sent'])): ?>
		<div class="m-formulaire" id="formulaire">
			<h2>Message sent</h2>
			<p class="m-confirmation" role="status">Thanks for contacting us! We will be in touch with you shortly.</p>
			<p><a href="/contact/">Send another message</a></p>
		</div>
<?php else: ?>
		<form class="m-formulaire" id="formulaire" method="post" action="/contact/#formulaire">
			<div>
				<h2>Send us a message</h2>
				<p class="m-intro">We'll get back to you by email.</p>
			</div>
			<?= contact_message() ?>
			<div class="m-champs-duo">
				<div class="m-champ"><label for="c-prenom">First name <span class="m-requis" aria-hidden="true">*</span></label><input id="c-prenom" name="prenom" type="text" required autocomplete="given-name" maxlength="100" value="<?= contact_valeur('prenom') ?>"><?= contact_erreur('prenom') ?></div>
				<div class="m-champ"><label for="c-nom">Last name <span class="m-requis" aria-hidden="true">*</span></label><input id="c-nom" name="nom" type="text" required autocomplete="family-name" maxlength="100" value="<?= contact_valeur('nom') ?>"><?= contact_erreur('nom') ?></div>
			</div>
			<div class="m-champ"><label for="c-email">Email <span class="m-requis" aria-hidden="true">*</span></label><input id="c-email" name="email" type="email" required autocomplete="email" maxlength="254" spellcheck="false" placeholder="you@example.com" value="<?= contact_valeur('email') ?>"><?= contact_erreur('email') ?></div>
			<div class="m-champ"><label for="c-message">Comment or message</label><textarea id="c-message" name="message" rows="6" maxlength="5000"><?= contact_valeur('message') ?></textarea><?= contact_erreur('message') ?></div>
			<div class="m-champ m-champ-piege" aria-hidden="true"><label for="c-site">Leave this field empty</label><input id="c-site" name="site" type="text" tabindex="-1" autocomplete="off"></div>
			<input type="hidden" name="_jeton" value="<?= contact_jeton() ?>">
			<div class="m-formulaire-pied">
				<p><span class="m-requis">*</span> Required fields</p>
				<button class="m-bouton m-bouton-principal" type="submit">Send message</button>
			</div>
		</form>
<?php endif ?>

		<aside class="m-coordonnees" aria-label="Contact details">
			<div class="m-coordonnees-bloc">
				<a class="m-coordonnee" href="tel:+18003919954">
					<span class="m-icone" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg></span>
					<span><small>Call us</small><strong>800-391-9954</strong></span>
				</a>
				<a class="m-coordonnee" href="mailto:info@wasatchparc.com">
					<span class="m-icone" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg></span>
					<span><small>Email us</small><strong>info@wasatchparc.com</strong></span>
				</a>
				<div class="m-coordonnee">
					<span class="m-icone" aria-hidden="true"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg></span>
					<span><small>Address</small><strong>3567 Nordic Valley Way<br>Eden, UT 84310</strong><small>Park in lot P3</small></span>
				</div>
			</div>
			<div class="m-plan"><img src="/wp-content/uploads/2024/12/plan_OK.png" width="400" height="400" alt="Map: Wasatch Parc near Eden and Nordic Valley" loading="lazy"></div>
		</aside>
	</div>
</section>

<section class="m-section" aria-labelledby="titre-groupes-contact">
	<div class="m-conteneur m-groupes-contact">
		<div><h2 id="titre-groupes-contact">Planning a group?</h2><p class="m-intro">Tell us your date and how many people are coming.</p></div>
		<div class="m-encart"><p class="m-offre-chiffre">20% off</p><h3>Groups of 20 or more</h3><p>One single online payment. Our team assists you by email or phone.</p></div>
		<div class="m-encart"><p class="m-offre-chiffre">Up to 50</p><h3>Private session</h3><p>Ask us for a quote. Not available during peak periods and weekends.</p></div>
	</div>
</section>
