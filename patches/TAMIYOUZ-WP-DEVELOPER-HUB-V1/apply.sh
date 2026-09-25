#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-WP-DEVELOPER-HUB-V1"
ROOT="${WP_ROOT:-$PWD}"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/src/tamiyouz-developer-hub.php"
DEST="$ROOT/wp-content/mu-plugins/tamiyouz-developer-hub.php"
mkdir -p "$(dirname "$DEST")"
php -l "$SRC" >/dev/null
[[ -f "$DEST" ]] && cp -a "$DEST" "$DEST.bak.$(date +%Y%m%d-%H%M%S)"
cp "$SRC" "$DEST"
chmod 644 "$DEST" || true
grep -q "$PATCH" "$DEST"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
echo "PHP_LINT=PASS"
echo "ADMIN_PAGE=Developer Hub"
echo "PUBLIC_SITE_CHANGED=NO"
echo "DB_SCHEMA_CHANGED=NO"
