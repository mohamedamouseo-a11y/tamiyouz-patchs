#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V3.1.12"
PATCH="TAMIYOUZ-HOMEPAGE-INTEGRATED-NATIVE-COMMAND-HERO-V3_1_12"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
tpl=home/"template.php"
css_path=home/"assets/home.css"
js_path=home/"assets/home.js"
patch_dir=Path(__file__).resolve().parent

for p in (tpl,css_path,js_path,patch_dir/"native-command-hero.php",patch_dir/"native-command-hero.css",patch_dir/"native-command-hero.js"):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (tpl,css_path,js_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

def replace_div_by_class(text, cls, replacement):
    m=re.search(r'<div\b[^>]*class="[^"]*\b'+re.escape(cls)+r'\b[^"]*"[^>]*>',text,re.I)
    if not m:
        raise SystemExit(f"ERROR={cls.upper()}_NOT_FOUND")
    tok=re.compile(r'<div\b[^>]*>|</div\s*>',re.I)
    depth=0
    end=None
    for t in tok.finditer(text,m.start()):
        if t.group(0).lower().startswith('<div'):
            depth+=1
        else:
            depth-=1
            if depth==0:
                end=t.end();break
    if end is None:
        raise SystemExit(f"ERROR={cls.upper()}_UNBALANCED")
    return text[:m.start()]+replacement+text[end:]

template=tpl.read_text(encoding="utf-8")
snippet=(patch_dir/"native-command-hero.php").read_text(encoding="utf-8").strip()
template=replace_div_by_class(template,"tyz-hero__media",snippet)
if PATCH not in template:
    template=template.replace("?>",f"/* {PATCH} */\n?>",1)
tpl.write_text(template,encoding="utf-8")

css=css_path.read_text(encoding="utf-8")
if PATCH not in css:
    css_path.write_text(css.rstrip()+"\n\n"+(patch_dir/"native-command-hero.css").read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

js=js_path.read_text(encoding="utf-8")
if PATCH not in js:
    js_path.write_text(js.rstrip()+"\n\n"+(patch_dir/"native-command-hero.js").read_text(encoding="utf-8").strip()+"\n",encoding="utf-8")

print(f"TITLE={TITLE}")
print(f"VERSION={VERSION}")
print(f"PATCH={PATCH}")
print("FILES_CHANGED=template.php;home.css;home.js")
print("COMPOSITION=ONE_INTEGRATED_COMMAND_DECK")
print("WORKFLOW=INSIDE_DECK")
print("SOURCES=INSIDE_DECK")
print("DASHBOARD=INSIDE_DECK")
print("PHOTO=PANE_ONLY")
print("DETACHED_SIDEPANEL=REMOVED")
print("LEGACY_OVERLAYS=SUPERSEDED")
print("APPLY=PASS")
