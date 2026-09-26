/* TAMIYOUZ-HOMEPAGE-INTEGRATED-NATIVE-COMMAND-HERO-V3_1_12 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 [data-tyz-native-command-v3112]');
  if(!hero || hero.dataset.ncReady==='3112') return;
  hero.dataset.ncReady='3112';

  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const steps=[...hero.querySelectorAll('[data-nc-step]')];
  const flows=[...hero.querySelectorAll('.tyz-nc-flow')];
  const sources=[...hero.querySelectorAll('[data-nc-source]')];
  const pulseSources=sources.filter(s=>s.classList.contains('tyz-nc-source--pulse'));
  const bars=[...hero.querySelectorAll('[data-nc-bars] i')];
  const status=hero.querySelector('[data-nc-status]');
  const activity=[...hero.querySelectorAll('[data-nc-activity] span b')];
  const metricCfg={
    health:{el:hero.querySelector('[data-nc-metric="health"]'),meter:hero.querySelector('[data-nc-meter="health"]'),v:88,min:84,max:97,suffix:'%'},
    flow:{el:hero.querySelector('[data-nc-metric="flow"]'),meter:hero.querySelector('[data-nc-meter="flow"]'),v:6,min:4,max:9,pad:2},
    signal:{el:hero.querySelector('[data-nc-metric="signal"]'),meter:hero.querySelector('[data-nc-meter="signal"]'),v:18,min:14,max:29,pad:2}
  };
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));

  let visible=true;
  if('IntersectionObserver' in window){
    new IntersectionObserver(entries=>{visible=!!entries[0]?.isIntersecting;},{threshold:.18}).observe(hero);
  }

  let wf=0;
  const cycleWorkflow=()=>{
    steps.forEach((step,i)=>step.classList.toggle('is-live',i===wf));
    flows.forEach((flow,i)=>flow.classList.toggle('is-live',i===wf-1));
    wf=(wf+1)%steps.length;
  };

  const updateMetrics=()=>{
    Object.entries(metricCfg).forEach(([key,cfg])=>{
      cfg.v=clamp(cfg.v+rand(-1,2),cfg.min,cfg.max);
      if(cfg.el){
        let value=String(cfg.v);
        if(cfg.pad) value=value.padStart(cfg.pad,'0');
        cfg.el.textContent=value+(cfg.suffix||'');
      }
      if(cfg.meter){
        const pct=key==='health'?cfg.v:Math.round(40+((cfg.v-cfg.min)/(cfg.max-cfg.min))*48);
        cfg.meter.style.setProperty('--w',clamp(pct,28,98)+'%');
      }
    });
    bars.forEach((bar,i)=>bar.style.setProperty('--h',clamp(rand(26,86)+(i>4?5:0),24,94)+'%'));
    activity.forEach(row=>row.style.setProperty('--w',rand(30,90)+'%'));
    if(status) status.textContent=status.textContent==='LIVE'?'SYNCING':'LIVE';
  };

  const resetLocal=()=>{
    [...steps,...sources].forEach(el=>{
      el.style.setProperty('--tx','0px');
      el.style.setProperty('--ty','0px');
    });
    sources.forEach(s=>s.classList.remove('is-live'));
  };

  if(fine && !reduce){
    hero.addEventListener('pointermove',e=>{
      let nearest=null,dist=Infinity;
      [...steps,...sources].forEach(el=>{
        const r=el.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=el;}
      });
      if(!nearest) return;
      const r=nearest.getBoundingClientRect();
      const threshold=Math.max(r.width,r.height)*1.0;
      [...steps,...sources].forEach(el=>{
        if(el!==nearest){
          el.style.setProperty('--tx','0px');el.style.setProperty('--ty','0px');
          if(el.hasAttribute('data-nc-source')) el.classList.remove('is-live');
        }
      });
      if(dist<threshold){
        const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
        const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
        nearest.style.setProperty('--tx',(nx*3.6).toFixed(2)+'px');
        nearest.style.setProperty('--ty',(ny*2.6).toFixed(2)+'px');
        if(nearest.hasAttribute('data-nc-source')) nearest.classList.add('is-live');
        if(nearest.hasAttribute('data-nc-step')) nearest.classList.add('is-live');
      }
    });
    hero.addEventListener('pointerleave',resetLocal);
  }

  cycleWorkflow();
  updateMetrics();

  if(!reduce){
    setInterval(()=>{if(visible&&!document.hidden)cycleWorkflow();},1050);
    setInterval(()=>{if(visible&&!document.hidden)updateMetrics();},1650);
    let si=0;
    setInterval(()=>{
      if(!visible||document.hidden||!pulseSources.length||(fine&&hero.matches(':hover'))) return;
      pulseSources.forEach(s=>s.classList.remove('is-live'));
      pulseSources[si%pulseSources.length].classList.add('is-live');
      si++;
    },1800);
  }
})();
