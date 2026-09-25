#!/usr/bin/env bash
set -euo pipefail
TITLE="Homepage Reference Lock"
VERSION="V2.9.4"
PATCH="TAMIYOUZ-HOMEPAGE-TEMPLATE-RECOVERY-V2_9_4"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="$ROOT/wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
[[ -f "$TARGET" ]] && cp -a "$TARGET" "$TARGET.bak.$STAMP"

cp "$BASE/src/template.php" "$TARGET"

for a in "نبني نموًا رقميًا" "لسنا مجرد وكالة رقمية" "حلول متكاملة" "من الفكرة إلى نظام" "الذكاء الاصطناعي ليس إضافة" "أعمال رقمية" "رحلة واضحة" "لديك فكرة أو تحدٍ"; do
  grep -Fq "$a" "$TARGET"
done

grep -q "$PATCH" "$TARGET"
grep -q "tamiyouz-template-fail-open-v2-9-4" "$TARGET"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "ANCHORS=8/8"
echo "APPLY=PASS"
