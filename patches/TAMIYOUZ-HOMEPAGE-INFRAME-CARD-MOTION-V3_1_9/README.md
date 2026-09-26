# TAMIYOUZ-HOMEPAGE-INFRAME-CARD-MOTION-V3_1_9

TITLE=Homepage Reference Lock
VERSION=V3.1.9
SCOPE=In-Frame Hero Card Motion

Reason:
The V3.1.8 screen recording showed the animation layers were not visually registered tightly enough to the cards in the Hero artwork. Parts appeared outside the actual card/icon areas.

V3.1.9 changes strategy completely:
- NO cloned card images
- NO card translation outside the original artwork
- NO overlay UI outside a card boundary
- NO movement across the seated person
- every animation is clipped inside an exact hotspot that matches the existing card/icon already present in the Hero image

Animated zones:
1. top-left stat card (New Leads)
2. top-right stat card (Follow Up)
3. Instagram icon card
4. TikTok icon card

Effects:
- subtle in-card glow / sheen
- clipped DEMO counter tick inside top stat cards
- tiny rising line / bar animation inside top stat cards
- icon pulse inside Instagram/TikTok tiles
- nearest-zone mouse wake
- staggered idle wake

V3.1.6 Live Command Center remains untouched and active.

Truthfulness:
All changing values are decorative DEMO interface telemetry only.

No template.php changes.
No image changes.
No DB changes.
No canonical/live push.
