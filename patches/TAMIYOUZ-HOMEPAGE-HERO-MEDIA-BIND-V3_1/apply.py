#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1"
PATCH="TAMIYOUZ-HOMEPAGE-HERO-MEDIA-BIND-V3_1"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
tpl=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/template.php"
css=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
patch_dir=Path(__file__).resolve().parent
src_asset=patch_dir/"assets/tamiyouz-hero-v3-1.webp"
dst_dir=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/media"
dst_asset=dst_dir/"tamiyouz-hero-v3-1.webp"

for p in (tpl,css,src_asset):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(tpl, tpl.with_name(tpl.name+f".bak.{stamp}"))
shutil.copy2(css, css.with_name(css.name+f".bak.{stamp}"))
dst_dir.mkdir(parents=True, exist_ok=True)
shutil.copy2(src_asset,dst_asset)

t=tpl.read_text(encoding="utf-8")
if "tyz-hero-media" not in t:
    t=t.replace('class="tyz-hero__media"', 'class="tyz-hero__media tyz-hero-media"', 1)

if 'class="tyz-hero-media__image"' not in t:
    pat=r'(<div\b[^>]*class="[^"]*\btyz-hero-media\b[^"]*"[^>]*>)'
    repl=r'''\1
                <img class="tyz-hero-media__image" src="<?php echo esc_url(content_url('/mu-plugins/tamiyouz-homepage-v2-1/assets/media/tamiyouz-hero-v3-1.webp')); ?>" alt="منظومة تسويق رقمي وCRM وأتمتة وتحليلات" loading="eager" decoding="async" fetchpriority="high">'''
    t,n=re.subn(pat,repl,t,count=1)
    if n!=1:
        raise SystemExit("ERROR=HERO_SLOT_NOT_FOUND")

if PATCH not in t:
    t=t.replace("?>", f"/* {PATCH} */\n?>", 1)

tpl.write_text(t,encoding="utf-8")

c=css.read_text(encoding="utf-8")
marker=f"/* {PATCH} */"
if marker not in c:
    c += f'''

{marker}
.tyz-hero-media{{
  position:relative!important;
  overflow:hidden!important;
  isolation:isolate;
  background:#111214!important;
}}
.tyz-hero-media__image{{
  position:absolute;
  inset:0;
  width:100%;
  height:100%;
  object-fit:cover;
  object-position:center center;
  display:block;
  z-index:1;
}}
.tyz-hero-media > *:not(.tyz-hero-media__image){{
  display:none!important;
}}
.tyz-hero-media:after{{
  content:"";
  position:absolute;
  inset:0;
  z-index:2;
  pointer-events:none;
  background:linear-gradient(180deg,rgba(12,13,15,.02),rgba(12,13,15,.08));
}}
html[data-tyz-theme="dark"] .tyz-hero-media__image{{
  filter:brightness(.78) saturate(.92) contrast(1.02);
}}
@media(max-width:760px){{
  .tyz-hero-media__image{{object-position:58% center}}
}}
'''
css.write_text(c,encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("ASSET=wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/media/tamiyouz-hero-v3-1.webp")
print("HERO_SLOT=BOUND")
print("APPLY=PASS")
