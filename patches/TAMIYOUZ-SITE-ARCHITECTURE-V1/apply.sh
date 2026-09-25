#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-SITE-ARCHITECTURE-V1"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
DIR="$MU/tamiyouz-site-v1"
STAMP="$(date +%Y%m%d-%H%M%S)"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$MU/tamiyouz-site-v1.php" ]] && cp -a "$MU/tamiyouz-site-v1.php" "$MU/tamiyouz-site-v1.php.bak.$STAMP"
[[ -d "$DIR" ]] && cp -a "$DIR" "$DIR.bak.$STAMP"
mkdir -p "$DIR/templates" "$DIR/assets"
cp "$BASE/src/tamiyouz-site-v1.php" "$MU/tamiyouz-site-v1.php"
cp "$BASE/src/templates/"*.php "$DIR/templates/"
cp "$BASE/src/assets/"* "$DIR/assets/"
php -l "$MU/tamiyouz-site-v1.php" >/dev/null
for f in "$DIR/templates/"*.php; do php -l "$f" >/dev/null; done
grep -q "$PATCH" "$MU/tamiyouz-site-v1.php"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
