#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.4"
PATCH="TAMIYOUZ-HOMEPAGE-HERO-STRUCTURAL-ARTIFACT-FIX-V3_1_4"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
patch_css=Path(__file__).resolve().parent/"hero-structural-artifact-fix.css"

for p in (css_path, patch_css):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(css_path, css_path.with_name(css_path.name+f".bak.{stamp}"))

current=css_path.read_text(encoding="utf-8")
block=patch_css.read_text(encoding="utf-8")
if PATCH not in current:
    css_path.write_text(current.rstrip()+"\n\n"+block.strip()+"\n", encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=home.css")
print("HERO_GRID_CHILDREN=LOCKED_TO_COPY_AND_VISUAL")
print("LEGACY_MEDIA_SLOT=FORCE_HIDDEN")
print("HERO_IMAGE=UNCHANGED")
print("HERO_COMPOSITION=UNCHANGED")
print("APPLY=PASS")
