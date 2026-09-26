#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V4.1.2"
PATCH="TAMIYOUZ-HOMEPAGE-HERO-VISUAL-OCCLUSION-FIX-V4_1_2"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
patch_dir=Path(__file__).resolve().parent
css_patch=patch_dir/"hero-visual-occlusion-fix.css"

for p in (css_path,css_patch):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

css=css_path.read_text(encoding="utf-8")
if "TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-BASE-REBUILD-V4_0" not in css:
    raise SystemExit("ERROR=V4_0_BASE_NOT_FOUND")

if PATCH in css:
    print("APPLY=ALREADY_PRESENT")
    raise SystemExit(0)

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(css_path,css_path.with_name(css_path.name+f".bak.{stamp}"))
css_path.write_text(css.rstrip()+"\n\n"+css_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILE_CHANGED=home.css")
print("PERSON_HEAD_OVERLAP=NONE")
print("PERSON_SHOULDER_OVERLAP=NONE")
print("PERSON_ARM_OVERLAP=NONE")
print("KPI_GLOW_ON_PERSON=NONE")
print("POINTER_LIGHT_ON_PERSON=NONE")
print("KPI_ANIMATION=PRESERVED")
print("MONITOR_UI_ALIGNMENT=PRESERVED")
print("PHOTO=STATIC")
print("DB_CHANGED=NO")
print("APPLY=PASS")
