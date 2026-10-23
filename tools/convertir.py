"""Outil de migration, à lancer une fois : transforme les pages nettoyées (.cache/propre/) en site PHP.

Produit :
- includes/haut.php et includes/bas.php, gabarits communs tirés de la page d'accueil, où chaque partie
  qui change d'une page à l'autre est remplacée par une valeur de $page ;
- includes/styles/astra-<mise en page>.css, les 4 variantes du CSS en ligne du thème Astra ;
- content/<chemin>.php pour chaque page : le tableau $page puis le contenu de la zone #primary.

tools/verifier.py contrôle ensuite que le rendu PHP redonne chaque page nettoyée à l'octet près.
Une fois le site repris à la main, ne plus relancer ce script : il écraserait les modifications.

Usage (depuis la racine du projet) :
    python3 -I tools/convertir.py
"""
import os, re

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
PROPRE = os.path.join(PROJ, '.cache', 'propre')
CHEMINS = {
    'accueil': '/',
    'contact': '/contact/',
    'information': '/information/',
    'category_blog': '/category/blog/',
    '5-amazing-benefits-of-winter-sports': '/5-amazing-benefits-of-winter-sports/',
    'how-safe-is-snow-tubing': '/how-safe-is-snow-tubing/',
    'what-is-snow-tubing-and-5-reasons-why-its-a-fun-family-activity': '/what-is-snow-tubing-and-5-reasons-why-its-a-fun-family-activity/',
}
DEBUT_PRIMARY = '<div id="primary" class="content-area primary">'
FIN_PRIMARY = '\t</div><!-- #primary -->'

# Parties variables : nom -> motif à un groupe. Le groupe est remplacé dans le gabarit par du PHP.
HAUT = {
    'seo': r'(\t<title>.*?<!-- All in One SEO -->\n)',
    'astra': r"<style id='astra-theme-css-inline-css'>\n(.*?)</style>",
    'styles': r"<link rel='stylesheet' id='theme\.css-css'[^\n]*\n(.*?)<!--\[if IE\]>",
    'uag': r'<style id="uagb-style-conditional-extension">(.*?)</style><link rel="icon"',
    'corps': r"(<body itemtype='[^']*' itemscope='itemscope' class=\"[^\"]*\">)",
}
BAS = {
    'styles_bas': r"  </div>((?:<style id='core-block-supports-inline-css'>.*?</style>\n)?)<script id=\"astra-theme-js-js-extra\">",
    'scripts': r'id="starter-templates-zip-preview-js"></script>\n(.*?)\t\t\t<script>\n\t\t\t/\(trident',
    'fin': r'\n\t\t\t</script>\n(.*?)</body>',
}
PHP = {
    'seo': "<?= $page['seo'] ?>",
    'astra': "<?php readfile(__DIR__ . '/styles/astra-' . $page['mise_en_page'] . '.css') ?>",
    'styles': "<?= $page['styles'] ?? '' ?>",
    'uag': "<?= $page['uag'] ?>",
    'corps': "<body itemtype='<?= $page['itemtype'] ?>' itemscope='itemscope' class=\"<?= $page['classes'] ?>\">",
    'styles_bas': "<?= $page['styles_bas'] ?? '' ?>",
    'scripts': "<?= $page['scripts'] ?? '' ?>",
    'fin': "<?= $page['fin'] ?>",
}
LOGO = r'(\t\t\t\t\t<span class="site-logo-img">[^\n]*\n)'
MENU = r'<li(?: id="menu-item-\d+")? class="[^"]*"><a [^>]*>[^<]*</a></li>\n'


def remplacer_groupe(texte, motif, par):
    m = re.search(motif, texte, re.S)
    if not m:
        raise SystemExit(f'Motif introuvable : {motif}')
    return texte[:m.start(1)] + par + texte[m.end(1):]


def valeur(texte, motif):
    m = re.search(motif, texte, re.S)
    if not m:
        raise SystemExit(f'Motif introuvable : {motif}')
    return m.group(1)


def mise_en_page(classes):
    classes = classes.split()
    if 'ast-page-builder-template' in classes:
        return 'pleine-largeur'
    if 'single-post' in classes:
        return 'article'
    if 'archive' in classes:
        return 'archive'
    return 'page'


