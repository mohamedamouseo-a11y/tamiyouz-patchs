#!/usr/bin/env bash
set -euo pipefail
TITLE="Homepage Reference Lock"
VERSION="V2.9.5"
PATCH="TAMIYOUZ-HOMEPAGE-THEME-RECOVERY-V2_9_5"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="$ROOT/wp-content/mu-plugins/tamiyouz-home-v2-1-preview.php"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] && cp -a "$TARGET" "$TARGET.bak.$STAMP"

cp "$BASE/src/tamiyouz-home-v2-1-preview.php" "$TARGET"

grep -q "$PATCH" "$TARGET"
grep -q 'tamiyouz-home-v21-js' "$TARGET"
grep -q "assets/home.js" "$TARGET"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
