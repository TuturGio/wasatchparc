"""Compare chaque page générée (_site/) à la page WordPress nettoyée (.cache/propre/), à l'octet près.

Les pages listées dans VOULUS ont été modifiées exprès et ne sont pas comparées.

Usage (depuis la racine du projet, après php tools/build.php) :
    python3 -I tools/verifier.py
"""
import difflib, os, sys

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from convertir import CHEMINS

# Pages modifiées volontairement après la migration : leurs écarts ne sont pas des erreurs.
VOULUS = {'contact': 'formulaire envoyé par Web3Forms au lieu de WPForms'}

ecarts = 0
for nom, chemin in CHEMINS.items():
    ref = open(os.path.join(PROJ, '.cache', 'propre', nom + '.html'), encoding='utf-8').read()
    fichier = chemin.strip('/') if chemin.endswith('.html') else os.path.join(chemin.strip('/'), 'index.html')
    gen = open(os.path.join(PROJ, '_site', fichier), encoding='utf-8').read()
    if ref == gen:
        print(f'identique  {chemin}')
        continue
    if nom in VOULUS:
        print(f'modifié    {chemin}  ({VOULUS[nom]})')
        continue
    ecarts += 1
    print(f'DIFFÉRENT  {chemin}')
    for l in list(difflib.unified_diff(ref.splitlines(), gen.splitlines(), 'wordpress', 'php', n=1, lineterm=''))[:20]:
        print('    ' + l[:200])
sys.exit(1 if ecarts else 0)
