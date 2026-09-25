# TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_1

Corrects V1 registration.

Root cause:
- V1 used the wrong/nonexistent registration function name:
  `wp_sitemaps_register_provider()`
- WordPress core uses `wp_register_sitemap_provider()`, and the documented lifecycle hook for additional providers is `wp_sitemaps_init`.

V1.1 registers the provider directly on `wp_sitemaps_init`.

Expected:
- `/wp-sitemap.xml` contains `tyzservices`
- `/wp-sitemap-tyzservices-1.xml` returns 200
- custom sitemap contains exactly 8 canonical service URLs

No DB/content/theme changes.
