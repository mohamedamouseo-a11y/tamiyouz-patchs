# TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_3

## Title
SEO Services Sitemap Fix

## Version
V1.3

## Why this version exists
V1.0–V1.2 attempted to register into WordPress core sitemap registry, but the live hosting stack does not expose the custom provider in `/wp-sitemap.xml`.

V1.3 stops depending on the core sitemap registry and serves a standalone sitemap directly at:

`/services-sitemap.xml`

It also advertises the sitemap through:
- `robots.txt`
- a `rel="sitemap"` link on relevant pages

## Expected
- /services-sitemap.xml = HTTP 200
- XML contains exactly 8 canonical service URLs
- /robots.txt contains the services sitemap URL
- homepage/admin unchanged

## Scope
One MU-plugin file only.
No DB changes.
No hosting-panel changes.
No theme/Elementor changes.
