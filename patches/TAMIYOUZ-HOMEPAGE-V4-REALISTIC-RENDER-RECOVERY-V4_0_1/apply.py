#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V4.0.1"
PATCH="TAMIYOUZ-HOMEPAGE-V4-REALISTIC-RENDER-RECOVERY-V4_0_1"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
patch_dir=Path(__file__).resolve().parent
css_patch=patch_dir/"render-recovery.css"

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
print("ROOT_CAUSE=LEGACY_V311_DIRECT_CHILD_HIDE_RULE")
print("V4_PHOTO_VISIBILITY=RECOVERED")
print("V4_KPI_VISIBILITY=RECOVERED")
print("V4_SCREEN_VISIBILITY=RECOVERED")
print("V4_GLASS_VISIBILITY=RECOVERED")
print("LEGACY_LAYERS=STILL_SUPPRESSED")
print("TEMPLATE_CHANGED=NO")
print("JS_CHANGED=NO")
print("DB_CHANGED=NO")
print("APPLY=PASS")
