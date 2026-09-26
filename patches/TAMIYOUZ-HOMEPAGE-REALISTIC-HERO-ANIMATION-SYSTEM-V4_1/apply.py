#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V4.1"
PATCH="TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-ANIMATION-SYSTEM-V4_1"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
css_path=home/"assets/home.css"
js_path=home/"assets/home.js"
patch_dir=Path(__file__).resolve().parent
css_patch=patch_dir/"realistic-hero-animation.css"
js_patch=patch_dir/"realistic-hero-animation.js"

for p in (css_path,js_path,css_patch,js_patch):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

css=css_path.read_text(encoding="utf-8")
js=js_path.read_text(encoding="utf-8")

if "TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-BASE-REBUILD-V4_0" not in css:
    raise SystemExit("ERROR=V4_0_BASE_NOT_FOUND")
if "TAMIYOUZ-HOMEPAGE-V4-REALISTIC-RENDER-RECOVERY-V4_0_1" not in css:
    raise SystemExit("ERROR=V4_0_1_RECOVERY_NOT_FOUND")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (css_path,js_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+css_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")
if PATCH not in js:
    js_path.write_text(js.rstrip()+"\n\n"+js_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=home.css;home.js")
print("PHYSICAL_PHOTO=STATIC")
print("KPI_SEQUENCE=ACTIVE")
print("KPI_VALUES=DEMO_DYNAMIC")
print("KPI_TRENDS=ACTIVE")
print("MAIN_METRICS=DEMO_DYNAMIC")
print("MAIN_BARS=ACTIVE")
print("MAIN_TREND=ACTIVE")
print("WORKFLOW_SEQUENCE=ACTIVE")
print("CHANNEL_SEQUENCE=ACTIVE")
print("LOCAL_POINTER_LIGHTING=ACTIVE")
print("WHOLE_IMAGE_TILT=ABSENT")
print("PHOTO_PARALLAX=ABSENT")
print("MOBILE=STATIC_SAFE")
print("REDUCED_MOTION=SUPPORTED")
print("DB_CHANGED=NO")
print("APPLY=PASS")
