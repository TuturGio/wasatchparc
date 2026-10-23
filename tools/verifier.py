"""Compare chaque page générée (_site/) à la page WordPress nettoyée (.cache/propre/), à l'octet près.

Usage (depuis la racine du projet, après php tools/build.php) :
    python3 -I tools/verifier.py
"""
import difflib, os, sys

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from convertir import CHEMINS

ecarts = 0
for nom, chemin in CHEMINS.items():
    ref = open(os.path.join(PROJ, '.cache', 'propre', nom + '.html'), encoding='utf-8').read()
    gen = open(os.path.join(PROJ, '_site', chemin.strip('/'), 'index.html'), encoding='utf-8').read()
    if ref == gen:
        print(f'identique  {chemin}')
        continue
    ecarts += 1
    print(f'DIFFÉRENT  {chemin}')
    for l in list(difflib.unified_diff(ref.splitlines(), gen.splitlines(), 'wordpress', 'php', n=1, lineterm=''))[:20]:
        print('    ' + l[:200])
sys.exit(1 if ecarts else 0)
