#!/usr/bin/env bash
# CardSecure tokenize (test PAN) + Gateway auth with ecomind=E (ecommerce).
# Use after inquireMerchant works. Check CardPointe Reporting for the new txn.
#
# Loads changehair-api/.env (and optionally scripts/.env.cardconnect) via _cardconnect_env.sh.
#
# Optional env overrides:
#   CARDCONNECT_TEST_AMOUNT   default 1.00
#   CARDCONNECT_TEST_POSTAL   default 80124 (AVS)
#   CARDCONNECT_TEST_NAME     default "Test User" (cardholder / AVS)
#   CARDCONNECT_TEST_ADDRESS  default "123 Test St"
#   CARDCONNECT_TEST_CITY     default "Denver"
#   CARDCONNECT_TEST_STATE    default "CO"
#   CARDCONNECT_TEST_COUNTRY  default US
#   CARDCONNECT_TEST_PAN      default 4111111111111111 (CardConnect test Visa — confirm in docs)
#   CARDCONNECT_TEST_EXPIRY   default 1228
#   CARDCONNECT_TEST_CVV      default 123
#
# Live fts host + real MID can move real money if you use a real PAN.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=/dev/null
source "${SCRIPT_DIR}/_cardconnect_env.sh"

: "${CARDCONNECT_BASE_URL:?Set CARDCONNECT_BASE_URL}"
: "${CARDCONNECT_MERCHID:?Set CARDCONNECT_MERCHID}"
: "${CARDCONNECT_API_USER:?Set CARDCONNECT_API_USER}"
: "${CARDCONNECT_API_PASSWORD:?Set CARDCONNECT_API_PASSWORD}"

BASE="${CARDCONNECT_BASE_URL%/}"
AMOUNT="${CARDCONNECT_TEST_AMOUNT:-1.00}"
POSTAL="${CARDCONNECT_TEST_POSTAL:-80124}"
NAME="${CARDCONNECT_TEST_NAME:-Test User}"
ADDRESS="${CARDCONNECT_TEST_ADDRESS:-123 Test St}"
CITY="${CARDCONNECT_TEST_CITY:-Denver}"
STATE="${CARDCONNECT_TEST_STATE:-CO}"
COUNTRY="${CARDCONNECT_TEST_COUNTRY:-US}"
PAN="${CARDCONNECT_TEST_PAN:-4111111111111111}"
EXPIRY="${CARDCONNECT_TEST_EXPIRY:-1228}"
CVV="${CARDCONNECT_TEST_CVV:-123}"
ORDERID="chb-ecom-$(date +%s)"

echo "=== CardSecure tokenize ==="
TOKEN_JSON=$(curl -sS -X POST "${BASE}/cardsecure/api/v1/ccn/tokenize" \
  -H "Content-Type: application/json" \
  -d "{\"account\":\"${PAN}\",\"expiry\":\"${EXPIRY}\",\"cvv\":\"${CVV}\"}")
echo "$TOKEN_JSON"

TOKEN=$(echo "$TOKEN_JSON" | php -r '$j=json_decode(stream_get_contents(STDIN),true); echo $j["token"]??"";')
if [[ -z "$TOKEN" ]]; then
  echo "No token from CardSecure; aborting."
  exit 1
fi

echo ""
echo "=== Gateway auth (ecomind=E, AVS address + postal=${POSTAL}, orderid=${ORDERID}) ==="
export CC_MERCH="$CARDCONNECT_MERCHID" CC_TOK="$TOKEN" CC_AMT="$AMOUNT" CC_OID="$ORDERID" \
  CC_ZIP="$POSTAL" CC_NAME="$NAME" CC_ADDR="$ADDRESS" CC_CITY="$CITY" CC_STATE="$STATE" CC_COUNTRY="$COUNTRY"
AUTH_BODY=$(php -r 'echo json_encode(array_filter([
  "merchid" => getenv("CC_MERCH"),
  "account" => getenv("CC_TOK"),
  "amount" => getenv("CC_AMT"),
  "currency" => "USD",
  "capture" => "Y",
  "orderid" => getenv("CC_OID"),
  "ecomind" => "E",
  "name" => getenv("CC_NAME"),
  "address" => getenv("CC_ADDR"),
  "city" => getenv("CC_CITY"),
  "state" => getenv("CC_STATE"),
  "country" => getenv("CC_COUNTRY"),
  "postal" => getenv("CC_ZIP"),
], fn ($v) => $v !== false && $v !== ""));')

curl -sS -u "${CARDCONNECT_API_USER}:${CARDCONNECT_API_PASSWORD}" \
  -H "Content-Type: application/json" \
  -X POST "${BASE}/cardconnect/rest/auth" \
  -w "\n\nHTTP_STATUS:%{http_code}\n" \
  -d "$AUTH_BODY"
