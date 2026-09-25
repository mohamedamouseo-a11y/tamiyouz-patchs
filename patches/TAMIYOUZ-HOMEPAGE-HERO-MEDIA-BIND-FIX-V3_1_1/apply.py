#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.1"
PATCH="TAMIYOUZ-HOMEPAGE-HERO-MEDIA-BIND-FIX-V3_1_1"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
tpl=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php"
css=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
patch_dir=Path(__file__).resolve().parent
src_asset=patch_dir/"assets/tamiyouz-hero-v3-1.webp"
dst_dir=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/media"
dst_asset=dst_dir/"tamiyouz-hero-v3-1.webp"

for p in (tpl, css, src_asset):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(tpl, tpl.with_name(tpl.name+f".bak.{stamp}"))
shutil.copy2(css, css.with_name(css.name+f".bak.{stamp}"))
dst_dir.mkdir(parents=True, exist_ok=True)
shutil.copy2(src_asset, dst_asset)

t=tpl.read_text(encoding="utf-8")

# Remove any prior V3.1 hero image to keep the patch idempotent.
t=re.sub(r'\s*<img\b[^>]*class="[^"]*tyz-hero-media__image[^"]*"[^>]*>\s*', '\n', t)

# Bind directly to the canonical Hero media container seen in the rendered page.
pat=r'(<div\b[^>]*class="[^"]*\btyz-hero__media\b[^"]*"[^>]*>)'
img='''\n                <img class="tyz-hero-media__image tyz-hero-media__image--v311" src="<?php echo esc_url(content_url('/mu-plugins/tamiyouz-homepage-v2-1/assets/media/tamiyouz-hero-v3-1.webp')); ?>" alt="منظومة تسويق رقمي وCRM وأتمتة وتحليلات" loading="eager" decoding="async" fetchpriority="high">'''
t,n=re.subn(pat, lambda m: m.group(1)+img, t, count=1)
if n != 1:
    raise SystemExit("ERROR=CANONICAL_HERO_MEDIA_NOT_FOUND")

if PATCH not in t:
    t=t.replace("?>", f"/* {PATCH} */\n?>", 1)

tpl.write_text(t, encoding="utf-8")

c=css.read_text(encoding="utf-8")
marker=f"/* {PATCH} */"
if marker not in c:
    c += f'''

{marker}
.tyz-hero__media{{
  position:relative!important;
  overflow:hidden!important;
  isolation:isolate!important;
  background:#111214!important;
  min-height:520px;
}}
.tyz-hero__media > *:not(.tyz-hero-media__image){{
  display:none!important;
  visibility:hidden!important;
}}
.tyz-hero__media > .tyz-hero-media__image{{
  display:block!important;
  visibility:visible!important;
  position:absolute!important;
  inset:0!important;
  width:100%!important;
  height:100%!important;
  max-width:none!important;
  object-fit:cover!important;
  object-position:center center!important;
  opacity:1!important;
  z-index:20!important;
  border-radius:inherit!important;
}}
html[data-tyz-theme="light"] .tyz-hero__media > .tyz-hero-media__image{{
  filter:none!important;
}}
html[data-tyz-theme="dark"] .tyz-hero__media > .tyz-hero-media__image{{
  filter:brightness(.86) saturate(.94) contrast(1.03)!important;
}}
.tyz-hero__media:before,
.tyz-hero__media:after{{
  z-index:21!important;
  pointer-events:none!important;
}}
@media(max-width:760px){{
  .tyz-hero__media{{min-height:360px;}}
  .tyz-hero__media > .tyz-hero-media__image{{object-position:58% center!important;}}
}}
'''
css.write_text(c, encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("ASSET=wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/media/tamiyouz-hero-v3-1.webp")
print("CANONICAL_HERO_TARGET=.tyz-hero__media")
print("OLD_HERO_MOCKUP=FORCE_HIDDEN")
print("APPLY=PASS")
