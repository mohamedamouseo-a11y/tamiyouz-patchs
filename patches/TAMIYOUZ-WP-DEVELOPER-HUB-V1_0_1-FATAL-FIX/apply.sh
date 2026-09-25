#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-WP-DEVELOPER-HUB-V1_0_1-FATAL-FIX"
ROOT="${WP_ROOT:-$PWD}"
SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/src/tamiyouz-developer-hub.php"
DEST="$ROOT/wp-content/mu-plugins/tamiyouz-developer-hub.php"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
php -l "$SRC"
[[ -f "$DEST" ]] && cp -a "$DEST" "$DEST.bak.$(date +%Y%m%d-%H%M%S)"
cp "$SRC" "$DEST"
grep -q "Hotfix: V1.0.1" "$DEST"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
echo "PHP_LINT=PASS"
