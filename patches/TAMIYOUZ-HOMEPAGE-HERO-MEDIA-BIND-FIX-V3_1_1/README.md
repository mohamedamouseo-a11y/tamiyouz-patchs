# TAMIYOUZ-HOMEPAGE-HERO-MEDIA-BIND-FIX-V3_1_1

TITLE=Homepage Reference Lock
VERSION=V3.1.1
SCOPE=Hero Media Bind Fix

Reason:
The V3.1 technical checks passed, but the rendered screenshot still showed the old synthetic Hero dashboard. This fix binds the approved image directly to the canonical .tyz-hero__media container instead of relying on the generic V3.0 slot hook.

Behavior:
- Hero only.
- Uses the approved V3.1 image.
- Injects the image directly into .tyz-hero__media.
- Forces all legacy Hero mockup children hidden.
- Keeps Hero copy, CTAs, meta/value points, RTL, Light/Dark, theme toggle and mobile.
- Does not touch About/Services/Showreel/AI/Work/Process/CTA/Footer.
- No DB changes.
- No canonical commit/push.
