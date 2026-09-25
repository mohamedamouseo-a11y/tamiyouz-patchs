# TAMIYOUZ-HOMEPAGE-HERO-COMPOSITION-FIX-V3_1_2

TITLE=Homepage Reference Lock
VERSION=V3.1.2
SCOPE=Hero Composition Fix

Rendered screenshot finding:
- Approved V3.1 hero image is now visible.
- Desktop composition is wrong: copy is on the left and media on the right, opposite the approved master.
- Media starts too low, creating a large dead vertical gap.
- A stray gray placeholder/bar is visible above the hero image.

This patch is CSS-only and hero-only:
- restores approved desktop composition: visual LEFT, Arabic copy RIGHT
- top-aligns media and copy in the same visual band
- removes stray visual pseudo/sibling artifacts
- gives the image a stable 3:2 ratio matching the approved asset
- preserves the V3.1.1 image binding
- preserves copy, CTAs, meta/value points, RTL, Light/Dark, theme toggle and mobile
- does not touch any later homepage section
- no DB changes
- no canonical commit/push
