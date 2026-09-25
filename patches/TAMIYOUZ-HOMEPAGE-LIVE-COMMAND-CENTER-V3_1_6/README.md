# TAMIYOUZ-HOMEPAGE-LIVE-COMMAND-CENTER-V3_1_6

TITLE=Homepage Reference Lock
VERSION=V3.1.6
SCOPE=Live Command Center

Goal:
Replace the V3.1.5 whole-image tilt / floating telemetry feel with a deeper "system alive inside the image" interaction.

Behavior:
- approved Hero image and seated person remain visually fixed
- no whole-image pointer tilt
- no floating lens or detached telemetry cards
- animated counters appear inside the main dashboard screen
- bars rise/fall and the trend line redraws continuously
- right-side monitor cycles SYNCING / LIVE with activity pulses
- top dashboard tiles breathe subtly during idle
- cinematic scan sweep wakes screen content periodically
- pointer interaction is local: only the nearest screen reacts
- local screen overlay follows pointer by only a few pixels
- idle motion continues while Hero is visible
- reduced-motion and touch-safe fallbacks included

Truthfulness:
All simulated readings are decorative demo telemetry. A tiny DEMO label remains inside the main screen; values must not be presented as Tamiyouz or client performance metrics.

Files:
- live-command-center.css
- live-command-center.js
- apply.py

Live files changed by apply:
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js

Safety:
- HERO INTERACTION ONLY.
- Preserve current Hero image, crop, copy, CTAs, layout and capability strip.
- Preserve About and every later homepage section.
- Preserve Light/Dark, RTL, theme toggle, mobile and anchors.
- No template.php changes.
- No DB changes.
- No live commit/push.
- Technical verification is not Interaction/Visual Pass; manual cursor testing is required.
