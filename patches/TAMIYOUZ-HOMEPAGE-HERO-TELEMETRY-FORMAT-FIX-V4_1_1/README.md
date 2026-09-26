# TAMIYOUZ-HOMEPAGE-HERO-TELEMETRY-FORMAT-FIX-V4_1_1

TITLE=Homepage Reference Lock
VERSION=V4.1.1
SCOPE=Hero Telemetry Formatting Fix

Observed in the V4.1 screenshot:
Website Traffic rendered as a long decimal value such as 58,425.099 instead of a clean whole-number count.

Fix:
- round Website Traffic before locale formatting
- keep Sales / Users / Conversion formatting unchanged
- no motion redesign
- no CSS changes
- no template changes
- no photo changes
- no DB changes

No canonical commit/push.
