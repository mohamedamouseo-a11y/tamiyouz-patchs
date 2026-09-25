#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.7"
PATCH="TAMIYOUZ-HOMEPAGE-LIVING-CARDS-V3_1_7"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
js_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js"
patch_dir=Path(__file__).resolve().parent
patch_css=patch_dir/"living-cards.css"
patch_js=patch_dir/"living-cards.js"

for p in (css_path,js_path,patch_css,patch_js):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(css_path,css_path.with_name(css_path.name+f".bak.{stamp}"))
shutil.copy2(js_path,js_path.with_name(js_path.name+f".bak.{stamp}"))

css=css_path.read_text(encoding="utf-8")
if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+patch_css.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

js=js_path.read_text(encoding="utf-8")
if PATCH not in js:
    js_path.write_text(js.rstrip()+"\n\n"+patch_js.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=home.css;home.js")
print("NEW_LEADS_CARD=ANIMATED")
print("FOLLOW_UP_CARD=ANIMATED")
print("INSTAGRAM_CARD=ANIMATED")
print("TIKTOK_CARD=ANIMATED")
print("IDLE_STAGGER=ENABLED")
print("LOCAL_POINTER_CARD_RESPONSE=ENABLED")
print("WHOLE_IMAGE_TILT=UNCHANGED_DISABLED")
print("SEATED_PERSON=STATIC")
print("V316_DASHBOARD=PRESERVED")
print("APPLY=PASS")
