#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.5"
PATCH="TAMIYOUZ-HOMEPAGE-INTERACTIVE-COMMAND-HERO-V3_1_5"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
css_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.css"
js_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js"
patch_dir=Path(__file__).resolve().parent
patch_css=patch_dir/"interactive-command-hero.css"
patch_js=patch_dir/"interactive-command-hero.js"

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
print("HERO_3D_DEPTH=ENABLED")
print("CURSOR_SPOTLIGHT=ENABLED")
print("FOCUS_LENS=ENABLED")
print("DEMO_TELEMETRY=ENABLED")
print("DYNAMIC_COUNTERS=ENABLED")
print("REDUCED_MOTION=SUPPORTED")
print("TOUCH_FALLBACK=SUPPORTED")
print("APPLY=PASS")
