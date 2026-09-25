#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-HOMEPAGE-REFERENCE-LOCK-V2_2"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="$ROOT/wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
STAMP="$(date +%Y%m%d-%H%M%S)"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] || { echo "ERROR=TARGET_MISSING"; exit 1; }
cp -a "$TARGET" "$TARGET.bak.$STAMP"
cp "$BASE/src/home.css" "$TARGET"
grep -q "$PATCH" "$TARGET"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
echo "LIVE=YES"
