#!/usr/bin/env bash
# Read-only CardPointe Gateway calls (no new charges):
#   1) CardSecure tokenize (fresh token for bin/surcharge)
#   2) GET /cardconnect/rest/bin/{merchid}/{token}
#   3) GET /cardconnect/rest/surcharge?merchid&token&postal&amount
#   4) GET /cardconnect/rest/inquire/{retref}/{merchid}  (optional: CARDCONNECT_TEST_RETREF)
#   5) GET /cardconnect/rest/inquireByOrderid/{orderid}/{merchid}/1  (optional: CARDCONNECT_TEST_ORDERID)
#   6) GET /cardconnect/rest/settlestat  (merchid + date MMDD, or CARDCONNECT_SETTLESTAT_BATCHID)
#   7) GET /cardconnect/rest/funding  (query merchid + date; optional page/limit)
#
# Loads api/.env via scripts/_cardconnect_env.sh (optional scripts/.env.cardconnect overlay).
#
# Optional env:
#   CARDCONNECT_TEST_RETREF, CARDCONNECT_TEST_ORDERID
#   CARDCONNECT_SETTLESTAT_BATCHID   if set, used instead of date for settlestat
#   CARDCONNECT_SETTLESTAT_DATE      MMDD (default: today local)
#   CARDCONNECT_FUNDING_DATE         MMDD or YYYYMMDD (default: today MMDD)
#   CARDCONNECT_FUNDING_PAGE, CARDCONNECT_FUNDING_LIMIT
#   CARDCONNECT_TEST_POSTAL, CARDCONNECT_TEST_AMOUNT, CARDCONNECT_TEST_PAN, etc.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# shellcheck source=/dev/null
source "${SCRIPT_DIR}/_cardconnect_env.sh"

: "${CARDCONNECT_BASE_URL:?Set CARDCONNECT_BASE_URL}"
: "${CARDCONNECT_MERCHID:?Set CARDCONNECT_MERCHID}"
: "${CARDCONNECT_API_USER:?Set CARDCONNECT_API_USER}"
: "${CARDCONNECT_API_PASSWORD:?Set CARDCONNECT_API_PASSWORD}"

BASE="${CARDCONNECT_BASE_URL%/}"
POSTAL="${CARDCONNECT_TEST_POSTAL:-80124}"
AMOUNT="${CARDCONNECT_TEST_AMOUNT:-1.00}"
PAN="${CARDCONNECT_TEST_PAN:-4111111111111111}"
EXPIRY="${CARDCONNECT_TEST_EXPIRY:-1228}"
CVV="${CARDCONNECT_TEST_CVV:-123}"

AUTH=( -sS -u "${CARDCONNECT_API_USER}:${CARDCONNECT_API_PASSWORD}" -H "Content-Type: application/json" )

echo "=== CardSecure tokenize (for bin + surcharge) ==="
TOKEN_JSON=$(curl -sS -X POST "${BASE}/cardsecure/api/v1/ccn/tokenize" \
  -H "Content-Type: application/json" \
  -d "{\"account\":\"${PAN}\",\"expiry\":\"${EXPIRY}\",\"cvv\":\"${CVV}\"}")
echo "$TOKEN_JSON"
TOKEN=$(echo "$TOKEN_JSON" | php -r '$j=json_decode(stream_get_contents(STDIN),true); echo $j["token"]??"";')
if [[ -z "$TOKEN" ]]; then
  echo "No token; cannot run bin/surcharge."
  exit 1
fi

echo ""
echo "=== GET bin / ${CARDCONNECT_MERCHID} / ${TOKEN} ==="
curl "${AUTH[@]}" -w "\nHTTP_STATUS:%{http_code}\n" \
  "${BASE}/cardconnect/rest/bin/${CARDCONNECT_MERCHID}/${TOKEN}"

echo ""
echo ""
echo "=== GET surcharge (merchid, token, postal, amount) ==="
curl "${AUTH[@]}" -G -w "\nHTTP_STATUS:%{http_code}\n" \
  "${BASE}/cardconnect/rest/surcharge" \
  --data-urlencode "merchid=${CARDCONNECT_MERCHID}" \
  --data-urlencode "token=${TOKEN}" \
  --data-urlencode "postal=${POSTAL}" \
  --data-urlencode "amount=${AMOUNT}"

