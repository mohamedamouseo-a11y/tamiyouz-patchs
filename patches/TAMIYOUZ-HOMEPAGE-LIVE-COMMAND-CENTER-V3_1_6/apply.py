#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.6"
PATCH="TAMIYOUZ-HOMEPAGE-LIVE-COMMAND-CENTER-V3_1_6"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
js_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js"
patch_dir=Path(__file__).resolve().parent
patch_css=patch_dir/"live-command-center.css"
patch_js=patch_dir/"live-command-center.js"

for p in (css_path,js_path,patch_css,patch_js):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(css_path, css_path.with_name(css_path.name+f".bak.{stamp}"))
shutil.copy2(js_path, js_path.with_name(js_path.name+f".bak.{stamp}"))

css=css_path.read_text(encoding="utf-8")
css_block=patch_css.read_text(encoding="utf-8")
if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+css_block.strip()+"\n",encoding="utf-8")

js=js_path.read_text(encoding="utf-8")
js_block=patch_js.read_text(encoding="utf-8")
if PATCH not in js:
    js_path.write_text(js.rstrip()+"\n\n"+js_block.strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=home.css;home.js")
print("WHOLE_IMAGE_TILT=DISABLED_BY_V316_OVERRIDE")
print("V315_FLOATING_FX=REMOVED_AT_RUNTIME")
print("SEATED_PERSON=STATIC")
print("MAIN_SCREEN_COUNTERS=ENABLED")
print("MAIN_SCREEN_BARS=ENABLED")
print("TREND_REDRAW=ENABLED")
print("RIGHT_SCREEN_STATUS=SYNCING_LIVE")
print("LOCAL_POINTER_RESPONSE=ENABLED")
print("IDLE_CINEMATIC_MOTION=ENABLED")
print("REDUCED_MOTION=SUPPORTED")
print("TOUCH_FALLBACK=SUPPORTED")
print("APPLY=PASS")
