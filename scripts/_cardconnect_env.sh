#!/usr/bin/env bash
# Sourced by cardconnect-*.sh — do not run directly.
# Loads env in order (later files override):
#   1) changehair-api/.env  — canonical place for CARDCONNECT_* (same as PHP booking API)
#   2) scripts/.env.cardconnect — optional; test PAN/CVV/postal overrides only if you prefer not to put them in API .env

_SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
_REPO_ROOT="$(cd "${_SCRIPT_DIR}/.." && pwd)"
_API_ENV="${_REPO_ROOT}/changehair-api/.env"
_SCRIPTS_LOCAL="${_SCRIPT_DIR}/.env.cardconnect"

_chb_source_env() {
  [[ -f "$1" ]] || return 0
  set -a
  # shellcheck source=/dev/null
  source "$1"
  set +a
}

_chb_source_env "${_API_ENV}"
_chb_source_env "${_SCRIPTS_LOCAL}"

unset -f _chb_source_env
unset _SCRIPT_DIR _REPO_ROOT _API_ENV _SCRIPTS_LOCAL
