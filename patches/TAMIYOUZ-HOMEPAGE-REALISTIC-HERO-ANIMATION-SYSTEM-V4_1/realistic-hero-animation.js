/* TAMIYOUZ-HOMEPAGE-REALISTIC-HERO-ANIMATION-SYSTEM-V4_1 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 [data-tyz-rh-v40]');
  if(!hero || hero.dataset.rhAnimationVersion==='41') return;

  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const mobile=window.matchMedia?.('(max-width:760px)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;

  hero.dataset.rhAnimationVersion='41';

  const kpis=[...hero.querySelectorAll('[data-rh-kpi]')];
  const screens=[...hero.querySelectorAll('[data-rh-screen]')];
  const sources=[...hero.querySelectorAll('.tyz-rh-source-list > span')];
  const steps=[...hero.querySelectorAll('[data-rh-step]')];
  const bars=[...hero.querySelectorAll('[data-rh-bars] i')];
  const main=hero.querySelector('[data-rh-screen="analytics"]');
  const kpiValues={
    traffic:{el:hero.querySelector('[data-rh-value="traffic"]'),v:58420,min:57900,max:59250,format:v=>v.toLocaleString('en-US')},
    sales:{el:hero.querySelector('[data-rh-value="sales"]'),v:230420,min:226000,max:236500,format:v=>'$'+Math.round(v).toLocaleString('en-US')},
    users:{el:hero.querySelector('[data-rh-value="users"]'),v:12430,min:12100,max:12850,format:v=>Math.round(v).toLocaleString('en-US')},
    conversion:{el:hero.querySelector('[data-rh-value="conversion"]'),v:3.24,min:3.08,max:3.46,format:v=>v.toFixed(2)+'%'}
  };
  const metrics={
    revenue:{el:hero.querySelector('[data-rh-metric="revenue"]'),v:230,min:226,max:236,format:v=>'$'+Math.round(v)+'K'},
    active:{el:hero.querySelector('[data-rh-metric="active"]'),v:12.4,min:12.1,max:12.9,format:v=>v.toFixed(1)+'K'},
    rate:{el:hero.querySelector('[data-rh-metric="rate"]'),v:3.24,min:3.08,max:3.46,format:v=>v.toFixed(2)+'%'},
    bounce:{el:hero.querySelector('[data-rh-metric="bounce"]'),v:28.1,min:26.8,max:29.4,format:v=>v.toFixed(1)+'%'}
  };
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>min+Math.random()*(max-min);

  /* Prevent decorative demo values from looking like verified live business data. */
  hero.querySelectorAll('.tyz-rh-kpi__head i').forEach(el=>el.textContent='DEMO');

  let visible=true;
  if('IntersectionObserver' in window){
    new IntersectionObserver(entries=>{visible=!!entries[0]?.isIntersecting;},{threshold:.16}).observe(hero);
  }

  const flash=(el)=>{
    if(!el) return;
    el.classList.add('is-rh-value-change');
    setTimeout(()=>el.classList.remove('is-rh-value-change'),160);
  };

  let kpiIndex=0;
  const cycleKpis=()=>{
    kpis.forEach((card,i)=>card.classList.toggle('is-rh-awake',i===kpiIndex));
    kpiIndex=(kpiIndex+1)%Math.max(kpis.length,1);
  };

  let stepIndex=0;
  const cycleWorkflow=()=>{
    steps.forEach((step,i)=>step.classList.toggle('is-rh-step-active',i===stepIndex));
    stepIndex=(stepIndex+1)%Math.max(steps.length,1);
  };

  let sourceIndex=0;
  const cycleSources=()=>{
    sources.forEach((row,i)=>row.classList.toggle('is-rh-source-active',i===sourceIndex));
    sourceIndex=(sourceIndex+1)%Math.max(sources.length,1);
  };

  const updateNumbers=()=>{
    Object.values(kpiValues).forEach(cfg=>{
      const span=(cfg.max-cfg.min)*.018;
      cfg.v=clamp(cfg.v+rand(-span,span*1.35),cfg.min,cfg.max);
      if(cfg.el){
        flash(cfg.el);
        cfg.el.textContent=cfg.format(cfg.v);
      }
    });

    Object.values(metrics).forEach(cfg=>{
      const span=(cfg.max-cfg.min)*.025;
      cfg.v=clamp(cfg.v+rand(-span,span*1.25),cfg.min,cfg.max);
      if(cfg.el){
        flash(cfg.el);
        cfg.el.textContent=cfg.format(cfg.v);
      }
    });

    bars.forEach((bar,i)=>{
      const base=30+((i*11)%44);
      bar.style.setProperty('--h',clamp(base+rand(-10,18),22,92).toFixed(0)+'%');
    });

    if(main){
      main.classList.remove('is-rh-cycle');
      void main.offsetWidth;
      main.classList.add('is-rh-cycle');
    }
  };

  if(fine && !mobile && !reduce){
    screens.forEach(screen=>{
      screen.addEventListener('pointermove',e=>{
        const r=screen.getBoundingClientRect();
        const x=clamp(((e.clientX-r.left)/r.width)*100,0,100);
        const y=clamp(((e.clientY-r.top)/r.height)*100,0,100);
        screen.style.setProperty('--rh-local-x',x.toFixed(1)+'%');
        screen.style.setProperty('--rh-local-y',y.toFixed(1)+'%');
        screen.classList.add('is-rh-screen-active');
      });
      screen.addEventListener('pointerleave',()=>screen.classList.remove('is-rh-screen-active'));
    });

    kpis.forEach(card=>{
      card.style.pointerEvents='auto';
      card.addEventListener('pointerenter',()=>card.classList.add('is-rh-hover'));
      card.addEventListener('pointerleave',()=>card.classList.remove('is-rh-hover'));
    });
  }

  cycleKpis();
  cycleWorkflow();
  cycleSources();

  if(!reduce && !mobile){
    setInterval(()=>{if(visible&&!document.hidden)cycleKpis();},1300);
    setInterval(()=>{if(visible&&!document.hidden)cycleWorkflow();},1150);
    setInterval(()=>{if(visible&&!document.hidden)cycleSources();},1450);
    setInterval(()=>{if(visible&&!document.hidden)updateNumbers();},2300);
  }
})();
