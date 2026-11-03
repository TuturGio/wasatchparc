#!/bin/bash
# Envoie le site PHP sur Hostinger, dans ~/domains/wasatchparc.com/wasatchparc-php/.
# Ne touche ni à public_html (lien vers wasatchparc-php/public une fois la bascule faite) ni au WordPress conservé.
#
# Usage (depuis la racine du projet) : bash tools/deployer.sh
set -euo pipefail
cd "$(dirname "$0")/.."

SERVEUR=u475088640@86.38.202.124
PORT=65002
CIBLE=domains/wasatchparc.com/wasatchparc-php

if [ -n "$(git status --porcelain)" ]; then
    echo "Des modifications ne sont pas commitées : commit d'abord, pour que le serveur corresponde à un commit." >&2
    exit 1
fi

rsync -az --delete -e "ssh -p $PORT -o BatchMode=yes" \
    --exclude .git/ --exclude .cache/ --exclude _site/ --exclude .claude/ --exclude .github/ --exclude .DS_Store \
    ./ "$SERVEUR:$CIBLE/"
echo "Déployé : $(git log -1 --format='%h %s')"
