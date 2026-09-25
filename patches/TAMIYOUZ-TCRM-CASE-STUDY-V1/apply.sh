#!/usr/bin/env bash
set -euo pipefail
TITLE="TCRM Case Study"
VERSION="V1"
PATCH="TAMIYOUZ-TCRM-CASE-STUDY-V1"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
SITE="$MU/tamiyouz-site-v1"
HOME="$MU/tamiyouz-homepage-v2-1"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }

for f in "$SITE/assets/site.css" "$HOME/template.php"; do
  [[ -f "$f" ]] && cp -a "$f" "$f.bak.$STAMP"
done
[[ -f "$MU/tamiyouz-tcrm-case-study-v1.php" ]] && cp -a "$MU/tamiyouz-tcrm-case-study-v1.php" "$MU/tamiyouz-tcrm-case-study-v1.php.bak.$STAMP"

cp "$BASE/src/tamiyouz-tcrm-case-study-v1.php" "$MU/tamiyouz-tcrm-case-study-v1.php"
cp "$BASE/src/site.css" "$SITE/assets/site.css"
cp "$BASE/src/home-template.php" "$HOME/template.php"

php -l "$MU/tamiyouz-tcrm-case-study-v1.php" >/dev/null
php -l "$HOME/template.php" >/dev/null
grep -q "$PATCH" "$MU/tamiyouz-tcrm-case-study-v1.php"
grep -q "$PATCH" "$SITE/assets/site.css"
grep -q "$PATCH" "$HOME/template.php"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
