# wasatchparc.com en PHP

Site PHP de wasatchparc.com, hébergé chez Hostinger (compte u475088640). Il remplace le WordPress depuis le 03/11/2026.

**Le serveur est la version de référence.** Les modifications se font directement dans
`~/domains/wasatchparc.com/wasatchparc-php/` ; `public_html` est un lien vers `wasatchparc-php/public`.
Après chaque modification, `bash tools/sauvegarder.sh "Description"` enregistre l'état dans git et l'envoie
sur GitHub (TuturGio/wasatchparc), pour pouvoir reconstruire le site en cas de panne du serveur.

## Organisation

- `content/` contient une page par fichier. Chaque fichier définit le tableau `$page` (SEO, mise en page, scripts), puis le contenu de la zone `#primary`.
- `includes/haut.php` et `includes/bas.php` sont les gabarits communs : `<head>`, en-tête, menu, pied de page et scripts.
- `includes/functions.php` contient le logo et le menu (l'entrée active est calculée).
- `includes/config.php` contient la clé Web3Forms du formulaire de contact.
- `public/` est copié tel quel. On y trouve les images, CSS et JS repris de WordPress (aux mêmes chemins `wp-content/…`), ainsi que `CNAME`, `robots.txt` et `sitemap.xml`.

## Modifier le site

1. Modifier le fichier de la page dans `content/` (ou un gabarit dans `includes/`) sur le serveur.
2. Vérifier la page en ligne.
3. `bash tools/sauvegarder.sh "Ce qui a changé"`.

Revenir en arrière : `git log` puis `git revert <commit>` (ou `git checkout <commit> -- <fichier>`), puis sauvegarder.

Retour au WordPress (conservé dans `public_html-wordpress`, base `u475088640_2hTte` intacte) :
`cd ~/domains/wasatchparc.com && rm public_html && mv public_html-wordpress public_html`.
Sauvegarde d'avant bascule : `~/sauvegardes/bascule-20261103-1519/`.

`php tools/build.php` génère encore une version statique dans `_site/` (ancienne publication GitHub Pages, arrêtée le 03/11/2026).

## Outils de migration (à lancer une seule fois)

- `tools/aspirer.py` copie les fichiers du WordPress en ligne dans `public/`.
- `tools/nettoyer.py` puis `tools/convertir.py` transforment les pages WordPress en gabarits et en contenus PHP. **Ne pas relancer `convertir.py`** : il écraserait les modifications faites à la main, à commencer par le formulaire de contact.
- `tools/verifier.py` compare le HTML généré à celui de WordPress, à l'octet près.
- `tools/captures.py` compare les captures d'écran du site en ligne et de `_site/` (sur ordinateur et sur mobile).

Ces outils ont besoin de `.cache/brut/`, les pages WordPress téléchargées le 23/10/2026. Ce dossier n'est pas versionné.
