# TAMIYOUZ-HOMEPAGE-ABOUT-ASSET-BIND-V3_2

TITLE=Homepage Reference Lock
VERSION=V3.2
SCOPE=About Asset Bind

Purpose:
Replace the synthetic About visual only with the approved premium About artwork while preserving the current About copy and every other homepage section.

Production asset:
- assets/tamiyouz-about-v3-2.webp
- 1024x768 (4:3)
- Derived from the approved V3.2 About concept by using its visual half only.
- Presentation-only numeric metrics from the full concept are intentionally excluded from the production asset.

Live targets:
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css
- wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/tamiyouz-about-v3-2.webp

Safety:
- ABOUT ONLY.
- Preserve Hero V3.1.x exactly.
- Preserve Services, Showreel, AI, Work, Process, CTA, Footer.
- Preserve RTL, Light/Dark, mobile, theme toggle and anchors.
- No DB changes.
- No live commit/push.
- Technical verification is not Visual Pass; rendered screenshots are required.
