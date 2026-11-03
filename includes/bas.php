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
// Sur mobile et tablette, le script FareHarbor remplace le site par sa page de réservation dans le même onglet.
// Cet écouteur, posé sur window avant lui, passe en premier : il arrête le clic sur un lien de réservation
// et l'ouvre dans un nouvel onglet. Sur ordinateur, FareHarbor garde sa fenêtre par-dessus la page.
window.addEventListener('click', function (e) {
	var lien = e.target.closest && e.target.closest('a[href*="fareharbor.com/embeds/book"]');
	if (lien && window.matchMedia('(max-width: 900px), (pointer: coarse)').matches) {
		e.stopImmediatePropagation();
		lien.target = '_blank';
		lien.rel = 'noopener';
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
