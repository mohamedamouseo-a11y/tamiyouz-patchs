#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.15"
PATCH="TAMIYOUZ-HOMEPAGE-CINEMATIC-NATIVE-WORKSTATION-V3_1_15"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
tpl=home/"template.php"
css_path=home/"assets/home.css"
patch_dir=Path(__file__).resolve().parent
svg_path=patch_dir/"operator-v3115.svg"
css_patch=patch_dir/"cinematic-native-workstation.css"

for p in (tpl,css_path,svg_path,css_patch):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (tpl,css_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

template=tpl.read_text(encoding="utf-8")
svg=svg_path.read_text(encoding="utf-8").strip()

pat=re.compile(r'<svg\b[^>]*class="[^"]*\btyz-pw-operator\b[^"]*"[^>]*>.*?</svg>',re.I|re.S)
template,n=pat.subn(svg,template,count=1)
if n!=1:
    raise SystemExit("ERROR=OPERATOR_SVG_NOT_FOUND")

if "<img" in template.lower():
    # This guard is scoped to the Hero visual replacement policy only; there can be logo images elsewhere.
    hero_match=re.search(r'<div\b[^>]*class="[^"]*\btyz-hero__media\b[^"]*"[^>]*>.*?</div>\s*</div>\s*</div>',template,re.I|re.S)
    if hero_match and "<img" in hero_match.group(0).lower():
        raise SystemExit("ERROR=HERO_RASTER_REFERENCE_FORBIDDEN")

if PATCH not in template:
    template=template.replace("?>",f"/* {PATCH} */\n?>",1)
tpl.write_text(template,encoding="utf-8")

css=css_path.read_text(encoding="utf-8")
if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+css_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=template.php;home.css")
print("ARCHITECTURE=V3.1.14_PRESERVED")
print("RASTER_IMAGE=NONE")
print("MAIN_MONITOR=ENLARGED_PREMIUM")
print("AUX_MONITOR=RECESSED")
print("OPERATOR=BACK_FACING_REFINED_SVG")
print("VISIBLE_FACE=NO")
print("DESK=PERSPECTIVE_NATIVE")
print("UI_SCALE=INCREASED")
print("DEPTH_SHADOWS=ENABLED")
print("APPLY=PASS")
