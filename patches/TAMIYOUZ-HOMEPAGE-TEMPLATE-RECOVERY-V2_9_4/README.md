# TAMIYOUZ-HOMEPAGE-TEMPLATE-RECOVERY-V2_9_4

TITLE=Homepage Reference Lock
VERSION=V2.9.4
SCOPE=Template Recovery

## Root cause
V2.9.3-DIAG confirmed the live homepage template was truncated during the V2.9.1 deployment:
- normal HTML anchors: 1/8
- bypass HTML anchors: 1/8
- live template anchors: 1/8
- cache is not the root cause

## Recovery strategy
Restore a known-complete template from the canonical Tamiyouz repository.
Do not attempt visual redesign in this recovery version.

The recovered template includes all 8 required text anchors:
1. نبني نموًا رقميًا
2. لسنا مجرد وكالة رقمية
3. حلول متكاملة
4. من الفكرة إلى نظام
5. الذكاء الاصطناعي ليس إضافة
6. أعمال رقمية
7. رحلة واضحة
8. لديك فكرة أو تحدٍ

A tiny inline fail-open rule is embedded in the template so .tyz-reveal content remains visible even if homepage JS is unavailable.

## Rules
- replace template.php only
- backup current live template first
- no CSS redesign
- no DB changes
- no commit/push
- TECHNICAL_PASS is not VISUAL_PASS
