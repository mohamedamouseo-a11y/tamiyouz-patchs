/* TAMIYOUZ-HOMEPAGE-WORKFLOW-SIGNAL-CALIBRATION-V3_1_10 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 .tyz-hero__media');
  if(!hero || hero.dataset.tyzWorkflowSignal==='3110') return;
  const img=hero.querySelector('.tyz-hero-media__image');
  if(!img) return;

  hero.dataset.tyzWorkflowSignal='3110';
  hero.classList.add('tyz-workflow-signal-v3110');

  // Remove V3.1.7/V3.1.8/V3.1.9 card layers only. Preserve V3.1.6 dashboard.
  hero.querySelectorAll(':scope > .tyz-living-cards,:scope > .tyz-native-cards,:scope > .tyz-inframe-cards,:scope > .tyz-workflow-signal').forEach(el=>el.remove());

  const layer=document.createElement('div');
  layer.className='tyz-workflow-signal';
  layer.setAttribute('aria-hidden','true');
  layer.innerHTML=`
    <span class="tyz-workflow-signal__card tyz-workflow-signal__card--lead" data-wf="lead"></span>
    <span class="tyz-workflow-signal__card tyz-workflow-signal__card--follow" data-wf="follow"></span>
    <span class="tyz-workflow-signal__card tyz-workflow-signal__card--create" data-wf="create"></span>
    <span class="tyz-workflow-signal__card tyz-workflow-signal__card--assign" data-wf="assign"></span>
    <span class="tyz-workflow-signal__card tyz-workflow-signal__card--close" data-wf="close"></span>
    <span class="tyz-workflow-signal__connector tyz-workflow-signal__connector--1"></span>
    <span class="tyz-workflow-signal__connector tyz-workflow-signal__connector--2"></span>
    <span class="tyz-workflow-signal__connector tyz-workflow-signal__connector--3"></span>
    <span class="tyz-workflow-signal__connector tyz-workflow-signal__connector--4"></span>
    <span class="tyz-workflow-signal__social tyz-workflow-signal__social--instagram" data-social-zone="instagram"></span>
    <span class="tyz-workflow-signal__social tyz-workflow-signal__social--tiktok" data-social-zone="tiktok"></span>`;
  hero.appendChild(layer);

  const cards=[...layer.querySelectorAll('[data-wf]')];
  const socials=[...layer.querySelectorAll('[data-social-zone]')];
  const zones=[...cards,...socials];
  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));

  const clearActive=()=>zones.forEach(z=>z.classList.remove('is-active'));

  const wake=(zone,strong=false)=>{
    zones.forEach(z=>{
      z.classList.toggle('is-active',strong && z===zone);
      if(z!==zone) z.classList.remove('is-awake');
    });
    if(zone && !strong) zone.classList.add('is-awake');
  };

  if(fine && !reduce){
    hero.addEventListener('pointermove',(e)=>{
      let nearest=null,dist=Infinity;
      zones.forEach(zone=>{
        const r=zone.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=zone;}
      });
      if(!nearest){clearActive();return;}

      const r=nearest.getBoundingClientRect();
      const threshold=Math.max(22,Math.max(r.width,r.height)*1.15);
      if(dist>threshold){clearActive();return;}

      if(!nearest.classList.contains('is-active')) wake(nearest,true);

      const x=clamp((e.clientX-r.left)/r.width,0,1);
      const y=clamp((e.clientY-r.top)/r.height,0,1);
      nearest.style.setProperty('--mx',(x*100).toFixed(1)+'%');
      nearest.style.setProperty('--my',(y*100).toFixed(1)+'%');
    });
    hero.addEventListener('pointerleave',clearActive);
  }

  let inView=true;
  if('IntersectionObserver' in window){
    new IntersectionObserver(entries=>{
      inView=!!entries[0]?.isIntersecting;
    },{threshold:.18}).observe(hero);
  }

  if(!reduce){
    const idleOrder=[cards[0],cards[1],cards[2],cards[3],cards[4],socials[0],socials[1]].filter(Boolean);
    let phase=0;
    setInterval(()=>{
      if(!inView || document.hidden) return;
      if(fine && hero.matches(':hover')) return;
      zones.forEach(z=>z.classList.remove('is-awake'));
      const zone=idleOrder[phase%idleOrder.length];
      phase++;
      if(zone){
        zone.classList.add('is-awake');
        setTimeout(()=>zone.classList.remove('is-awake'),720);
      }
    },760);
  }
})();
