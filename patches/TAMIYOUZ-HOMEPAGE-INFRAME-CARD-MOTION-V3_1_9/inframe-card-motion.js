/* TAMIYOUZ-HOMEPAGE-INFRAME-CARD-MOTION-V3_1_9 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 .tyz-hero__media');
  if(!hero || hero.dataset.tyzInframeCards==='319') return;
  const img=hero.querySelector('.tyz-hero-media__image');
  if(!img) return;

  hero.dataset.tyzInframeCards='319';
  hero.classList.add('tyz-inframe-v319');

  // Remove previous card overlays at runtime; keep V3.1.6 dashboard layer.
  hero.querySelectorAll(':scope > .tyz-living-cards,:scope > .tyz-native-cards,:scope > .tyz-inframe-cards').forEach(el=>el.remove());

  const layer=document.createElement('div');
  layer.className='tyz-inframe-cards';
  layer.setAttribute('aria-hidden','true');
  layer.innerHTML=`
    <div class="tyz-inframe-zone tyz-inframe-zone--leads" data-zone="leads">
      <div class="tyz-inframe-zone__content">
        <span class="tyz-inframe-zone__wash"></span>
        <div class="tyz-inframe-zone__metric"><strong data-value>29</strong><small>DEMO</small></div>
        <div class="tyz-inframe-zone__mini"><svg viewBox="0 0 100 22" preserveAspectRatio="none"><polyline points="0,19 18,17 35,15 52,13 67,10 83,7 100,4"></polyline></svg></div>
      </div>
    </div>
    <div class="tyz-inframe-zone tyz-inframe-zone--follow" data-zone="follow">
      <div class="tyz-inframe-zone__content">
        <span class="tyz-inframe-zone__wash"></span>
        <div class="tyz-inframe-zone__metric"><strong data-value>16</strong><small>DEMO</small></div>
        <div class="tyz-inframe-zone__mini"><svg viewBox="0 0 100 22" preserveAspectRatio="none"><polyline points="0,18 17,16 34,17 52,12 68,11 84,7 100,5"></polyline></svg></div>
      </div>
    </div>
    <div class="tyz-inframe-zone tyz-inframe-zone--instagram" data-zone="instagram">
      <div class="tyz-inframe-zone__content"><span class="tyz-inframe-zone__wash"></span><span class="tyz-inframe-zone__social-pulse"></span></div>
    </div>
    <div class="tyz-inframe-zone tyz-inframe-zone--tiktok" data-zone="tiktok">
      <div class="tyz-inframe-zone__content"><span class="tyz-inframe-zone__wash"></span><span class="tyz-inframe-zone__social-pulse"></span></div>
    </div>`;
  hero.appendChild(layer);

  const zones=[...layer.querySelectorAll('[data-zone]')];
  const byName=Object.fromEntries(zones.map(z=>[z.dataset.zone,z]));
  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));
  const values={leads:{v:29,min:24,max:39},follow:{v:16,min:11,max:25}};

  const tick=(name,boost=false)=>{
    const zone=byName[name], cfg=values[name];
    if(!zone || !cfg) return;
    cfg.v=clamp(cfg.v+rand(boost?1:-1,boost?3:2),cfg.min,cfg.max);
    const el=zone.querySelector('[data-value]');
    if(el) el.textContent=String(cfg.v).padStart(name==='follow'?2:1,'0');
    zone.classList.remove('is-ticking');
    void zone.offsetWidth;
    zone.classList.add('is-ticking');
    setTimeout(()=>zone.classList.remove('is-ticking'),430);
  };

  const wake=(zone,strong=false)=>{
    zones.forEach(z=>{
      z.classList.toggle('is-active',strong && z===zone);
      if(z!==zone) z.classList.remove('is-awake');
    });
    if(!zone) return;
    if(!strong) zone.classList.add('is-awake');
    const name=zone.dataset.zone;
    if(name==='leads'||name==='follow') tick(name,strong);
  };

  const clear=()=>{
    zones.forEach(z=>{
      z.classList.remove('is-active');
      z.style.setProperty('--ix','0px');
      z.style.setProperty('--iy','0px');
      z.style.setProperty('--mx','50%');
      z.style.setProperty('--my','50%');
    });
  };

  if(fine && !reduce){
    hero.addEventListener('pointermove',(e)=>{
      let nearest=null,dist=Infinity;
      zones.forEach(z=>{
        const r=z.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=z;}
      });
      if(!nearest){clear();return;}
      const r=nearest.getBoundingClientRect();
      const threshold=Math.max(r.width,r.height)*1.05;
      if(dist>threshold){clear();return;}

      if(!nearest.classList.contains('is-active')) wake(nearest,true);
      const x=clamp((e.clientX-r.left)/r.width,0,1);
      const y=clamp((e.clientY-r.top)/r.height,0,1);

      // Max ~1px movement and it remains clipped by the exact card mask.
      nearest.style.setProperty('--ix',((x-.5)*1.8).toFixed(2)+'px');
      nearest.style.setProperty('--iy',((y-.5)*1.4).toFixed(2)+'px');
      nearest.style.setProperty('--mx',(x*100).toFixed(1)+'%');
      nearest.style.setProperty('--my',(y*100).toFixed(1)+'%');

      zones.filter(z=>z!==nearest).forEach(z=>{
        z.classList.remove('is-active');
        z.style.setProperty('--ix','0px');
        z.style.setProperty('--iy','0px');
      });
    });
    hero.addEventListener('pointerleave',clear);
  }

  let inView=true;
  if('IntersectionObserver' in window){
    const io=new IntersectionObserver(entries=>{inView=!!entries[0]?.isIntersecting;},{threshold:.18});
    io.observe(hero);
  }

  if(!reduce){
    const order=['leads','instagram','follow','tiktok'];
    let phase=0;
    setInterval(()=>{
      if(!inView || document.hidden) return;
      if(fine && hero.matches(':hover')) return;
      const zone=byName[order[phase%order.length]];
      phase++;
      zones.forEach(z=>z.classList.remove('is-awake'));
      if(zone){
        wake(zone,false);
        setTimeout(()=>zone.classList.remove('is-awake'),820);
      }
    },1700);
  }
})();
