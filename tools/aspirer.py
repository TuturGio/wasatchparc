"""Copie locale des fichiers du site WordPress en ligne (CSS, JS, images, polices) dans public/,
aux mêmes chemins que sur WordPress (wp-content/…, wp-includes/…), pour que le site PHP soit autonome.

Lit les pages HTML brutes de .cache/brut/ (téléchargées par tools/telecharger.sh), relève toutes les
adresses wasatchparc.com/wp-content et /wp-includes (attributs, srcset, styles en ligne, JSON échappé),
puis suit les url() et @import des feuilles CSS. Lecture seule sur le site en ligne.

Usage (depuis la racine du projet) :
    python3 -I tools/aspirer.py [adresse supplémentaire …]
"""
import os, re, sys, time, urllib.parse as up, urllib.request

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
BRUT = os.path.join(PROJ, '.cache', 'brut')
PUBLIC = os.path.join(PROJ, 'public')
SITE = 'https://wasatchparc.com'
UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126 Safari/537.36'
MOTIF = re.compile(r'(?:https?:)?(?:\\?/\\?/)?wasatchparc\.com(\\?/wp-(?:content|includes)\\?/[^\s"\'()<>,;]*)')


def chemin_local(url):
    p = up.unquote(up.urlsplit(url).path)
    return os.path.join(PUBLIC, p.lstrip('/'))


def telecharger(url):
    dest = chemin_local(url)
    if os.path.isfile(dest):
        return dest, False
    req = urllib.request.Request(url, headers={'User-Agent': UA})
    try:
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
    except Exception as e:
        print(f'  ÉCHEC {url} : {e}')
        return None, False
    os.makedirs(os.path.dirname(dest), exist_ok=True)
    with open(dest, 'wb') as f:
        f.write(data)
    time.sleep(0.3)
    return dest, True


def adresses_html(texte):
    for m in MOTIF.finditer(texte):
        chemin = m.group(1).replace('\\/', '/')
        chemin = re.split(r'\\?&(?:quot|#0?39|#x27)', chemin)[0]
        chemin = re.sub(r'&(amp|#038);', '&', chemin).split('?')[0].split('#')[0]
        if chemin.rstrip('/') and not chemin.endswith('/'):
            yield SITE + chemin


def adresses_css(url, texte):
    for m in re.finditer(r'url\(\s*["\']?([^"\')]+)["\']?\s*\)|@import\s+["\']([^"\']+)["\']', texte):
        ref = (m.group(1) or m.group(2)).strip()
        if ref.startswith('data:') or ref.startswith('#'):
            continue
        abs_ = up.urljoin(url, ref).split('?')[0].split('#')[0]
        if up.urlsplit(abs_).netloc == 'wasatchparc.com':
            yield abs_


def main():
    a_faire = set(sys.argv[1:])
    for nom in os.listdir(BRUT):
        if nom.endswith('.html'):
            a_faire.update(adresses_html(open(os.path.join(BRUT, nom), encoding='utf-8').read()))
    vus = set()
    while a_faire:
        url = a_faire.pop()
        if url in vus:
            continue
        vus.add(url)
        dest, nouveau = telecharger(url)
        if nouveau:
            print('  +', up.urlsplit(url).path)
        if dest and dest.endswith('.css'):
            a_faire.update(set(adresses_css(url, open(dest, encoding='utf-8', errors='replace').read())) - vus)
    print(f'{len(vus)} fichiers relevés.')


if __name__ == '__main__':
    main()
