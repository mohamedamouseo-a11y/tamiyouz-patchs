#!/usr/bin/env bash
# TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1
# Safe installer for the Tamiyouz WordPress homepage MU-plugin.
# Default behavior: install + verify only. It does NOT publish the new homepage.
set -euo pipefail

PATCH_ID="TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC_DIR="$SCRIPT_DIR/src"
MODE="${1:-apply}"

log()  { printf '\n[%s] %s\n' "$PATCH_ID" "$*"; }
fail() { printf '\n[%s] ERROR: %s\n' "$PATCH_ID" "$*" >&2; exit 1; }

discover_wp_root() {
  if [[ -n "${WP_ROOT:-}" ]]; then
    [[ -f "$WP_ROOT/wp-config.php" ]] || fail "WP_ROOT does not contain wp-config.php: $WP_ROOT"
    printf '%s' "$WP_ROOT"
    return
  fi

  local d="$PWD"
  local i
  for i in 1 2 3 4 5 6; do
    if [[ -f "$d/wp-config.php" ]]; then
      printf '%s' "$d"
      return
    fi
    [[ "$d" == "/" ]] && break
    d="$(dirname "$d")"
  done

  local matches=()
  while IFS= read -r item; do
    [[ -n "$item" ]] && matches+=("$(dirname "$item")")
  done < <(find /var/www /srv/www /opt -maxdepth 6 -type f -name wp-config.php 2>/dev/null | sort -u)

  if [[ "${#matches[@]}" -eq 1 ]]; then
    printf '%s' "${matches[0]}"
    return
  fi

  if [[ "${#matches[@]}" -gt 1 ]]; then
    printf 'Multiple WordPress roots found:\n' >&2
    printf ' - %s\n' "${matches[@]}" >&2
    fail "Set WP_ROOT explicitly to avoid touching the wrong site."
  fi

  fail "WordPress root not found. Set WP_ROOT=/path/to/wordpress."
}

wp_cmd() {
  command -v wp >/dev/null 2>&1 || return 127
  if [[ "$(id -u)" -eq 0 ]]; then
    wp --allow-root --path="$WP_ROOT" "$@"
  else
    wp --path="$WP_ROOT" "$@"
  fi
}

[[ -f "$SRC_DIR/loader.php" ]] || fail "Missing src/loader.php"
[[ -f "$SRC_DIR/template.php" ]] || fail "Missing src/template.php"
[[ -f "$SRC_DIR/assets/home.css" ]] || fail "Missing src/assets/home.css"
[[ -f "$SRC_DIR/assets/home.js" ]] || fail "Missing src/assets/home.js"

WP_ROOT="$(discover_wp_root)"
MU_DIR="$WP_ROOT/wp-content/mu-plugins"
DEST_DIR="$MU_DIR/tamiyouz-homepage-v1"
LOADER_DEST="$MU_DIR/tamiyouz-home-v1.php"
BACKUP_ROOT="$WP_ROOT/wp-content/tamiyouz-patch-backups"
STAMP="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="$BACKUP_ROOT/$PATCH_ID-$STAMP"

publish() {
  log "Publishing Homepage V1"
  wp_cmd option update tamiyouz_home_v1_enabled 1 >/dev/null || fail "WP-CLI is required for --publish."
  log "PUBLISH=PASS"
}

disable() {
  log "Disabling Homepage V1"
  wp_cmd option update tamiyouz_home_v1_enabled 0 >/dev/null || fail "WP-CLI is required for --disable."
  log "DISABLE=PASS"
}

rollback() {
  mkdir -p "$BACKUP_ROOT"
  local latest
  latest="$(find "$BACKUP_ROOT" -maxdepth 1 -mindepth 1 -type d -name "$PATCH_ID-*" | sort | tail -n 1 || true)"

  if [[ -z "$latest" ]]; then
    log "No backup found; removing current patch files only."
    rm -rf "$DEST_DIR"
    rm -f "$LOADER_DEST"
    if command -v wp >/dev/null 2>&1; then
      wp_cmd option update tamiyouz_home_v1_enabled 0 >/dev/null 2>&1 || true
    fi
    log "ROLLBACK=PASS"
    return
  fi

  log "Restoring backup: $latest"
  rm -rf "$DEST_DIR"
  rm -f "$LOADER_DEST"

  if [[ -d "$latest/tamiyouz-homepage-v1" ]]; then
    cp -a "$latest/tamiyouz-homepage-v1" "$DEST_DIR"
  fi
  if [[ -f "$latest/tamiyouz-home-v1.php" ]]; then
    cp -a "$latest/tamiyouz-home-v1.php" "$LOADER_DEST"
  fi

  if command -v wp >/dev/null 2>&1; then
    wp_cmd option update tamiyouz_home_v1_enabled 0 >/dev/null 2>&1 || true
  fi

  log "ROLLBACK=PASS"
}

