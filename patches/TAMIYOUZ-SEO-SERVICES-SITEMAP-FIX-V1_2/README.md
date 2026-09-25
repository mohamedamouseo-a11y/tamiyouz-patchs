# TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_2

## Title
SEO Services Sitemap Fix

## Version
V1.2

## Root cause addressed
V1.1 relied only on `wp_sitemaps_init`. If the sitemap server had already been initialized before this MU-plugin registered that callback, the hook was already gone for the request and the custom provider never entered the registry.

V1.2 uses the official `wp_register_sitemap_provider()` function directly on `init` priority 0. That function registers into the current sitemap registry whether the server was initialized earlier or is initialized at that moment.

## Expected
- /wp-sitemap.xml contains tyzservices
- /wp-sitemap-tyzservices-1.xml returns 200
- custom sitemap contains exactly 8 service URLs

## Scope
One MU-plugin file only.
No DB/content/theme/Elementor changes.
No cache or hosting-panel changes required.
