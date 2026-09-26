/* TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-BASE-REBUILD-V4_0 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 [data-tyz-rh-v40]');
  if(!hero || hero.dataset.rhReady==='40') return;
  hero.dataset.rhReady='40';

  /* Clean any runtime nodes appended by older experiments. V4.0 itself is static. */
  hero.querySelectorAll('.tyz-command-hero__fx,.tyz-live-command,.tyz-living-cards,.tyz-native-cards,.tyz-inframe-cards,.tyz-workflow-signal,.tyz-native-hero__scene,.tyz-nc-scene,.tyz-ns-stage,.tyz-pw-scene').forEach(el=>el.remove());

  /* Deterministic initial bars. V4.1 will own animation/timelines. */
  const heights=[38,57,49,68,61,79,53,71,64];
  hero.querySelectorAll('[data-rh-bars] i').forEach((bar,i)=>bar.style.setProperty('--h',(heights[i]||45)+'%'));

  /* Explicitly publish stable hooks for the animation phase. */
  hero.dataset.rhAnimationReady='yes';
})();
