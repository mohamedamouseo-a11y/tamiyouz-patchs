# TAMIYOUZ-HOMEPAGE-V4-REALISTIC-RENDER-RECOVERY-V4_0_1

TITLE=Homepage Reference Lock
VERSION=V4.0.1
SCOPE=Realistic Hero Render Recovery

Root cause:
An older cumulative V3.1.1 rule still exists in the aggregated homepage CSS:

#tyz-home-v21 .tyz-hero__media > *:not(.tyz-hero-media__image) {
  display:none!important;
  visibility:hidden!important;
}

V4.0 replaced the old Hero media children with new classes:
- .tyz-rh-photo
- .tyz-rh-kpis
- .tyz-rh-screen
- .tyz-rh-glass

Because the legacy rule is !important and more specific than the original V4.0 visibility declarations, the new V4.0 children were hidden, leaving only the dark Hero container background visible.

V4.0.1:
- does NOT redesign V4.0
- does NOT add animation
- adds higher-specificity visibility recovery rules for ONLY V4.0 approved children
- keeps legacy layers suppressed
- preserves the realistic static base + native monitor/KPI architecture

No template change.
No JS change.
No DB change.
No canonical commit/push.
