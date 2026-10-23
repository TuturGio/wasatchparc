# wasatchparc.com en PHP

Le site est écrit en PHP, puis compilé en HTML statique et publié sur GitHub Pages. Il remplace le WordPress hébergé chez Hostinger.

## Organisation

- `content/` contient une page par fichier. Chaque fichier définit le tableau `$page` (SEO, mise en page, scripts), puis le contenu de la zone `#primary`.
- `includes/haut.php` et `includes/bas.php` sont les gabarits communs : `<head>`, en-tête, menu, pied de page et scripts.
- `includes/functions.php` contient le logo et le menu (l'entrée active est calculée).
- `includes/config.php` contient la clé Web3Forms du formulaire de contact.
- `public/` est copié tel quel. On y trouve les images, CSS et JS repris de WordPress (aux mêmes chemins `wp-content/…`), ainsi que `CNAME`, `robots.txt` et `sitemap.xml`.

## Modifier le site

```bash
php tools/build.php
php -S localhost:8090 -t _site
```

Ensuite, un `git push` sur `main` suffit : l'action `.github/workflows/pages.yml` regénère le site et le publie.

## Outils de migration (à lancer une seule fois)

- `tools/aspirer.py` copie les fichiers du WordPress en ligne dans `public/`.
- `tools/nettoyer.py` puis `tools/convertir.py` transforment les pages WordPress en gabarits et en contenus PHP. **Ne pas relancer `convertir.py`** : il écraserait les modifications faites à la main, à commencer par le formulaire de contact.
- `tools/verifier.py` compare le HTML généré à celui de WordPress, à l'octet près.
- `tools/captures.py` compare les captures d'écran du site en ligne et de `_site/` (sur ordinateur et sur mobile).

Ces outils ont besoin de `.cache/brut/`, les pages WordPress téléchargées le 23/10/2026. Ce dossier n'est pas versionné.
