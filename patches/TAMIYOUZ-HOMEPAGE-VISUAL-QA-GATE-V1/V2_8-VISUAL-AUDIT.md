# Homepage Reference Lock — V2.8 Visual Audit

TITLE=Homepage Reference Lock
VERSION=V2.8
REFERENCE=MASTER_REFERENCE_LIGHT
STATUS=VISUAL_FAIL
NEXT_VERSION=V2.9
NEXT_VERSION_NAME=Exact Reference Match

## Evidence
REFERENCE: 728x2160 — library file file_0000000065d08210bd0574879e95aa17
CURRENT_LIGHT: 624x2048 — file file_000000005de881f48153f4c74be48283
CURRENT_DARK: 624x2048 — file file_00000000849c8246bf33e35846e65fc7

## Blocking delta matrix

### 01 Header
REFERENCE:
- prominent Tamiyouz mark/wordmark
- wider luxury header rhythm
- navigation breathes across the center
- CTA/toggle have more visual weight

V2.8:
- brand and controls are visually smaller
- navigation is compressed
- header reads utilitarian rather than premium editorial

FIX:
- match reference header proportions and brand presence
- preserve truthful current navigation labels

### 02 Hero
REFERENCE:
- much stronger right-side headline hierarchy
- two primary/secondary CTAs
- three prominent trust/value blocks below
- left visual has layered depth, dark vertical navigation and several floating cards
- soft photographic/ambient background creates premium depth

V2.8:
- hero visual is a simplified abstract dashboard
- CTA/meta area is much lighter and smaller
- left visual lacks reference layering and spatial depth

FIX:
- rebuild visual layering and hero proportions
- no fake numerical metrics: use qualitative value blocks with same geometry

### 03 Capability strip
REFERENCE:
- refined mixed Arabic/English capability line with fine separators and stronger spacing

V2.8:
- tiny all-English labels and weaker rhythm

FIX:
- match reference spacing/weight while using real capability names

### 04 About
REFERENCE:
- copy and visual composition are opposite the current arrangement
- real/editorial workspace visual with floating growth/list cards
- text hierarchy and feature row are larger and more open

V2.8:
- generic schematic card
- split orientation does not match master reference
- supporting cards are too compact

FIX:
- restore reference side orientation
- rebuild the visual composition without fake performance claims

### 05 Services
REFERENCE:
- centered section introduction
- six larger cards with clear line icons
- circular arrow affordance
- softer shadows and more generous card whitespace

V2.8:
- heading/paragraph split across opposite sides
- smaller flat cards with code-like circle badges
- overall section is visually denser and less premium

FIX:
- restore centered reference composition
- redesign card internals to reference hierarchy

### 06 Showreel
REFERENCE:
- cinematic photographic strip
- image/environment is a major part of the section
- large play treatment and copy anchored into the image

V2.8:
- abstract black panel
- no photographic/environmental depth
- copy/play composition differs

FIX:
- match cinematic composition using non-country-specific neutral premium imagery/visual treatment

### 07 AI
REFERENCE:
- light dashboard with dark sidebar and gold data visualization
- copy on the right
- two CTA buttons under copy
- product visual is larger and brighter

V2.8:
- almost fully dark console
- four small feature pills replace CTA structure
- visual hierarchy is flatter

FIX:
- rebuild dashboard color hierarchy and CTA geometry to match reference
- no fake metrics

### 08 Work
REFERENCE:
- wide CRM card is mostly dark with a real-product-style screenshot panel
- two lower dark cards
- lower-left card uses a subtle line/area chart, not five large bars
- all three cards feel like one dark portfolio system

V2.8:
- wide CRM card has a large white copy panel
- lower cards use white copy footers
- V2.8 added five large gold bars that are not in the reference

FIX:
- remove V2.8 five-bar invention
- restore dark portfolio composition and subtle chart treatment
- later real TCRM screenshots can replace the schematic placeholder without changing geometry

### 09 Process
REFERENCE:
- open white background
- four spacious standalone steps with visible icons/numbers
- stronger typography, no heavy boxed strip

V2.8:
- steps are enclosed in one bordered container
- hierarchy is smaller

FIX:
- restore open reference layout and icon/number rhythm

### 10 CTA
REFERENCE:
- warm photographic/cinematic banner
- large gold geometric brand element
- strong spatial separation of copy/action

V2.8:
- abstract dark gradient panel
- missing reference imagery/brand geometry

FIX:
- reproduce the banner composition using neutral premium imagery; no national/country motifs

### 11 Footer
REFERENCE:
- stronger logo lockup
- social/navigation structure
- more deliberate spacing

V2.8:
- very small/simple footer

FIX:
- restore reference footer scale and spacing using only real links

## Global blockers
- Typography in V2.8 is consistently smaller at full-page scale.
- V2.8 has more empty disconnected space around several section headings.
- Visual mockups are simplified compared with the layered reference.
- Dark mode may stay, but Light is the master reference and must be locked first.

## V2.9 scope rule
V2.9 must fix only the deltas above.
It must NOT invent new cards, charts, metrics, imagery concepts, section order or layout.
No VISUAL_PASS may be reported from source/marker verification.
