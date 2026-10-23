"""Nettoie les pages HTML brutes de WordPress (.cache/brut/) et les écrit dans .cache/propre/.

Le résultat est la référence que le site PHP doit reproduire à l'octet près (tools/verifier.py) :
- les adresses des fichiers (wp-content, wp-includes) deviennent relatives à la racine (/wp-content/…) ;
- les liens internes https://wasatchparc.com/… deviennent /…, sauf dans les balises SEO (canonical, og:, JSON-LD) ;
- les balises propres à WordPress qui n'ont plus de cible sont retirées (API REST, RSD, oEmbed, flux RSS,
  pingback, shortlink, generator, dns-prefetch inutilisé). Rien de ce qui s'affiche n'est modifié.

Usage (depuis la racine du projet) :
    python3 -I tools/nettoyer.py
"""
import os, re

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
BRUT = os.path.join(PROJ, '.cache', 'brut')
PROPRE = os.path.join(PROJ, '.cache', 'propre')

RETIRER = [
    r"<link rel='dns-prefetch' href='//www\.googletagmanager\.com' />\n",
    r'<link rel="alternate" type="application/rss\+xml"[^>]*/>\n',
    r'<link rel="https://api\.w\.org/"[^>]*/>',
    r'<link rel="alternate" title="JSON"[^>]*/>',
    r'<link rel="EditURI"[^>]*/>\n?',
    r'<meta name="generator" content="[^"]*" />\n?',
    r"<link rel='shortlink'[^>]*/>\n",
    r'<link rel="alternate" title="oEmbed[^>]*/>\n',
    r'<link rel="pingback"[^>]*>\n',
]


def nettoyer(h):
    for motif in RETIRER:
        h = re.sub(motif, '', h)
    # Fichiers : toujours relatifs à la racine, y compris dans le JSON échappé des scripts.
    h = re.sub(r'https?://wasatchparc\.com(/wp-(?:content|includes)/)', r'\1', h)
    h = re.sub(r'https?:\\/\\/wasatchparc\.com(\\/wp-(?:content|includes)\\/)', r'\1', h)
    # Liens internes dans les attributs href, hors balises <link rel="canonical"> et <meta>.
    def lien(m):
        balise = m.group(0)
        if balise.startswith('<link') or balise.startswith('<meta'):
            return balise
        return re.sub(r'href=(["\'])https://wasatchparc\.com/', r'href=\1/', balise)
    h = re.sub(r'<(?:a|link|meta)\b[^>]*>', lien, h)
    return h


def main():
    os.makedirs(PROPRE, exist_ok=True)
    for nom in sorted(os.listdir(BRUT)):
        if nom.endswith('.html'):
            h = open(os.path.join(BRUT, nom), encoding='utf-8').read()
            open(os.path.join(PROPRE, nom), 'w', encoding='utf-8').write(nettoyer(h))
            print(nom)


if __name__ == '__main__':
    main()
