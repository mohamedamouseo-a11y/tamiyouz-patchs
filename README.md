# Tamiyouz Patches

Safe, reviewable patches for the Tamiyouz WordPress website.

## Current homepage patch

### TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1

Path:

`patches/TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1/`

Approved direction:

- Premium editorial light-first homepage.
- Full Light / Dark mode toggle.
- Cinematic Hero Video support.
- AI + Software + Digital Growth positioning.
- Neutral visual language with no country-specific styling.
- Arabic RTL and responsive behavior.
- Preview-first installation as an isolated MU-plugin.
- No active-theme edits and no database schema changes.

Install:

```bash
cd patches/TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1
WP_ROOT=/path/to/wordpress bash apply.sh
```

Private admin preview:

`/?tamiyouz_preview=1`

Publish only after approval:

```bash
WP_ROOT=/path/to/wordpress bash apply.sh --publish
```

See the patch README for hero-video configuration and rollback details.
