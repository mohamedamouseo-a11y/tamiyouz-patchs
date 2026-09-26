#!/usr/bin/env python3
from pathlib import Path
import shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V4.1.1"
PATCH="TAMIYOUZ-HOMEPAGE-HERO-TELEMETRY-FORMAT-FIX-V4_1_1"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
js_path=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1/assets/home.js"

if not js_path.exists():
    raise SystemExit(f"ERROR=MISSING:{js_path}")

js=js_path.read_text(encoding="utf-8")
old="format:v=>v.toLocaleString('en-US')"
new="format:v=>Math.round(v).toLocaleString('en-US')"

if new in js:
    print("APPLY=ALREADY_PRESENT")
    raise SystemExit(0)

if old not in js:
    raise SystemExit("ERROR=V4_1_TRAFFIC_FORMATTER_NOT_FOUND")

stamp=time.strftime("%Y%m%d-%H%M%S")
shutil.copy2(js_path,js_path.with_name(js_path.name+f".bak.{stamp}"))

js=js.replace(old,new,1)
js_path.write_text(js,encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILE_CHANGED=home.js")
print("TRAFFIC_FORMAT=WHOLE_INTEGER")
print("MOTION_LOGIC=UNCHANGED")
print("PHOTO=UNCHANGED")
print("CSS=UNCHANGED")
print("TEMPLATE=UNCHANGED")
print("DB_CHANGED=NO")
print("APPLY=PASS")
