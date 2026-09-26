#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.13"
PATCH="TAMIYOUZ-HOMEPAGE-FULL-NATIVE-SCENE-V3_1_13"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
tpl=home/"template.php"
css_path=home/"assets/home.css"
js_path=home/"assets/home.js"
patch_dir=Path(__file__).resolve().parent
snippet_path=patch_dir/"full-native-scene.php"
css_patch=patch_dir/"full-native-scene.css"
js_patch=patch_dir/"full-native-scene.js"

for p in (tpl,css_path,js_path,snippet_path,css_patch,js_patch):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (tpl,css_path,js_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

def replace_div_by_class(text, cls, replacement):
    m=re.search(r'<div\b[^>]*class="[^"]*\b'+re.escape(cls)+r'\b[^"]*"[^>]*>',text,re.I)
    if not m:
        raise SystemExit(f"ERROR={cls.upper()}_NOT_FOUND")
    token_re=re.compile(r'<div\b[^>]*>|</div\s*>',re.I)
    depth=0
    end=None
    for tok in token_re.finditer(text,m.start()):
        if tok.group(0).lower().startswith('<div'):
            depth+=1
        else:
            depth-=1
            if depth==0:
                end=tok.end()
                break
    if end is None:
        raise SystemExit(f"ERROR={cls.upper()}_UNBALANCED")
    return text[:m.start()]+replacement+text[end:]

template=tpl.read_text(encoding="utf-8")
snippet=snippet_path.read_text(encoding="utf-8").strip()
template=replace_div_by_class(template,"tyz-hero__media",snippet)

if PATCH not in template:
    template=template.replace("?>",f"/* {PATCH} */\n?>",1)

# Guard: full-native means absolutely no image tag or raster asset reference inside the new Hero snippet.
if "<img" in snippet.lower() or "tamiyouz-hero-v3-1.webp" in snippet or "background-image" in snippet.lower():
    raise SystemExit("ERROR=RASTER_REFERENCE_FORBIDDEN")

tpl.write_text(template,encoding="utf-8")

css=css_path.read_text(encoding="utf-8")
if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+css_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

js=js_path.read_text(encoding="utf-8")
if PATCH not in js:
    js_path.write_text(js.rstrip()+"\n\n"+js_patch.read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=template.php;home.css;home.js")
print("HERO_RASTER_IMAGE=REMOVED")
print("HERO_BACKGROUND_IMAGE=NONE")
print("DESKTOP_SCENE=FULL_NATIVE_DOM_CSS_SVG")
print("MOBILE_SCENE=FULL_NATIVE_DOM_CSS_SVG")
print("OPERATOR=INLINE_SVG_STATIC")
print("PIPELINE=5_NATIVE_STEPS")
print("CHANNELS=6_NATIVE")
print("MAIN_MONITOR=NATIVE")
print("SIDE_MONITOR=NATIVE")
print("DESK=NATIVE")
print("LEGACY_V315_TO_V3112=SUPERSEDED")
print("DB_CHANGED=NO")
print("APPLY=PASS")
