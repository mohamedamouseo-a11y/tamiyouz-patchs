#!/usr/bin/env bash
set -euo pipefail
TITLE="Homepage Reference Lock"
VERSION="V2.9.7"
PATCH="TAMIYOUZ-HOMEPAGE-REFERENCE-MATCH-V2_9_7"
ROOT="${WP_ROOT:-$PWD}"
BASE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TPL="$ROOT/wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php"
CSS="$ROOT/wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }
cp -a "$TPL" "$TPL.bak.$STAMP"
cp -a "$CSS" "$CSS.bak.$STAMP"

cp "$BASE/src/template.php" "$TPL"
cp "$BASE/src/home.css" "$CSS"

for a in "نبني نموًا رقميًا" "لسنا مجرد وكالة رقمية" "حلول متكاملة" "من الفكرة إلى نظام" "الذكاء الاصطناعي ليس إضافة" "أعمال رقمية" "رحلة واضحة" "لديك فكرة أو تحدٍ"; do
  grep -Fq "$a" "$TPL"
done

grep -q "$PATCH" "$TPL"
grep -q "$PATCH" "$CSS"
grep -q "tamiyouz-template-fail-open-v2-9-7" "$TPL"

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "PATCH=$PATCH"
echo "TEMPLATE_ANCHORS=8/8"
echo "APPLY=PASS"
