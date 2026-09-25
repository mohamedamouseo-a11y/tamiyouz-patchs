#!/usr/bin/env bash
set -euo pipefail
TITLE="Homepage Reference Lock"
VERSION="V2.9.2"
PATCH="TAMIYOUZ-HOMEPAGE-RENDER-RECOVERY-V2_9_2"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
TARGET="$MU/tamiyouz-home-render-recovery-v2-9-2.php"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] && cp -a "$TARGET" "$TARGET.bak.$STAMP"

cp "$BASE/src/tamiyouz-home-render-recovery-v2-9-2.php" "$TARGET"
php -l "$TARGET" >/dev/null
grep -q "$PATCH" "$TARGET"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
