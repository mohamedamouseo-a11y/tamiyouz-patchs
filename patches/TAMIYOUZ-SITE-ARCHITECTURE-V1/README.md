# TAMIYOUZ-SITE-ARCHITECTURE-V1

SEO-ready architecture foundation for the ongoing Tamiyouz website.

Routes:
- /services/ — Services archive
- /services/<slug>/ — Service page
- /work/ — Work / Case Studies archive
- /work/<slug>/ — Case Study page
- /blog/ — native WordPress posts in a premium blog archive

Admin:
- Services custom post type
- Work / Case Studies custom post type
- native Posts remain the blog CMS

SEO foundation:
- public/indexable CPTs with clean slugs
- REST/editor support
- titles/meta descriptions/canonical/robots on managed routes
- WordPress core sitemap inclusion for service/case-study entries
- extra routes sitemap added to wp-sitemap index
- responsive RTL templates
- no fake content seeded

This is foundation only. Real service pages, case studies (TCRM etc.), screenshots, reviews and articles are added in following phases.