RETREF="${CARDCONNECT_TEST_RETREF:-}"
if [[ -n "$RETREF" ]]; then
  echo ""
  echo ""
  echo "=== GET inquire / ${RETREF} / ${CARDCONNECT_MERCHID} ==="
  curl "${AUTH[@]}" -w "\nHTTP_STATUS:%{http_code}\n" \
    "${BASE}/cardconnect/rest/inquire/${RETREF}/${CARDCONNECT_MERCHID}"
else
  echo ""
  echo ""
  echo "=== GET inquire (skipped) ==="
  echo "Set CARDCONNECT_TEST_RETREF in api/.env or scripts/.env.cardconnect (e.g. 082930785638)."
fi

ORDERID="${CARDCONNECT_TEST_ORDERID:-}"
if [[ -n "$ORDERID" ]]; then
  echo ""
  echo ""
  echo "=== GET inquireByOrderid / ${ORDERID} / ${CARDCONNECT_MERCHID} / 1 ==="
  curl "${AUTH[@]}" -w "\nHTTP_STATUS:%{http_code}\n" \
    "${BASE}/cardconnect/rest/inquireByOrderid/${ORDERID}/${CARDCONNECT_MERCHID}/1"
else
  echo ""
  echo ""
  echo "=== GET inquireByOrderid (skipped) ==="
  echo "Set CARDCONNECT_TEST_ORDERID in api/.env or scripts/.env.cardconnect (e.g. chb-ecom-...) to try order lookup."
fi

# settlestat: query merchid + (batchid OR date MMDD)
SETTLE_BATCH="${CARDCONNECT_SETTLESTAT_BATCHID:-}"
SETTLE_DATE="${CARDCONNECT_SETTLESTAT_DATE:-$(date +%m%d)}"
echo ""
echo ""
if [[ -n "$SETTLE_BATCH" ]]; then
  echo "=== GET settlestat (merchid + batchid=${SETTLE_BATCH}) ==="
  curl "${AUTH[@]}" -G -w "\nHTTP_STATUS:%{http_code}\n" \
    "${BASE}/cardconnect/rest/settlestat" \
    --data-urlencode "merchid=${CARDCONNECT_MERCHID}" \
    --data-urlencode "batchid=${SETTLE_BATCH}"
else
  echo "=== GET settlestat (merchid + date=${SETTLE_DATE} MMDD) ==="
  curl "${AUTH[@]}" -G -w "\nHTTP_STATUS:%{http_code}\n" \
    "${BASE}/cardconnect/rest/settlestat" \
    --data-urlencode "merchid=${CARDCONNECT_MERCHID}" \
    --data-urlencode "date=${SETTLE_DATE}"
fi

# funding: query merchid + date (MMDD or YYYYMMDD); optional page/limit. (Header merchid per OpenAPI returned 401 on fts; query matches portal docs.)
FUND_DATE="${CARDCONNECT_FUNDING_DATE:-$(date +%m%d)}"
FUND_PAGE="${CARDCONNECT_FUNDING_PAGE:-}"
FUND_LIMIT="${CARDCONNECT_FUNDING_LIMIT:-}"
echo ""
echo ""
echo "=== GET funding (merchid + date=${FUND_DATE}) ==="
FUND_ARGS=( "${AUTH[@]}" -G -w "\nHTTP_STATUS:%{http_code}\n"
  "${BASE}/cardconnect/rest/funding"
  --data-urlencode "merchid=${CARDCONNECT_MERCHID}"
  --data-urlencode "date=${FUND_DATE}" )
[[ -n "$FUND_PAGE" ]] && FUND_ARGS+=( --data-urlencode "page=${FUND_PAGE}" )
[[ -n "$FUND_LIMIT" ]] && FUND_ARGS+=( --data-urlencode "limit=${FUND_LIMIT}" )
curl "${FUND_ARGS[@]}"

echo ""
