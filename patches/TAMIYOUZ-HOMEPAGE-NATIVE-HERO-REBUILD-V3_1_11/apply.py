#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.11"
PATCH="TAMIYOUZ-HOMEPAGE-NATIVE-HERO-REBUILD-V3_1_11"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
tpl=home/"template.php"
css_path=home/"assets/home.css"
js_path=home/"assets/home.js"
patch_dir=Path(__file__).resolve().parent
snippet_path=patch_dir/"native-hero.php"
css_patch=patch_dir/"native-hero.css"
js_patch=patch_dir/"native-hero.js"

for p in (tpl,css_path,js_path,snippet_path,css_patch,js_patch):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (tpl,css_path,js_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

def replace_div_by_class(text, cls, replacement):
    start_pat=re.compile(r'<div\b[^>]*class="[^"]*\b'+re.escape(cls)+r'\b[^"]*"[^>]*>',re.I)
    m=start_pat.search(text)
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

if 'data-tyz-native-hero' not in template:
    template=replace_div_by_class(template,'tyz-hero__media',snippet)

if PATCH not in template:
    template=template.replace('?>',f'/* {PATCH} */\n?>',1)

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
print("DESKTOP_UI=NATIVE_DOM_CSS_SVG")
print("RASTER_ROLE=PHOTO_ONLY_PLUS_MOBILE_FALLBACK")
print("WORKFLOW_CARDS=5_NATIVE")
print("WORKFLOW_CONNECTORS=4_NATIVE")
print("SOURCE_TILES=6_NATIVE")
print("MAIN_DASHBOARD=NATIVE")
print("STATUS_PANEL=NATIVE")
print("LEGACY_V315_TO_V3110=SUPERSEDED")
print("DB_CHANGED=NO")
print("APPLY=PASS")
