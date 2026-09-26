/* TAMIYOUZ-HOMEPAGE-NATIVE-HERO-REBUILD-V3_1_11 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 [data-tyz-native-hero]');
  if(!hero || hero.dataset.tyzNativeHeroReady==='3111') return;
  hero.dataset.tyzNativeHeroReady='3111';

  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const workflow=[...hero.querySelectorAll('[data-native-step]')];
  const links=[...hero.querySelectorAll('.tyz-native-wf-link')];
  const sources=[...hero.querySelectorAll('[data-native-source]')];
  const dashboard=hero.querySelector('[data-native-dashboard]');
  const sidepanel=hero.querySelector('[data-native-sidepanel]');
  const status=hero.querySelector('[data-native-status]');
  const bars=[...hero.querySelectorAll('[data-native-bars] i')];
  const activity=[...hero.querySelectorAll('[data-native-activity] span b')];
  const meters={
    health:hero.querySelector('[data-native-meter="health"]'),
    flow:hero.querySelector('[data-native-meter="flow"]'),
    signal:hero.querySelector('[data-native-meter="signal"]')
  };
  const values={
    health:{el:hero.querySelector('[data-native-metric="health"]'),v:84,min:82,max:97,suffix:'%'},
    flow:{el:hero.querySelector('[data-native-metric="flow"]'),v:6,min:4,max:9,pad:2},
    signal:{el:hero.querySelector('[data-native-metric="signal"]'),v:18,min:14,max:29,pad:2}
  };
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));

  let inView=true;
  if('IntersectionObserver' in window){
    new IntersectionObserver(entries=>{inView=!!entries[0]?.isIntersecting;},{threshold:.18}).observe(hero);
  }

  let wfIndex=0;
  const runWorkflow=()=>{
    workflow.forEach((card,i)=>card.classList.toggle('is-live',i===wfIndex));
    links.forEach((link,i)=>link.classList.toggle('is-live',i===wfIndex-1));
    wfIndex=(wfIndex+1)%workflow.length;
  };

  const updateMetrics=()=>{
    Object.entries(values).forEach(([key,cfg])=>{
      cfg.v=clamp(cfg.v+rand(-1,2),cfg.min,cfg.max);
      if(cfg.el){
        let t=String(cfg.v);
        if(cfg.pad) t=t.padStart(cfg.pad,'0');
        cfg.el.textContent=t+(cfg.suffix||'');
      }
      const meter=meters[key];
      if(meter){
        const pct=key==='health'?cfg.v:Math.round(42+((cfg.v-cfg.min)/(cfg.max-cfg.min))*44);
        meter.style.setProperty('--w',clamp(pct,24,98)+'%');
      }
    });
    bars.forEach((bar,i)=>{
      const bias=i>4?8:0;
      bar.style.setProperty('--h',clamp(rand(24,84)+bias,22,96)+'%');
    });
    activity.forEach((row)=>row.style.setProperty('--w',rand(28,92)+'%'));
    if(status) status.textContent=status.textContent==='LIVE'?'SYNCING':'LIVE';
  };

  const wakeSource=(target)=>{
    sources.forEach(s=>s.classList.toggle('is-live',s===target));
  };

  if(fine && !reduce){
    hero.addEventListener('pointermove',(e)=>{
      const hr=hero.getBoundingClientRect();
      if(!hr.width||!hr.height) return;

      let nearest=null,dist=Infinity;
      [...workflow,...sources].forEach(el=>{
        const r=el.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=el;}
      });

      [...workflow,...sources].forEach(el=>{
        const r=el.getBoundingClientRect();
        const threshold=Math.max(r.width,r.height)*.95;
        if(el===nearest && dist<threshold){
          const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
          const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
          el.style.setProperty('--tx',(nx*4).toFixed(2)+'px');
          el.style.setProperty('--ty',(ny*3).toFixed(2)+'px');
          if(el.hasAttribute('data-native-source')) wakeSource(el);
          if(el.hasAttribute('data-native-step')) el.classList.add('is-live');
        }else{
          el.style.setProperty('--tx','0px');
          el.style.setProperty('--ty','0px');
        }
      });

      if(dashboard){
        const r=dashboard.getBoundingClientRect();
        const inside=e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom;
        dashboard.classList.toggle('is-active',inside);
        if(inside){
          const nx=((e.clientX-r.left)/r.width-.5)*2;
          const ny=((e.clientY-r.top)/r.height-.5)*2;
          dashboard.style.setProperty('--dash-x',(nx*1.8).toFixed(2)+'px');
          dashboard.style.setProperty('--dash-y',(ny*1.4).toFixed(2)+'px');
        }else{
          dashboard.style.setProperty('--dash-x','0px');
          dashboard.style.setProperty('--dash-y','0px');
        }
      }
    });
    hero.addEventListener('pointerleave',()=>{
      [...workflow,...sources].forEach(el=>{
        el.style.setProperty('--tx','0px');
        el.style.setProperty('--ty','0px');
      });
      sources.forEach(s=>s.classList.remove('is-live'));
      dashboard?.classList.remove('is-active');
      dashboard?.style.setProperty('--dash-x','0px');
      dashboard?.style.setProperty('--dash-y','0px');
    });
  }

  runWorkflow();
  updateMetrics();

  if(!reduce){
    setInterval(()=>{
      if(!inView||document.hidden) return;
      runWorkflow();
    },1050);

    setInterval(()=>{
      if(!inView||document.hidden) return;
      updateMetrics();
    },1650);

    let sourceIndex=0;
    const activeSources=sources.filter(s=>s.classList.contains('tyz-native-source--active'));
    setInterval(()=>{
      if(!inView||document.hidden||!activeSources.length) return;
      if(fine&&hero.matches(':hover')) return;
      activeSources.forEach(s=>s.classList.remove('is-live'));
      activeSources[sourceIndex%activeSources.length].classList.add('is-live');
      sourceIndex++;
    },1850);
  }
})();
