#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_1"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="$ROOT/wp-content/mu-plugins/tamiyouz-seo-services-sitemap-v1.php"
STAMP="$(date +%Y%m%d-%H%M%S)"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] && cp -a "$TARGET" "$TARGET.bak.$STAMP"
cp "$BASE/src/tamiyouz-seo-services-sitemap-v1.php" "$TARGET"
php -l "$TARGET" >/dev/null
grep -q "$PATCH" "$TARGET"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
