/* TAMIYOUZ-HOMEPAGE-INTERACTIVE-COMMAND-HERO-V3_1_5 */
(()=> {
  const SELECTOR='#tyz-home-v21 .tyz-hero__media';
  const hero=document.querySelector(SELECTOR);
  if(!hero || hero.dataset.tyzInteractiveHero==='315') return;
  const img=hero.querySelector('.tyz-hero-media__image');
  if(!img) return;

  hero.dataset.tyzInteractiveHero='315';
  hero.classList.add('tyz-command-hero');

  const fx=document.createElement('div');
  fx.className='tyz-command-hero__fx';
  fx.setAttribute('aria-hidden','true');
  fx.innerHTML=
    '<div class="tyz-command-hero__spotlight"></div>'+
    '<div class="tyz-command-hero__lens"></div>'+
    '<div class="tyz-command-hero__pulse"></div>'+
    '<div class="tyz-command-hero__telemetry">'+
      '<div class="tyz-command-hero__metric" data-metric="sync"><small>SYNC HEALTH <b>DEMO</b></small><strong>84%</strong><span><i style="--tyz-meter:84%"></i></span></div>'+
      '<div class="tyz-command-hero__metric" data-metric="flow"><small>WORKFLOWS <b>DEMO</b></small><strong>06</strong><span><i style="--tyz-meter:62%"></i></span></div>'+
      '<div class="tyz-command-hero__metric" data-metric="signal"><small>AI SIGNALS <b>DEMO</b></small><strong>18</strong><span><i style="--tyz-meter:68%"></i></span></div>'+
    '</div>';
  hero.appendChild(fx);

  const reduce=window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia && window.matchMedia('(hover:hover) and (pointer:fine)').matches;

  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const set=(k,v)=>hero.style.setProperty(k,v);

  if(fine && !reduce){
    hero.addEventListener('pointerenter',()=>hero.classList.add('is-interacting'));
    hero.addEventListener('pointermove',(e)=>{
      const r=hero.getBoundingClientRect();
      if(!r.width || !r.height) return;
      const x=clamp((e.clientX-r.left)/r.width,0,1);
      const y=clamp((e.clientY-r.top)/r.height,0,1);
      const nx=(x-.5)*2;
      const ny=(y-.5)*2;
      set('--tyz-hx',(x*100).toFixed(2)+'%');
      set('--tyz-hy',(y*100).toFixed(2)+'%');
      set('--tyz-rx',(-ny*2.2).toFixed(2)+'deg');
      set('--tyz-ry',(nx*2.4).toFixed(2)+'deg');
      set('--tyz-p1x',(nx*5).toFixed(1)+'px');
      set('--tyz-p1y',(ny*5).toFixed(1)+'px');
      set('--tyz-p2x',(nx*-8).toFixed(1)+'px');
      set('--tyz-p2y',(ny*-6).toFixed(1)+'px');
      set('--tyz-p3x',(nx*11).toFixed(1)+'px');
      set('--tyz-p3y',(ny*8).toFixed(1)+'px');
    });
    hero.addEventListener('pointerleave',()=>{
      hero.classList.remove('is-interacting');
      set('--tyz-hx','50%'); set('--tyz-hy','50%');
      set('--tyz-rx','0deg'); set('--tyz-ry','0deg');
      ['--tyz-p1x','--tyz-p1y','--tyz-p2x','--tyz-p2y','--tyz-p3x','--tyz-p3y'].forEach(k=>set(k,'0px'));
    });
  }

  const metrics={
    sync:{el:fx.querySelector('[data-metric="sync"]'),min:82,max:97,suffix:'%',pad:0},
    flow:{el:fx.querySelector('[data-metric="flow"]'),min:4,max:9,suffix:'',pad:2},
    signal:{el:fx.querySelector('[data-metric="signal"]'),min:14,max:29,suffix:'',pad:2}
  };
  const nextValue=(m)=>Math.round(m.min+Math.random()*(m.max-m.min));
  const render=(m,v)=>{
    const strong=m.el && m.el.querySelector('strong');
    const bar=m.el && m.el.querySelector('span i');
    if(!strong || !bar) return;
    const txt=m.pad ? String(v).padStart(m.pad,'0') : String(v);
    strong.textContent=txt+m.suffix;
    const pct=m.suffix==='%' ? v : Math.round(40+((v-m.min)/(m.max-m.min))*48);
    bar.style.setProperty('--tyz-meter',clamp(pct,18,98)+'%');
  };

  if(!reduce){
    let active=true;
    const io=('IntersectionObserver' in window) ? new IntersectionObserver((entries)=>{
      active=!!entries[0]?.isIntersecting;
    },{threshold:.2}) : null;
    if(io) io.observe(hero);

    window.setInterval(()=>{
      if(!active || document.hidden) return;
      Object.values(metrics).forEach(m=>render(m,nextValue(m)));
    },1250);
  }
})();
