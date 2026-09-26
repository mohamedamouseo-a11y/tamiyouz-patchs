/* TAMIYOUZ-HOMEPAGE-PREMIUM-NATIVE-WORKSTATION-V3_1_14 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 [data-tyz-premium-workstation-v3114]');
  if(!hero || hero.dataset.pwReady==='3114') return;
  hero.dataset.pwReady='3114';

  hero.querySelectorAll('.tyz-command-hero__fx,.tyz-live-command,.tyz-living-cards,.tyz-native-cards,.tyz-inframe-cards,.tyz-workflow-signal,.tyz-native-hero__scene,.tyz-nc-scene,.tyz-ns-stage').forEach(el=>el.remove());

  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const steps=[...hero.querySelectorAll('[data-pw-step]')];
  const links=[...hero.querySelectorAll('.tyz-pw-link')];
  const channels=[...hero.querySelectorAll('[data-pw-channel]')];
  const pulseChannels=channels.filter(c=>c.classList.contains('tyz-pw-channel--pulse'));
  const main=hero.querySelector('[data-pw-main]');
  const status=hero.querySelector('[data-pw-status]');
  const bars=[...hero.querySelectorAll('[data-pw-bars] i')];
  const activity=[...hero.querySelectorAll('[data-pw-activity] span b')];
  const cfg={
    health:{el:hero.querySelector('[data-pw-metric="health"]'),meter:hero.querySelector('[data-pw-meter="health"]'),v:91,min:85,max:98,suffix:'%'},
    flow:{el:hero.querySelector('[data-pw-metric="flow"]'),meter:hero.querySelector('[data-pw-meter="flow"]'),v:6,min:4,max:9,pad:2},
    signal:{el:hero.querySelector('[data-pw-metric="signal"]'),meter:hero.querySelector('[data-pw-meter="signal"]'),v:19,min:14,max:29,pad:2}
  };
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));

  let visible=true;
  if('IntersectionObserver' in window){
    new IntersectionObserver(entries=>{visible=!!entries[0]?.isIntersecting;},{threshold:.18}).observe(hero);
  }

  let stepIndex=0;
  const cycleFlow=()=>{
    steps.forEach((step,i)=>step.classList.toggle('is-live',i===stepIndex));
    links.forEach((link,i)=>link.classList.toggle('is-live',i===stepIndex-1));
    stepIndex=(stepIndex+1)%steps.length;
  };

  const updateTelemetry=()=>{
    Object.entries(cfg).forEach(([key,item])=>{
      item.v=clamp(item.v+rand(-1,2),item.min,item.max);
      if(item.el){
        let value=String(item.v);
        if(item.pad)value=value.padStart(item.pad,'0');
        item.el.textContent=value+(item.suffix||'');
      }
      if(item.meter){
        const pct=key==='health'?item.v:Math.round(42+((item.v-item.min)/(item.max-item.min))*44);
        item.meter.style.setProperty('--w',clamp(pct,30,98)+'%');
      }
    });
    bars.forEach((bar,i)=>bar.style.setProperty('--h',clamp(rand(28,86)+(i>4?5:0),24,95)+'%'));
    activity.forEach(row=>row.style.setProperty('--w',rand(30,90)+'%'));
    if(status)status.textContent=status.textContent==='LIVE'?'SYNCING':'LIVE';
  };

  const resetPointer=()=>{
    [...steps,...channels].forEach(el=>{
      el.style.setProperty('--tx','0px');
      el.style.setProperty('--ty','0px');
    });
    channels.forEach(c=>c.classList.remove('is-live'));
    if(main){
      main.style.setProperty('--mx','0px');
      main.style.setProperty('--my','0px');
    }
  };

  if(fine&&!reduce){
    hero.addEventListener('pointermove',e=>{
      let nearest=null,dist=Infinity;
      [...steps,...channels].forEach(el=>{
        const r=el.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=el;}
      });

      [...steps,...channels].forEach(el=>{
        if(el!==nearest){
          el.style.setProperty('--tx','0px');
          el.style.setProperty('--ty','0px');
          if(el.hasAttribute('data-pw-channel'))el.classList.remove('is-live');
        }
      });

      if(nearest){
        const r=nearest.getBoundingClientRect();
        if(dist<Math.max(r.width,r.height)){
          const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
          const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
          nearest.style.setProperty('--tx',(nx*3).toFixed(2)+'px');
          nearest.style.setProperty('--ty',(ny*2.2).toFixed(2)+'px');
          if(nearest.hasAttribute('data-pw-channel'))nearest.classList.add('is-live');
          if(nearest.hasAttribute('data-pw-step'))nearest.classList.add('is-live');
        }
      }

      if(main){
        const r=main.getBoundingClientRect();
        const inside=e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom;
        if(inside){
          const nx=((e.clientX-r.left)/r.width-.5)*2;
          const ny=((e.clientY-r.top)/r.height-.5)*2;
          main.style.setProperty('--mx',(nx*.9).toFixed(2)+'px');
          main.style.setProperty('--my',(ny*.7).toFixed(2)+'px');
        }else{
          main.style.setProperty('--mx','0px');
          main.style.setProperty('--my','0px');
        }
      }
    });
    hero.addEventListener('pointerleave',resetPointer);
  }

  cycleFlow();
  updateTelemetry();

  if(!reduce){
    setInterval(()=>{if(visible&&!document.hidden)cycleFlow();},1050);
    setInterval(()=>{if(visible&&!document.hidden)updateTelemetry();},1650);

    let ci=0;
    setInterval(()=>{
      if(!visible||document.hidden||!pulseChannels.length||(fine&&hero.matches(':hover')))return;
      pulseChannels.forEach(c=>c.classList.remove('is-live'));
      pulseChannels[ci%pulseChannels.length].classList.add('is-live');
      ci++;
    },1850);
  }
})();
