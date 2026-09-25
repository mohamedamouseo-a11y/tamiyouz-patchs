# TAMIYOUZ-HOMEPAGE-HERO-STRUCTURAL-ARTIFACT-FIX-V3_1_4

TITLE=Homepage Reference Lock
VERSION=V3.1.4
SCOPE=Hero Structural Artifact Removal

Rendered screenshot finding:
- V3.1.3 did NOT remove the detached gray rectangle.
- The artifact remains below the approved Hero image.
- This proves the artifact is a real leftover structural node, not a pseudo-element/comment false positive.

This patch is CSS-only and Hero-only.
It hard-locks the Hero grid to exactly two approved direct children:
1. .tyz-hero__copy
2. .tyz-hero__visual

It also hides any standalone legacy .tyz-hero-media/media-slot node that is not the canonical .tyz-hero__media container.

Preserves:
- approved V3.1 image
- V3.1.2 visual-left / copy-right composition
- Hero copy / CTAs / meta points
- Light/Dark
- theme toggle
- mobile
- capability strip
- all later homepage sections

No DB changes.
No canonical commit/push.
