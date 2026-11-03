#!/bin/bash
# Enregistre l'état actuel du site dans git et l'envoie sur GitHub (TuturGio/wasatchparc), en cas de panne du serveur.
# À lancer sur le serveur après chaque modification ; peut aussi tourner en tâche cron (hPanel) pour rattraper
# les modifications faites à la main. Ne fait rien s'il n'y a aucun changement.
#
# Usage : bash tools/sauvegarder.sh "Description de la modification"
set -euo pipefail
cd "$(dirname "$0")/.."

git add -A
if ! git diff --cached --quiet; then
    git commit -q -m "${1:-Sauvegarde du $(date '+%d/%m/%Y %H:%M')}"
fi
if [ -n "$(git log origin/main..HEAD --oneline 2>/dev/null)" ] || ! git rev-parse -q --verify origin/main >/dev/null; then
    git push -q origin main
fi
echo "GitHub à jour : $(git log -1 --format='%h %s')"