def php_chaine(s):
    """Chaîne PHP littérale : nowdoc si elle contient des sauts de ligne ou des apostrophes."""
    if '\n' not in s and "'" not in s and '\\' not in s:
        return "'" + s + "'"
    assert '\nHTML' not in s
    return "<<<'HTML'\n" + s + "\nHTML"


def decouper(h):
    a = h.index(DEBUT_PRIMARY) + len(DEBUT_PRIMARY)
    b = h.index(FIN_PRIMARY)
    return h[:a], h[a:b], h[b:]


def main():
    os.makedirs(os.path.join(PROJ, 'includes', 'styles'), exist_ok=True)
    for nom, chemin in CHEMINS.items():
        h = open(os.path.join(PROPRE, nom + '.html'), encoding='utf-8').read()
        haut, contenu, bas = decouper(h)
        corps = valeur(haut, HAUT['corps'])
        itemtype, classes = re.match(r"<body itemtype='([^']*)' itemscope='itemscope' class=\"([^\"]*)\">", corps).groups()
        page = {
            'chemin': chemin,
            'seo': valeur(haut, HAUT['seo']),
            'mise_en_page': mise_en_page(classes),
            'styles': valeur(haut, HAUT['styles']),
            'itemtype': itemtype,
            'classes': classes,
            'uag': valeur(haut, HAUT['uag']),
            'entete_transparent': 'ast-theme-transparent-header' in classes.split(),
            'styles_bas': valeur(bas, BAS['styles_bas']),
            'scripts': valeur(bas, BAS['scripts']),
            'fin': valeur(bas, BAS['fin']),
        }
        css = os.path.join(PROJ, 'includes', 'styles', 'astra-' + page['mise_en_page'] + '.css')
        astra = valeur(haut, HAUT['astra'])
        if os.path.exists(css) and open(css, encoding='utf-8').read() != astra:
            raise SystemExit(f'Deux CSS Astra différents pour la mise en page {page["mise_en_page"]}')
        open(css, 'w', encoding='utf-8').write(astra)

        lignes = ['<?php', '$page = [']
        for cle, v in page.items():
            if v == '' and cle in ('styles', 'styles_bas', 'scripts'):
                continue
            lignes.append(f"    '{cle}' => " + ('true' if v is True else 'false' if v is False else php_chaine(v)) + ',')
        lignes.append('];')
        lignes.append('?>\n')
        dest = os.path.join(PROJ, 'content', ('accueil' if chemin == '/' else chemin.strip('/')) + '.php')
        os.makedirs(os.path.dirname(dest), exist_ok=True)
        open(dest, 'w', encoding='utf-8').write('\n'.join(lignes) + contenu)
        print(dest[len(PROJ) + 1:])

        if nom == 'accueil':
            for cle, motif in HAUT.items():
                haut = remplacer_groupe(haut, motif, PHP[cle])
            haut = re.sub(LOGO, "<?php logo($page, true) ?>\n", haut, count=1)
            haut = re.sub(LOGO, "<?php logo($page, false) ?>\n", haut, count=1)
            haut = re.sub(r'(<ul id="ast-hf-menu-1" class="[^"]*">)' + MENU + MENU, r'\1<?php menu($page, true) ?>', haut, count=1)
            haut = re.sub(r'(<ul id="ast-hf-menu-1" class="[^"]*">)' + MENU + MENU, r'\1<?php menu($page, false) ?>', haut, count=1)
            for cle, motif in BAS.items():
                bas = remplacer_groupe(bas, motif, PHP[cle])
            # PHP avale le saut de ligne qui suit « ?> » : on le double pour qu'il reste dans la page.
            # logo() écrit lui-même la fin de sa ligne.
            haut, bas = (re.sub(r'(?<!logo\(\$page, true\) )(?<!logo\(\$page, false\) )\?>\n', '?>\n\n', t) for t in (haut, bas))
            open(os.path.join(PROJ, 'includes', 'haut.php'), 'w', encoding='utf-8').write(haut)
            open(os.path.join(PROJ, 'includes', 'bas.php'), 'w', encoding='utf-8').write(bas)


if __name__ == '__main__':
    main()
