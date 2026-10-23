"""Captures pleine page du site en ligne et du site généré (_site/), en ordinateur (1366 px) et mobile (375 px),
puis comparaison pixel par pixel. Les iframes (YouTube, FareHarbor) et la popup sont masquées et les animations
coupées, car leur contenu change d'un chargement à l'autre.

Usage (depuis la racine du projet, après php tools/build.php) :
    python3 -I tools/captures.py [--base=https://wasatchparc.com]
Sorties : .cache/captures/<page>-<appareil>-{enligne,local,ecart}.png et un résumé à l'écran.
Prérequis : Google Chrome, php, websocket-client, numpy, Pillow.
"""
import base64, io, json, os, socket, subprocess, sys, tempfile, time, urllib.request
import numpy as np
import websocket
from PIL import Image

PROJ = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), '..'))
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from convertir import CHEMINS

OUT = os.path.join(PROJ, '.cache', 'captures')
OPTS = dict((a[2:].split('=', 1) + [''])[:2] for a in sys.argv[1:] if a.startswith('--'))
EN_LIGNE = OPTS.get('base', 'https://wasatchparc.com')
LOCAL = 'http://localhost:8091'
CHROME = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
APPAREILS = {'ordinateur': dict(width=1366, height=900, deviceScaleFactor=1, mobile=False),
             'mobile': dict(width=375, height=812, deviceScaleFactor=1, mobile=True)}
FIGER = """(() => { const s = document.createElement('style');
  s.textContent = '*,*::before,*::after{animation:none!important;transition:none!important;caret-color:transparent!important}'
    + 'iframe{visibility:hidden!important}'
    + '.sgpb-popup-dialog-main-div-wrapper,.sgpb-popup-overlay{display:none!important}html,body{overflow:visible!important}';
  document.head.appendChild(s);
  // Popup « Tubing closed » : son affichage dépend des visites précédentes, elle est vérifiée à part.
  document.querySelectorAll('.slick-slider').forEach(e => { try { jQuery(e).slick('slickPause'); jQuery(e).slick('slickGoTo', 0, true); } catch (x) {} });
  document.querySelectorAll('img[loading=lazy]').forEach(i => i.loading = 'eager');
  return document.documentElement.scrollHeight; })()"""


class Cdp:
    def __init__(self, ws_url):
        self.ws = websocket.create_connection(ws_url, timeout=120)
        self.n = 0

    def __call__(self, methode, **params):
        self.n += 1
        self.ws.send(json.dumps({'id': self.n, 'method': methode, 'params': params}))
        while True:
            r = json.loads(self.ws.recv())
            if r.get('id') == self.n:
                if 'error' in r:
                    raise RuntimeError(f'{methode} : {r["error"]}')
                return r.get('result', {})


def capture(cdp, url, appareil):
    cdp('Emulation.setDeviceMetricsOverride', **APPAREILS[appareil])
    cdp('Page.navigate', url=url)
    time.sleep(1)
    for _ in range(60):
        if cdp('Runtime.evaluate', expression='document.readyState', returnByValue=True)['result']['value'] == 'complete':
            break
        time.sleep(0.5)
    # Défilement jusqu'en bas pour déclencher le chargement des images, puis retour en haut.
    cdp('Runtime.evaluate', expression='(async()=>{for(let y=0;y<document.documentElement.scrollHeight;y+=400){scrollTo(0,y);await new Promise(r=>setTimeout(r,60))}scrollTo(0,0)})()', awaitPromise=True)
    hauteur = cdp('Runtime.evaluate', expression=FIGER, returnByValue=True)['result']['value']
    cdp('Runtime.evaluate', expression='document.fonts.ready.then(()=>1)', awaitPromise=True)
    time.sleep(1.5)
    m = APPAREILS[appareil]
    png = cdp('Page.captureScreenshot', format='png', captureBeyondViewport=True,
              clip=dict(x=0, y=0, width=m['width'], height=hauteur, scale=1))['data']
    return Image.open(io.BytesIO(base64.b64decode(png))).convert('RGB')


def comparer(a, b):
    h = max(a.height, b.height)
    pa, pb = (np.zeros((h, a.width, 3), np.uint8) for _ in range(2))
    pa[:a.height] = np.asarray(a); pb[:b.height] = np.asarray(b)
    diff = np.abs(pa.astype(np.int16) - pb.astype(np.int16)).max(axis=2) > 16
    ecart = pa.copy(); ecart[diff] = [255, 0, 0]
    return diff.sum(), Image.fromarray(ecart)


def main():
    os.makedirs(OUT, exist_ok=True)
    serveur = subprocess.Popen(['php', '-S', 'localhost:8091', '-t', os.path.join(PROJ, '_site')],
                               stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    profil = tempfile.mkdtemp()
    s = socket.socket(); s.bind(('', 0)); port = s.getsockname()[1]; s.close()
    chrome = subprocess.Popen([CHROME, '--headless=new', f'--remote-debugging-port={port}', f'--user-data-dir={profil}',
                               '--hide-scrollbars', '--remote-allow-origins=*', '--no-first-run', '--mute-audio', 'about:blank'],
                              stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
    try:
        for _ in range(50):
            try:
                cibles = json.load(urllib.request.urlopen(f'http://localhost:{port}/json'))
                break
            except Exception:
                time.sleep(0.2)
        cdp = Cdp(next(c for c in cibles if c['type'] == 'page')['webSocketDebuggerUrl'])
        cdp('Page.enable'); cdp('Runtime.enable')
        total = 0
        for nom, chemin in CHEMINS.items():
            for appareil in APPAREILS:
                a = capture(cdp, EN_LIGNE + ('/page-introuvable-test/' if nom == '404' else chemin), appareil)
                b = capture(cdp, LOCAL + chemin, appareil)
                n, ecart = comparer(a, b)
                total += n > 0
                for suffixe, img in (('enligne', a), ('local', b), ('ecart', ecart)):
                    img.save(os.path.join(OUT, f'{nom}-{appareil}-{suffixe}.png'))
                print(f'{"identique" if n == 0 else "ÉCART":9}  {chemin:40} {appareil:10} {a.size} / {b.size}  {n} px')
        sys.exit(1 if total else 0)
    finally:
        chrome.kill(); serveur.kill()


if __name__ == '__main__':
    main()
