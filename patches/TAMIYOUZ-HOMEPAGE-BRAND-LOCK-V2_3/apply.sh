#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-HOMEPAGE-BRAND-LOCK-V2_3"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
DIR="$MU/tamiyouz-homepage-v2-1"
STAMP="$(date +%Y%m%d-%H%M%S)"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
for f in "$MU/tamiyouz-home-v2-1-preview.php" "$DIR/template.php" "$DIR/assets/home.css"; do
  [[ -f "$f" ]] && cp -a "$f" "$f.bak.$STAMP"
done
cp "$BASE/src/tamiyouz-home-v2-1-preview.php" "$MU/tamiyouz-home-v2-1-preview.php"
cp "$BASE/src/template.php" "$DIR/template.php"
cp "$BASE/src/home.css" "$DIR/assets/home.css"
cp "$BASE/src/tamiyouz-logo.svg" "$DIR/assets/tamiyouz-logo.svg"
php -l "$MU/tamiyouz-home-v2-1-preview.php" >/dev/null
php -l "$DIR/template.php" >/dev/null
grep -q "$PATCH" "$MU/tamiyouz-home-v2-1-preview.php"
grep -q "$PATCH" "$DIR/template.php"
grep -q "$PATCH" "$DIR/assets/home.css"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
echo "LIVE=YES"
