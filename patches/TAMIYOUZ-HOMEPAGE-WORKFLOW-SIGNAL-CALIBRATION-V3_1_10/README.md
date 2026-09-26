# TAMIYOUZ-HOMEPAGE-WORKFLOW-SIGNAL-CALIBRATION-V3_1_10

TITLE=Homepage Reference Lock
VERSION=V3.1.10
SCOPE=Workflow Signal Calibration

Finding from the 2026-09-26 screen recording:
- V3.1.9 still visually failed.
- The right stat-card number leaked to the LEFT of the real card.
- The left stat-card overlay collided with the baked 356.2K content.
- More importantly, the intended "New Lead / Auto Follow up / Create Task / Assign Team / Close Deal" cards are the five SMALL workflow cards across the top center, not the two large KPI cards at the far left/right.

Strategy change:
- Disable V3.1.7, V3.1.8 and V3.1.9 card layers.
- Do not animate or overwrite the large baked KPI cards.
- Animate ONLY the real five workflow cards and the Instagram/TikTok icon tiles.
- Keep every effect strictly inside calibrated visual zones.
- Add a staggered connector signal between workflow steps.
- Keep V3.1.6 dashboard animation unchanged.

Desktop calibration source:
Latest rendered Hero recording, 1280x720 viewport.
Hero image observed approx x=171..622, y=177..477.

No text-number replacement is added on raster cards.
No cloned cards.
No translation outside the original artwork.
No person overlap.
No DB/template/image changes.
No live commit/push.
