#!/usr/bin/env bash
set -euo pipefail

# Run `git push` with your usual arguments, then deploy api/ to cPanel via rsync.
#
# Setup (pick one):
#   1) Export vars before running:
#        export CPANEL_SSH_HOST=tekmaxhosting.com
#        export CPANEL_SSH_USER=tekmaxhosting
#        export CPANEL_SSH_IDENTITY_FILE="$PWD/cpanel_deploy_key"
#   2) Create repo-root .cpanel-deploy.env (gitignored) with the same exports.
#
# Usage:
#   ./scripts/push-and-deploy-api.sh
#   ./scripts/push-and-deploy-api.sh origin payment
#   ./scripts/push-and-deploy-api.sh origin payment --dry-run   # deploy dry-run only

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

DEPLOY_EXTRA=()
GIT_ARGS=()
while (($#)); do
  case "$1" in
    --dry-run) DEPLOY_EXTRA+=(--dry-run) ;;
    *) GIT_ARGS+=("$1") ;;
  esac
  shift
done

if [[ -f "$ROOT_DIR/.cpanel-deploy.env" ]]; then
  set -a
  # shellcheck source=/dev/null
  source "$ROOT_DIR/.cpanel-deploy.env"
  set +a
fi

echo "==> git push ${GIT_ARGS[*]}"
git push "${GIT_ARGS[@]}"

echo "==> Deploy API to cPanel"
exec bash "$ROOT_DIR/scripts/deploy-cpanel.sh" api "${DEPLOY_EXTRA[@]}"
