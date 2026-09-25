# TAMIYOUZ-HOMEPAGE-HERO-MEDIA-BIND-V3_1

TITLE=Homepage Reference Lock
VERSION=V3.1
SCOPE=Hero Media Bind

Purpose:
Bind the approved marketing/CRM/automation hero asset to the existing V3.0 hero media slot only.

This patch:
- adds tamiyouz-hero-v3-1.webp
- patches current live template in-place instead of replacing the whole V3.0 template
- targets .tyz-hero-media if present
- falls back to adding tyz-hero-media to .tyz-hero__media if needed
- injects a real <img> asset into the hero slot
- hides the old CSS mockup internals inside the hero media slot
- preserves hero copy, CTAs, value points, RTL, Light/Dark, mobile, loader and theme toggle

Do not modify About / Services / Showreel / AI / Work / Process / CTA / Footer.
No DB changes.
No canonical commit/push.

Asset:
assets/tamiyouz-hero-v3-1.webp
