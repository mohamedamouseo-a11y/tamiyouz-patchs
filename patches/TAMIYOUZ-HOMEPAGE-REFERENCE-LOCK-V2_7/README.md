# TAMIYOUZ-HOMEPAGE-REFERENCE-LOCK-V2_7

TITLE=Homepage Reference Lock
VERSION=V2.7

Final screenshot QA correction after V2.6.

V2.6 visual review:
- Light/Ivory hierarchy is restored.
- Dark mode hierarchy is preserved.
- Hero, About, Services, Showreel, AI, Process and CTA are visually consistent.
- One visible defect remained: the DIGITAL GROWTH card visual rendered as a blank dark panel.

Root cause:
- the growth bars used percentage heights inside a parent whose height was not definite, so the bars could collapse at runtime despite existing in source.

V2.7:
- gives the growth chart a definite height
- gives each bar an explicit height
- preserves responsive/mobile behavior
- changes no content, DB, template structure or other homepage sections

TCRM Case Study remains HOLD until this final visual check passes.
