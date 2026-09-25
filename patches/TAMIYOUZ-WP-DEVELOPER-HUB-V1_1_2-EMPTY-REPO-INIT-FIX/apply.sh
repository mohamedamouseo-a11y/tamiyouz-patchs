#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-WP-DEVELOPER-HUB-V1_1_2-EMPTY-REPO-INIT-FIX"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC="$BASE/src/tamiyouz-developer-hub/base.php"
DEST="$ROOT/wp-content/mu-plugins/tamiyouz-developer-hub/base.php"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
php -l "$SRC"
[[ -f "$DEST" ]] && cp -a "$DEST" "$DEST.bak.$(date +%Y%m%d-%H%M%S)"
cp "$SRC" "$DEST"
grep -q "$PATCH" "$DEST"
grep -q "contents/.gitkeep" "$DEST"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
echo "PHP_LINT=PASS"
echo "EMPTY_REPO_INIT=CONTENTS_API"
echo "SYNC_EXECUTE=DISABLED"
