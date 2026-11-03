# wasatchparc.com en PHP

Site PHP de wasatchparc.com, hébergé chez Hostinger (compte u475088640). Il remplace le WordPress depuis le 03/11/2026.

**Le serveur est la version de référence.** Les modifications se font directement dans
`~/domains/wasatchparc.com/wasatchparc-php/` ; `public_html` est un lien vers `wasatchparc-php/public`.
Après chaque modification, `bash tools/sauvegarder.sh "Description"` enregistre l'état dans git et l'envoie
sur GitHub (TuturGio/wasatchparc), pour pouvoir reconstruire le site en cas de panne du serveur.

## Organisation

- `content/` contient une page par fichier. Chaque fichier définit le tableau `$page` (adresse, nom, bloc SEO du `<head>`, styles ou scripts propres à la page), puis le contenu de la page.
- `includes/haut.php` et `includes/bas.php` sont le gabarit commun : `<head>`, bandeau de saison, en-tête, menu, pied de page et scripts.
- `includes/rendu.php` assemble une page ; `public/index.php` est le routeur (adresses, redirection `/x` → `/x/`, page 404, envoi du formulaire).
- `includes/config.php` : `SAISON_FERMEE` (bandeau « Closed for the … season »), `LIEN_RESERVATION`, `LIEN_CARTES_CADEAUX`, et le chemin du dossier privé.
- `includes/contact.php` : formulaire de contact envoyé par SMTP avec la boîte Hostinger `info@wasatchparc.com` (anti-spam : champ piège, jeton à usage unique, limite par IP).
- `includes/blog.php` : la liste des articles (titres, dates, extraits) pour la page Blog et le bas des articles.
- `public/assets/css/moderne.css` est la feuille de style du site ; `page-404.css` celle de la page 404.
- `public/wp-content/uploads/` garde les images aux mêmes adresses que sous WordPress.
- `~/wasatchparc-prive/` (hors du site et hors de git) : mot de passe SMTP, clé secrète des jetons, fichiers anti-spam.

## Modifier le site

1. Modifier le fichier de la page dans `content/` (ou le gabarit dans `includes/`) sur le serveur.
2. Vérifier la page en ligne. Après une modification de CSS ou d'image, changer son numéro `?ver=` dans les pages : Cloudflare garde ces fichiers en cache six mois (`moderne.css` le fait tout seul).
3. `bash tools/sauvegarder.sh "Ce qui a changé"`.

Revenir en arrière : `git log` puis `git revert <commit>` (ou `git checkout <commit> -- <fichier>`), puis sauvegarder.

Retour au WordPress (conservé dans `public_html-wordpress`, base `u475088640_2hTte` intacte) :
`cd ~/domains/wasatchparc.com && rm public_html && mv public_html-wordpress public_html`.
Sauvegarde d'avant bascule : `~/sauvegardes/bascule-20261103-1519/`.

`php tools/build.php` génère une copie statique du site dans `_site/` (ancienne publication GitHub Pages, arrêtée le 03/11/2026).
Le gabarit repris de WordPress (Astra) et les fichiers des extensions ont été supprimés le 03/11/2026 ; ils restent dans l'historique git.
