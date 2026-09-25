#!/usr/bin/env python3
from pathlib import Path
import shutil, sys

MARKER = "TAMIYOUZ-HOMEPAGE-ABOUT-RENDER-COMPOSITION-FIX-V3_2_1"

def die(msg):
    raise SystemExit(msg)

wp = Path(sys.argv[1]).resolve() if len(sys.argv) > 1 else None
if not wp or not (wp / "wp-content").exists():
    die("Usage: apply.py <WP_ROOT>")

patch = Path(__file__).resolve().parent
home = wp / "wp-content" / "mu-plugins" / "tamiyouz-homepage-v2-1"
template = home / "template.php"
css_file = home / "assets" / "home.css"
asset_dst = home / "assets" / "tamiyouz-about-v3-2.webp"
asset_src = patch / "assets" / "tamiyouz-about-v3-2.webp"
css_src = patch / "about-render-fix.css"

for p in (template, css_file, asset_src, css_src):
    if not p.exists():
        die(f"Missing required file: {p}")

template_text = template.read_text(encoding="utf-8")
if "tyz-about-media--v32" not in template_text:
    die("V3_2_BASELINE_MISSING: tyz-about-media--v32 not found; stop without modifying.")

css_text = css_file.read_text(encoding="utf-8")
if MARKER not in css_text:
    shutil.copy2(css_file, css_file.with_suffix(".css.bak-v3.2.1"))
    css_file.write_text(css_text.rstrip() + "\n\n" + css_src.read_text(encoding="utf-8").strip() + "\n", encoding="utf-8")

asset_dst.parent.mkdir(parents=True, exist_ok=True)
shutil.copy2(asset_src, asset_dst)

print("PATCH=TAMIYOUZ-HOMEPAGE-ABOUT-RENDER-COMPOSITION-FIX-V3_2_1")
print("ABOUT_BASELINE=V3.2")
print("ABOUT_ORDER=VISUAL_LEFT_COPY_RIGHT")
print("ASSET=tamiyouz-about-v3-2.webp")
print("DB_CHANGED=NO")
