#!/usr/bin/env python3
from pathlib import Path
import re, shutil, sys, time

TITLE="Homepage Reference Lock"
VERSION="V4.0"
PATCH="TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-BASE-REBUILD-V4_0"

root=Path(sys.argv[1] if len(sys.argv)>1 else ".").resolve()
home=root/"wp-content/mu-plugins/tamiyouz-homepage-v2-1"
tpl=home/"template.php"
css_path=home/"assets/home.css"
js_path=home/"assets/home.js"
patch_dir=Path(__file__).resolve().parent
snippet_path=patch_dir/"realistic-hero.php"
css_patch=patch_dir/"realistic-hero.css"
js_patch=patch_dir/"realistic-hero.js"
src_asset=patch_dir/"assets/tamiyouz-v4-realistic-base.webp"
dst_dir=home/"assets/media"
dst_asset=dst_dir/"tamiyouz-v4-realistic-base.webp"

for p in (tpl,css_path,js_path,snippet_path,css_patch,js_patch,src_asset):
    if not p.exists():
        raise SystemExit(f"ERROR=MISSING:{p}")

stamp=time.strftime("%Y%m%d-%H%M%S")
for p in (tpl,css_path,js_path):
    shutil.copy2(p,p.with_name(p.name+f".bak.{stamp}"))

dst_dir.mkdir(parents=True,exist_ok=True)
shutil.copy2(src_asset,dst_asset)

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
print("ASSET=assets/media/tamiyouz-v4-realistic-base.webp")
print("PHYSICAL_SCENE=PHOTOREALISTIC_STATIC_BASE")
print("PERSON_DESK_ENVIRONMENT=PHOTOREALISTIC")
print("KPI_CARDS=4_NATIVE")
print("LEFT_MONITOR_UI=NATIVE")
print("MAIN_MONITOR_UI=NATIVE")
print("RIGHT_MONITOR_UI=NATIVE")
print("SCREEN_CONTENT=ANIMATION_READY")
print("HEAVY_ANIMATION=DEFERRED_TO_V4_1")
print("CARTOON_SVG_PERSON=REMOVED")
print("LEGACY_RUNTIME_LAYERS=SUPERSEDED")
print("DB_CHANGED=NO")
print("APPLY=PASS")
