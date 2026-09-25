# TAMIYOUZ-SEO-SERVICES-SITEMAP-FIX-V1_4

## Title
SEO Services Sitemap Fix

## Version
V1.4

## Root cause
V1.3 already contains a correct WordPress `robots_txt` filter, but the live `/robots.txt` is being served as a physical/static file before WordPress runs. Therefore the PHP filter cannot modify the response.

## Fix
Safely append exactly this line to the existing root `robots.txt`:

`Sitemap: https://tamiyouz.com/services-sitemap.xml`

Rules:
- backup existing robots.txt first
- preserve every existing line exactly
- append only if the sitemap line is missing
- do not replace or reorder existing directives
- no DB/theme/WordPress changes

## Expected
- /services-sitemap.xml = 200
- COUNT=8
- /robots.txt contains services-sitemap.xml
- homepage/admin unchanged
