# Tamiyouz Patches

Safe, reviewable patches for the Tamiyouz WordPress website.

## Homepage V1

`TAMIYOUZ-HOMEPAGE-THEGEM-AI-LUXURY-V1.sh` installs an original premium RTL light-mode homepage inspired by high-end editorial WordPress showcase patterns, with an AI/software visual direction and optional hero video.

The patch is isolated as a WordPress MU plugin and does **not** modify the active theme or database schema.

### Preview-first workflow

```bash
bash TAMIYOUZ-HOMEPAGE-THEGEM-AI-LUXURY-V1.sh
```

After apply, admins can preview the new homepage with:

`/?tamiyouz_preview=1`

To publish it:

```bash
wp option update tamiyouz_homepage_v1_enabled 1
```

To configure the hero video URL:

```bash
wp option update tamiyouz_homepage_v1_hero_video 'https://example.com/path/hero.mp4'
```

To disable the new homepage without removing files:

```bash
wp option update tamiyouz_homepage_v1_enabled 0
```

Rollback files:

```bash
bash TAMIYOUZ-HOMEPAGE-THEGEM-AI-LUXURY-V1.sh --rollback
```
