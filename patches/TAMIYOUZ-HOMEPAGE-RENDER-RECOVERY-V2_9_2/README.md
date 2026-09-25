# TAMIYOUZ-HOMEPAGE-RENDER-RECOVERY-V2_9_2

TITLE=Homepage Reference Lock
VERSION=V2.9.2
SCOPE=Render Recovery Only
STATUS=EMERGENCY_FIX

## Why
Rendered V2.9.1 screenshot shows most homepage content missing while structural spacing remains.
This strongly matches the existing reveal CSS fail-state: `.tyz-reveal` defaults to opacity:0 and requires JS/IntersectionObserver to add `.is-visible`.

Visual QA therefore fails before reference comparison can even begin.

## Fix
A tiny front-page-only MU-plugin injects a fail-open override:
- reveal content always visible
- no transform
- no transition
- no DB/theme/template redesign

This is intentionally isolated so V2.9.1 layout work is preserved.

## Root-cause checks after recovery
OpenHands must verify:
1. homepage HTML contains `tamiyouz-home-v21-js`
2. template still calls `wp_footer()`
3. home.js is readable and injected
4. no malformed JS/PHP marker replacement
5. rendered content visibility no longer depends on JS

## Gate
TECHNICAL_PASS != VISUAL_PASS.
After deployment, capture fresh Light + Dark full-page screenshots.
