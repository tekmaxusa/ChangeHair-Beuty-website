#!/usr/bin/env bash
# First API sanity check: GET /cardconnect/rest/inquireMerchant/{merchid}
#
# Option A — secrets in api/.env (recommended; same as booking API):
#   cp api/.env.example api/.env
#   # set CARDCONNECT_* in api/.env
#   ./scripts/cardconnect-inquire-merchant.sh
#
# Option A2 — optional overlay scripts/.env.cardconnect (e.g. test PAN only); merged after API .env
#
# Option B — export in your shell before running:
#   export CARDCONNECT_BASE_URL=... CARDCONNECT_MERCHID=... CARDCONNECT_API_USER=... CARDCONNECT_API_PASSWORD=...
#   ./scripts/cardconnect-inquire-merchant.sh
#
# Do not put real passwords in this script if the repo is ever pushed anywhere.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=/dev/null
source "${SCRIPT_DIR}/_cardconnect_env.sh"

: "${CARDCONNECT_BASE_URL:?Set CARDCONNECT_BASE_URL (e.g. https://fts.cardconnect.com)}"
: "${CARDCONNECT_MERCHID:?Set CARDCONNECT_MERCHID}"
: "${CARDCONNECT_API_USER:?Set CARDCONNECT_API_USER}"
: "${CARDCONNECT_API_PASSWORD:?Set CARDCONNECT_API_PASSWORD}"

BASE="${CARDCONNECT_BASE_URL%/}"
URL="${BASE}/cardconnect/rest/inquireMerchant/${CARDCONNECT_MERCHID}"

curl -sS -u "${CARDCONNECT_API_USER}:${CARDCONNECT_API_PASSWORD}" \
  -H "Content-Type: application/json" \
  -w "\n\nHTTP_STATUS:%{http_code}\n" \
  "${URL}"
