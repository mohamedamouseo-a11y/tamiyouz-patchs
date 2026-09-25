#!/usr/bin/env python3
from pathlib import Path
import shutil, sys

MARKER = "TAMIYOUZ-HOMEPAGE-ABOUT-ASSET-BIND-V3_2"

def die(msg):
    raise SystemExit(msg)

wp = Path(sys.argv[1]).resolve() if len(sys.argv) > 1 else None
if not wp or not (wp / "wp-content").exists():
    die("Usage: apply.py <WP_ROOT>")

patch = Path(__file__).resolve().parent
mu = wp / "wp-content" / "mu-plugins"
home = mu / "tamiyouz-homepage-v2-1"
template = home / "template.php"
css_file = home / "assets" / "home.css"
asset_dst = home / "assets" / "tamiyouz-about-v3-2.webp"
asset_src = patch / "assets" / "tamiyouz-about-v3-2.webp"
css_src = patch / "about-bind.css"

for p in (template, css_file, asset_src, css_src):
    if not p.exists():
        die(f"Missing required file: {p}")

old = """        <div class="tyz-about__visual tyz-reveal">
            <div class="tyz-office-card">
                <div class="tyz-office-card__screen"><span></span><b></b><b></b><b></b></div>
                <div class="tyz-office-card__badge"><small>GROWTH SIGNAL</small><strong>Insight → Action</strong></div>
                <div class="tyz-office-card__check" aria-hidden="true"><span>استراتيجية واضحة</span><span>تنفيذ مترابط</span><span>تحسين مستمر</span></div>
            </div>
        </div>"""

new = """        <div class="tyz-about__visual tyz-reveal">
            <figure class="tyz-about-media tyz-about-media--v32">
                <img src="<?php echo esc_url(content_url('/mu-plugins/tamiyouz-homepage-v2-1/assets/tamiyouz-about-v3-2.webp')); ?>" alt="عن تميز — حلول رقمية وبرمجيات ونمو" width="1024" height="768" loading="lazy" decoding="async">
            </figure>
        </div>"""

text = template.read_text(encoding="utf-8")
if "tyz-about-media--v32" not in text:
    if old not in text:
        die("ABOUT_ANCHOR_MISMATCH: canonical About visual block not found; stop without modifying.")
    shutil.copy2(template, template.with_suffix(".php.bak-v3.2"))
    template.write_text(text.replace(old, new, 1), encoding="utf-8")

css_text = css_file.read_text(encoding="utf-8")
if MARKER not in css_text:
    shutil.copy2(css_file, css_file.with_suffix(".css.bak-v3.2"))
    css_file.write_text(css_text.rstrip() + "\n\n" + css_src.read_text(encoding="utf-8").strip() + "\n", encoding="utf-8")

asset_dst.parent.mkdir(parents=True, exist_ok=True)
shutil.copy2(asset_src, asset_dst)

print("PATCH=TAMIYOUZ-HOMEPAGE-ABOUT-ASSET-BIND-V3_2")
print("ABOUT_BIND=PASS")
print("ASSET=tamiyouz-about-v3-2.webp")
print("DB_CHANGED=NO")
