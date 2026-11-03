<?php
/** Fin de chaque page : pied de page et scripts. @var array $page */
?>
</main>
<footer class="m-pied">
	<div class="m-conteneur">
		<div class="m-pied-haut">
			<div class="m-pied-marque">
				<span class="m-logo m-logo-petit"><img src="/wp-content/uploads/2024/12/logo-bracelets.png" width="200" height="20" alt="Wasatch Parc" loading="lazy"></span>
				<p>Snow tubing with banked turns in Ogden Valley, Utah.</p>
			</div>
			<nav class="m-pied-menu" aria-label="Footer">
				<a href="/#prices">Prices</a>
				<a href="/information/">Plan your visit</a>
				<a href="/category/blog/">Blog</a>
				<a href="/contact/">Contact</a>
			</nav>
		</div>
		<div class="m-pied-bas">
			<p>3567 Nordic Valley Way, Eden, UT 84310 · <a href="tel:+18003919954">800-391-9954</a> · <a href="mailto:info@wasatchparc.com">info@wasatchparc.com</a></p>
			<p>Copyright © <?= date('Y') ?> Wasatch Parc – Giocanti-Milelli LLC</p>
		</div>
	</div>
</footer>
<div class="m-reserver-mobile"><a class="m-bouton m-bouton-principal" href="<?= LIEN_RESERVATION ?>" target="_blank" rel="noopener">Book now</a></div>
<script>
// Bouton « Book now » fixe du mobile : la réservation s'ouvre dans un nouvel onglet. Le script FareHarbor
// intercepte sinon le clic et quitte le site. Cet écouteur, posé sur window avant lui, passe en premier
// et arrête le clic ; le lien (target="_blank") s'ouvre alors normalement.
window.addEventListener('click', function (e) {
	if (e.target.closest && e.target.closest('.m-reserver-mobile a')) {
		e.stopImmediatePropagation();
	}
}, true);
</script>
<script src="https://fareharbor.com/embeds/api/v1/?autolightframe=yes"></script>
<script>
// Menu mobile : ouverture et fermeture par le bouton ☰.
(function () {
	var bouton = document.querySelector('.m-menu-bouton');
	bouton.addEventListener('click', function () {
		var ouvert = bouton.getAttribute('aria-expanded') !== 'true';
		bouton.setAttribute('aria-expanded', ouvert);
		bouton.setAttribute('aria-label', ouvert ? 'Close menu' : 'Open menu');
		document.body.classList.toggle('m-menu-ouvert', ouvert);
	});
})();
// Vidéo YouTube chargée seulement au clic (page plus légère, pas de cookie YouTube avant).
document.querySelectorAll('[data-youtube]').forEach(function (lien) {
	lien.addEventListener('click', function (e) {
		e.preventDefault();
		var cadre = document.createElement('iframe');
		cadre.src = 'https://www.youtube-nocookie.com/embed/' + lien.dataset.youtube + '?autoplay=1';
		cadre.title = lien.getAttribute('aria-label');
		cadre.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
		cadre.allowFullscreen = true;
		lien.replaceWith(cadre);
	});
});
</script>
<?= $page['scripts'] ?? '' ?>
</body>
</html>
