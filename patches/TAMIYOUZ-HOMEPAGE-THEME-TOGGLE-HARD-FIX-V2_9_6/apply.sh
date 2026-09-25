#!/usr/bin/env bash
set -euo pipefail
TITLE="Homepage Reference Lock"
VERSION="V2.9.6"
PATCH="TAMIYOUZ-HOMEPAGE-THEME-TOGGLE-HARD-FIX-V2_9_6"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
TARGET="$MU/tamiyouz-home-theme-toggle-hard-fix-v2-9-6.php"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] && cp -a "$TARGET" "$TARGET.bak.$STAMP"

cp "$BASE/src/tamiyouz-home-theme-toggle-hard-fix-v2-9-6.php" "$TARGET"

grep -q "$PATCH" "$TARGET"
grep -q "stopImmediatePropagation" "$TARGET"
grep -q "tamiyouzSetThemeV296" "$TARGET"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
