#!/usr/bin/env bash
set -euo pipefail
PATCH="TAMIYOUZ-WP-DEVELOPER-HUB-V1_4-BASELINE-REAL-PUSH-PROGRESS"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
MU="$ROOT/wp-content/mu-plugins"
HUB="$MU/tamiyouz-developer-hub"
STAMP="$(date +%Y%m%d-%H%M%S)"
[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
for f in tamiyouz-developer-hub.php tamiyouz-developer-hub/admin.php tamiyouz-developer-hub/base.php tamiyouz-developer-hub/review.php tamiyouz-developer-hub/progress.php; do
  src="$BASE/src/$f"; [[ -f "$src" ]] || { echo "ERROR=MISSING_$f"; exit 1; }; php -l "$src" >/dev/null
done
mkdir -p "$HUB"
[[ -f "$MU/tamiyouz-developer-hub.php" ]] && cp -a "$MU/tamiyouz-developer-hub.php" "$MU/tamiyouz-developer-hub.php.bak.$STAMP"
[[ -d "$HUB" ]] && cp -a "$HUB" "$HUB.bak.$STAMP"
cp "$BASE/src/tamiyouz-developer-hub.php" "$MU/tamiyouz-developer-hub.php"
cp "$BASE/src/tamiyouz-developer-hub/admin.php" "$HUB/admin.php"
cp "$BASE/src/tamiyouz-developer-hub/base.php" "$HUB/base.php"
cp "$BASE/src/tamiyouz-developer-hub/review.php" "$HUB/review.php"
cp "$BASE/src/tamiyouz-developer-hub/progress.php" "$HUB/progress.php"
grep -q "$PATCH" "$MU/tamiyouz-developer-hub.php"
grep -q "$PATCH" "$HUB/review.php"
grep -q "$PATCH" "$HUB/progress.php"
echo "PATCH=$PATCH"
echo "APPLY=PASS"
