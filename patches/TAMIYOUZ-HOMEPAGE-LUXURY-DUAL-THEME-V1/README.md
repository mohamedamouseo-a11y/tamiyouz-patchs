# TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1

Premium WordPress homepage patch for Tamiyouz.

## Design lock

This patch follows the approved direction:

- Light-first luxury editorial look inspired by high-end creative WordPress showcase layouts.
- Full Light / Dark mode with a persistent user toggle.
- Neutral international visual language: **no Saudi, Egyptian, Gulf, city skyline, national clothing, flags, or country-specific visual cues**.
- Tamiyouz gold identity is the accent; ivory/black are the primary surfaces.
- AI + software are part of the core story, not decorative buzzwords.
- Hero is built for a cinematic video. If no video is configured, a lightweight animated AI network canvas is used.
- Original layout and implementation; not a copy of TheGem or any third-party demo.
- Arabic RTL first, responsive from desktop to mobile.

## Included

- Hero with video support and floating AI / Software / Growth cards.
- Sticky premium navigation.
- Persistent Light / Dark toggle using `localStorage`.
- Neutral capability marquee instead of fake client logos.
- Statement / positioning section.
- Six service areas.
- Cinematic showreel section.
- AI operating-layer console concept.
- Software / AI / Growth work examples without fabricated client claims.
- Four-step delivery process.
- Final CTA and footer.
- Reduced-motion support and responsive layouts.

## Safety model

The patch is installed as an isolated WordPress MU-plugin.

It does **not**:

- modify the active theme;
- modify Elementor/TheGem/page-builder content;
- change the database schema;
- publish itself by default;
- create fake client logos, fake testimonials, or fake commercial metrics.

Default apply is **preview-first**. Only logged-in administrators can open:

`/?tamiyouz_preview=1`

## Install

From a clone of this repository:

```bash
cd patches/TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1
WP_ROOT=/path/to/wordpress bash apply.sh
```

If there is only one WordPress installation under the common server roots, `WP_ROOT` can be omitted and the installer will auto-discover it.

## Hero video

After apply, either use:

**WordPress Admin → Settings → Tamiyouz Homepage V1**

or WP-CLI:

```bash
wp option update tamiyouz_home_v1_hero_video 'https://example.com/wp-content/uploads/tamiyouz-hero.mp4'
```

Recommended video:

- 16:9 or wide cinematic crop.
- MP4 H.264 plus optional WebM.
- Muted visual sequence, no burned-in text.
- Office / screens / UI / abstract AI / software craft.
- No country-specific skyline, uniforms, flags, or identifiable national styling.
- Compress aggressively for web delivery.

## Publish

Only after the preview is approved:

```bash
WP_ROOT=/path/to/wordpress bash apply.sh --publish
```

## Disable

```bash
WP_ROOT=/path/to/wordpress bash apply.sh --disable
```

## Rollback

```bash
WP_ROOT=/path/to/wordpress bash apply.sh --rollback
```

The installer keeps previous patch files under:

`wp-content/tamiyouz-patch-backups/`

## Expected verification summary

```text
APPLY=PASS
PHP_LINT=PASS
MODE=PREVIEW_FIRST
PUBLIC_HOMEPAGE_CHANGED=NO
LIGHT_DARK_TOGGLE=ACTIVE
COUNTRY_SPECIFIC_VISUALS=NONE
AI_SOFTWARE_DIRECTION=ACTIVE
HERO_VIDEO=CONFIGURABLE
THEME_FILES_CHANGED=NO
DB_SCHEMA_CHANGED=NO
```
