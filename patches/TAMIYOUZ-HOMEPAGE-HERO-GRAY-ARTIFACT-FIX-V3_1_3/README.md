# TAMIYOUZ-HOMEPAGE-HERO-GRAY-ARTIFACT-FIX-V3_1_3

TITLE=Homepage Reference Lock
VERSION=V3.1.3
SCOPE=Hero Gray Artifact Removal

Rendered screenshot finding:
- V3.1.2 Hero image/copy composition is correct.
- One detached gray horizontal rectangle remains below the Hero image.
- It is not part of the approved Hero design.

This patch is CSS-only and Hero-only.
It removes stale V3.0 media-slot placeholder/fallback artifacts and resets Hero pseudo-elements that can render detached blocks.

Preserves:
- V3.1.1 approved Hero image
- V3.1.2 desktop composition
- Hero copy / CTAs / value points
- RTL
- Light/Dark
- theme toggle
- mobile
- all later sections

No DB changes.
No canonical commit/push.
