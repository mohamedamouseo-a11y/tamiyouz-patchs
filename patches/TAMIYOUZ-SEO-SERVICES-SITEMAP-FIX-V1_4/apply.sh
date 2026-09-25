#!/usr/bin/env bash
set -euo pipefail
TITLE="SEO Services Sitemap Fix"
VERSION="V1.4"
ROOT="${WP_ROOT:-$PWD}"
TARGET="$ROOT/robots.txt"
LINE="Sitemap: https://tamiyouz.com/services-sitemap.xml"
STAMP="$(date +%Y%m%d-%H%M%S)"

[[ -f "$ROOT/wp-config.php" ]] || { echo "ERROR=SET_WP_ROOT"; exit 1; }

if [[ -f "$TARGET" ]]; then
  cp -a "$TARGET" "$TARGET.bak.$STAMP"
else
  : > "$TARGET"
fi

if ! grep -Fqx "$LINE" "$TARGET"; then
  if [[ -s "$TARGET" ]] && [[ "$(tail -c1 "$TARGET" | wc -l)" -eq 0 ]]; then
    printf '\n' >> "$TARGET"
  fi
  printf '%s\n' "$LINE" >> "$TARGET"
fi

echo "TITLE=$TITLE"
echo "VERSION=$VERSION"
echo "ROBOTS=UPDATED"
echo "ERROR=NONE"
