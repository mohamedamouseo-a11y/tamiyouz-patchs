# TAMIYOUZ-HOMEPAGE-NATIVE-HERO-REBUILD-V3_1_11

TITLE=Homepage Reference Lock
VERSION=V3.1.11
SCOPE=Native Hero Rebuild

Problem solved:
Previous V3.1.5 -> V3.1.10 approaches animated layers on top of a flat raster Hero. Even when coordinates were clipped, motion still looked like something floating over the picture.

V3.1.11 rebuilds the Hero visual as real DOM/CSS/SVG components.

Architecture:
- the approved raster is no longer the desktop UI canvas
- the raster is used only as a clipped photographic source for the seated person / office atmosphere
- all UI is native:
  - 5 workflow cards
  - workflow connectors
  - social/source tiles
  - main operating dashboard
  - KPI/demo telemetry
  - animated bars
  - animated trend SVG
  - right status panel
- all motion runs on the real components themselves
- no overlay calibration against baked UI is needed

Desktop:
- fully native interactive visual
- seated person photo remains static
- cards/panels animate themselves
- mouse proximity affects the actual DOM card/panel

Mobile:
- conservative static fallback using the approved Hero image

Truthfulness:
All dynamic telemetry is clearly decorative DEMO data and is not a client result or Tamiyouz performance claim.

Live targets:
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js

Safety:
- Hero visual only
- preserve Hero copy / CTA / meta / layout position
- preserve capability strip and every later homepage section
- preserve RTL / Light / Dark / theme toggle / anchors
- no DB changes
- no canonical commit/push
- V3.1.5 -> V3.1.10 legacy Hero animation layers are superseded
