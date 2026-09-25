# TAMIYOUZ-HOMEPAGE-NATIVE-CARD-LIFT-V3_1_8

TITLE=Homepage Reference Lock
VERSION=V3.1.8
SCOPE=Native Hero Card Lift + Live Readouts

Why:
The V3.1.7 source markers were present, but the screen recording showed the intended card motion was not visually convincing:
- V3.1.6 dashboard movement is visible and working.
- NEW LEADS / FOLLOW UP still look effectively static.
- TikTok / Instagram cards do not visibly lift/wake enough on pointer proximity.

Fix:
V3.1.8 stops drawing replacement cards.
Instead, it clones the exact card pixels from the approved Hero image into four clipped overlays so the original-looking cards themselves can lift, glow, pulse and react locally.

Adds:
- native-looking cloned NEW LEADS card crop
- native-looking cloned FOLLOW UP card crop
- native-looking cloned TikTok card crop
- native-looking cloned Instagram card crop
- staggered idle wake
- nearest-card-only hover lift
- local sheen/pulse
- small dynamic DEMO number patch for Leads / Follow Up
- tiny rising micro-line under the metric area

Preserves:
- V3.1.6 Live Command Center
- approved Hero image
- seated person static
- whole-image tilt disabled
- Hero copy / CTA / layout
- all later homepage sections
- Light/Dark, RTL, mobile, reduced motion, theme toggle

Truthfulness:
Dynamic values remain decorative DEMO interface telemetry only.

No template.php change.
No DB change.
No live commit/push.
