# TAMIYOUZ-HOMEPAGE-THEME-RECOVERY-V2_9_5

TITLE=Homepage Reference Lock
VERSION=V2.9.5
SCOPE=Theme + JS Loader Recovery

## Root cause evidence
Earlier live diagnostics confirmed:
- home.js source exists and is valid
- template contains wp_footer()
- rendered HTML did NOT contain tamiyouz-home-v21-js

The Light/Dark toggle listener lives in home.js.
If home.js is not injected, a saved dark theme remains active and the toggle cannot switch back to Light.

## Fix
Restore the known-good V2.8 loader logic that:
- injects home.css in wp_head
- injects home.js in wp_footer
- preserves front-page template routing
- preserves brand asset endpoint
- keeps current homepage template/CSS untouched

## Scope
Replace loader only:
wp-content/mu-plugins/tamiyouz-home-v2-1-preview.php

No template/CSS/JS edits.
No DB changes.
No visual redesign.
No commit/push.

## Acceptance
- rendered HTML contains id="tamiyouz-home-v21-js"
- data-version="2.9.5"
- clicking theme toggle changes html[data-tyz-theme] dark ↔ light
- localStorage tamiyouz-theme updates
- Light mode visibly changes page palette
