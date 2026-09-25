# TAMIYOUZ-HOMEPAGE-VISUAL-QA-GATE-V1

TITLE=Homepage Visual QA Gate
VERSION=V1
STATUS=ACTIVE

## Immutable source of truth
MASTER_REFERENCE_NAME=تميّز: منصة نمو رقمية عربية فاتحة.png
MASTER_REFERENCE_LIBRARY_FILE_ID=file_0000000065d08210bd0574879e95aa17
MASTER_REFERENCE_DIMENSIONS=728x2160
REFERENCE_MODE=LIGHT
REFERENCE_RULE=COMPOSITION_AND_VISUAL_HIERARCHY_1_TO_1_WHERE_PRACTICAL

The Light reference is the visual source of truth. Dark mode is a parity adaptation of the same hierarchy; it is not allowed to redefine composition.

## Current baseline under review
BASELINE_VERSION=V2.8
BASELINE_LIGHT_FILE_ID=file_000000005de881f48153f4c74be48283
BASELINE_DARK_FILE_ID=file_00000000849c8246bf33e35846e65fc7
BASELINE_STATUS=VISUAL_FAIL

## Mandatory gate sequence
1. Lock the master reference before editing.
2. Record a section-by-section delta audit against the current rendered screenshot.
3. Build only from documented deltas. No invented "improvements".
4. Technical verification is a separate gate and can never produce VISUAL_PASS.
5. Rendered screenshot evidence is mandatory after deployment.
6. Compare rendered output against the master reference section by section.
7. VISUAL_PASS is allowed only when every blocking delta is closed.
8. User performs canonical Review Push + Execute Reviewed Push only after VISUAL_PASS.

## What does NOT count as visual evidence
- CSS markers
- source selectors
- number of responsive rules
- HTTP 200
- "5 bar references found"
- a feature being present in DOM/source
- OpenHands describing the visual without a rendered screenshot comparison

## No-drift rules
- Never replace a reference visual with a new visual idea because it seems "better".
- Never turn intentional reference whitespace/dark space into charts, cards, metrics or decoration.
- Never add fake metrics, clients, testimonials or claims just because they exist visually in the reference.
- Reference content may be replaced with truthful Tamiyouz content while preserving geometry/hierarchy.
- Every patch must state TITLE, VERSION, SCOPE, BLOCKING_DELTAS, VERIFY, RESULT, NEXT.
- One version addresses the documented delta set only.
- No canonical commit/push from OpenHands or server.

## Pass states
TECHNICAL_PASS = runtime/source/deployment checks passed.
VISUAL_FAIL = at least one blocking rendered delta remains.
VISUAL_PASS = rendered screenshot comparison closes all blocking deltas.
CLOSED = VISUAL_PASS plus user canonical push completed.
