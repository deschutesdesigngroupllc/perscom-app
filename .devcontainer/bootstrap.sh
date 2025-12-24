#!/usr/bin/env bash
set -e

################################################################################
# Bootstrap Laravel app inside GitHub Codespaces
################################################################################

APP_ROOT="/var/www/html"
PORT=8080

echo "▶ Bootstrapping development container…"
cd "$APP_ROOT"

export DEVCONTAINER_SETUP=1

################################################################################
# Ensure .env exists
################################################################################

[[ -f .env ]] || cp .env.example .env

################################################################################
# Configure APP_URL when running inside GitHub Codespaces
################################################################################

if [[ -n "${CODESPACE_NAME:-}" && -n "${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN:-}" ]]; then
  echo "▶ Codespaces environment detected"

  APP_URL="https://${CODESPACE_NAME}-${PORT}.${GITHUB_CODESPACES_PORT_FORWARDING_DOMAIN}"
  export APP_URL

  echo "  → APP_URL set to: ${APP_URL}"

  if grep -Eq '^APP_URL=' .env; then
    echo "  → Updating APP_URL in .env..."
    tmpfile=$(mktemp)
    sed "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env > "$tmpfile"
    mv "$tmpfile" .env
  else
    echo "  → Appending APP_URL to .env..."
    echo "APP_URL=${APP_URL}" >> .env
  fi

  echo "  → APP_URL successfully written to .env"
else
  echo "▶ Not running in Codespaces. Skipping APP_URL setup..."
fi

################################################################################
# Run project setup
################################################################################

echo "▶ Running project setup (composer run setup)…"
composer run setup

echo "✔ Bootstrap complete"