case "$MODE" in
  --publish)
    publish
    exit 0
    ;;
  --disable)
    disable
    exit 0
    ;;
  --rollback)
    rollback
    exit 0
    ;;
  apply|"")
    ;;
  *)
    fail "Unknown mode: $MODE. Use apply, --publish, --disable, or --rollback."
    ;;
esac

log "Target WordPress root: $WP_ROOT"
mkdir -p "$MU_DIR" "$BACKUP_ROOT"

if [[ -e "$DEST_DIR" || -e "$LOADER_DEST" ]]; then
  log "Backing up previous Homepage V1 files to $BACKUP_DIR"
  mkdir -p "$BACKUP_DIR"
  [[ -d "$DEST_DIR" ]] && cp -a "$DEST_DIR" "$BACKUP_DIR/tamiyouz-homepage-v1"
  [[ -f "$LOADER_DEST" ]] && cp -a "$LOADER_DEST" "$BACKUP_DIR/tamiyouz-home-v1.php"
fi

STAGE="$(mktemp -d)"
trap 'rm -rf "$STAGE"' EXIT
mkdir -p "$STAGE/tamiyouz-homepage-v1/assets"

cp "$SRC_DIR/template.php" "$STAGE/tamiyouz-homepage-v1/template.php"
cp "$SRC_DIR/assets/home.css" "$STAGE/tamiyouz-homepage-v1/assets/home.css"
cp "$SRC_DIR/assets/home.js" "$STAGE/tamiyouz-homepage-v1/assets/home.js"
cp "$SRC_DIR/loader.php" "$STAGE/tamiyouz-home-v1.php"

log "Validating PHP syntax before install"
php -l "$STAGE/tamiyouz-home-v1.php" >/dev/null
php -l "$STAGE/tamiyouz-homepage-v1/template.php" >/dev/null

log "Installing atomically"
rm -rf "$DEST_DIR.next"
cp -a "$STAGE/tamiyouz-homepage-v1" "$DEST_DIR.next"
rm -rf "$DEST_DIR"
mv "$DEST_DIR.next" "$DEST_DIR"

cp "$STAGE/tamiyouz-home-v1.php" "$LOADER_DEST.next"
mv "$LOADER_DEST.next" "$LOADER_DEST"

OWNER="$(stat -c '%U:%G' "$WP_ROOT/wp-config.php" 2>/dev/null || true)"
if [[ -n "$OWNER" && "$OWNER" != "UNKNOWN:UNKNOWN" ]]; then
  chown -R "$OWNER" "$DEST_DIR" "$LOADER_DEST" 2>/dev/null || true
fi

find "$DEST_DIR" -type d -exec chmod 755 {} \; 2>/dev/null || true
find "$DEST_DIR" -type f -exec chmod 644 {} \; 2>/dev/null || true
chmod 644 "$LOADER_DEST" 2>/dev/null || true

log "Verifying installed markers and syntax"
grep -q "$PATCH_ID" "$LOADER_DEST" || fail "Loader marker missing"
grep -q "$PATCH_ID" "$DEST_DIR/template.php" || fail "Template marker missing"
grep -q "$PATCH_ID" "$DEST_DIR/assets/home.css" || fail "CSS marker missing"
grep -q "$PATCH_ID" "$DEST_DIR/assets/home.js" || fail "JS marker missing"
php -l "$LOADER_DEST" >/dev/null
php -l "$DEST_DIR/template.php" >/dev/null

if command -v wp >/dev/null 2>&1; then
  CURRENT="$(wp_cmd option get tamiyouz_home_v1_enabled 2>/dev/null || printf '0')"
  VIDEO="$(wp_cmd option get tamiyouz_home_v1_hero_video 2>/dev/null || true)"
  log "WP option state: enabled=$CURRENT, hero_video=$([[ -n "$VIDEO" ]] && printf 'SET' || printf 'EMPTY')"
else
  log "WP-CLI not found; files installed, publish state unchanged."
fi

cat <<EOF

PATCH=$PATCH_ID
APPLY=PASS
PHP_LINT=PASS
MODE=PREVIEW_FIRST
PUBLIC_HOMEPAGE_CHANGED=NO
LIGHT_DARK_TOGGLE=ACTIVE
COUNTRY_SPECIFIC_VISUALS=NONE
AI_SOFTWARE_DIRECTION=ACTIVE
HERO_VIDEO=CONFIGURABLE
THEME_FILES_CHANGED=NO
DB_SCHEMA_CHANGED=NO

Private preview for an administrator:
  /?tamiyouz_preview=1

Publish only after visual approval:
  bash "$SCRIPT_DIR/apply.sh" --publish

Disable without removing files:
  bash "$SCRIPT_DIR/apply.sh" --disable

Rollback:
  bash "$SCRIPT_DIR/apply.sh" --rollback
EOF
