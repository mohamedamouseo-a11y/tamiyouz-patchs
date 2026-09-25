/* TAMIYOUZ-HOMEPAGE-LIVING-CARDS-V3_1_7 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 .tyz-hero__media');
  if(!hero || hero.dataset.tyzLivingCards==='317') return;
  const img=hero.querySelector('.tyz-hero-media__image');
  if(!img) return;

  hero.dataset.tyzLivingCards='317';
  hero.classList.add('tyz-living-cards-v317');
  hero.querySelectorAll(':scope > .tyz-living-cards').forEach((el)=>el.remove());

  const layer=document.createElement('div');
  layer.className='tyz-living-cards';
  layer.setAttribute('aria-hidden','true');
  layer.innerHTML=`
    <div class="tyz-living-card tyz-living-card--leads" data-card="leads">
      <div class="tyz-living-card__inner">
        <div class="tyz-living-card__head"><span>NEW LEADS</span><b>DEMO</b></div>
        <div class="tyz-living-card__value"><strong data-value>18</strong><small>ACTIVE</small></div>
        <span class="tyz-living-card__meter"><i data-meter style="--meter:58%"></i></span>
      </div>
    </div>
    <div class="tyz-living-card tyz-living-card--follow" data-card="follow">
      <div class="tyz-living-card__inner">
        <div class="tyz-living-card__head"><span>FOLLOW UP</span><b>DEMO</b></div>
        <div class="tyz-living-card__value"><strong data-value>07</strong><small>QUEUE</small></div>
        <span class="tyz-living-card__meter"><i data-meter style="--meter:46%"></i></span>
      </div>
    </div>
    <div class="tyz-living-card tyz-living-card--instagram" data-card="instagram">
      <div class="tyz-living-card__inner">
        <div class="tyz-living-card__social">
          <span class="tyz-living-card__ring"><span>IG</span></span>
          <strong>Instagram</strong>
          <small><i class="tyz-living-card__dot"></i><span data-social>LIVE</span></small>
        </div>
      </div>
    </div>
    <div class="tyz-living-card tyz-living-card--tiktok" data-card="tiktok">
      <div class="tyz-living-card__inner">
        <div class="tyz-living-card__social">
          <span class="tyz-living-card__ring"><span>TT</span></span>
          <strong>TikTok</strong>
          <small><i class="tyz-living-card__dot"></i><span data-social>SYNC</span></small>
        </div>
      </div>
    </div>`;
  hero.appendChild(layer);

  const cards=[...layer.querySelectorAll('[data-card]')];
  const byName=Object.fromEntries(cards.map((c)=>[c.dataset.card,c]));
  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));

  const state={
    leads:{value:18,min:16,max:29},
    follow:{value:7,min:5,max:16}
  };

  const tick=(name,boost=false)=>{
    const card=byName[name];
    const cfg=state[name];
    if(!card || !cfg) return;
    const delta=boost ? rand(1,2) : rand(-1,2);
    cfg.value=clamp(cfg.value+delta,cfg.min,cfg.max);
    const value=card.querySelector('[data-value]');
    const meter=card.querySelector('[data-meter]');
    if(value) value.textContent=name==='follow'?String(cfg.value).padStart(2,'0'):String(cfg.value);
    if(meter){
      const pct=Math.round(38+((cfg.value-cfg.min)/(cfg.max-cfg.min))*48);
      meter.style.setProperty('--meter',clamp(pct,34,90)+'%');
    }
    card.classList.remove('is-ticking');
    void card.offsetWidth;
    card.classList.add('is-ticking');
    window.setTimeout(()=>card.classList.remove('is-ticking'),460);
  };

  const updateSocial=(name)=>{
    const card=byName[name];
    const label=card?.querySelector('[data-social]');
    if(!card || !label) return;
    const choices=name==='instagram'?['LIVE','ACTIVE','LIVE']:['SYNC','LIVE','SYNC'];
    label.textContent=choices[rand(0,choices.length-1)];
    card.classList.add('is-awake');
    window.setTimeout(()=>card.classList.remove('is-awake'),900);
  };

  const wakeOnly=(card)=>{
    cards.forEach((c)=>c.classList.toggle('is-active',c===card));
    if(!card) return;
    const name=card.dataset.card;
    if(name==='leads' || name==='follow') tick(name,true);
    else updateSocial(name);
  };
  const sleep=()=>cards.forEach((c)=>c.classList.remove('is-active'));

  if(fine && !reduce){
    hero.addEventListener('pointermove',(e)=>{
      const hr=hero.getBoundingClientRect();
      if(!hr.width || !hr.height) return;
      let nearest=null;
      let nearestDist=Infinity;
      cards.forEach((card)=>{
        const r=card.getBoundingClientRect();
        const cx=r.left+r.width/2;
        const cy=r.top+r.height/2;
        const d=Math.hypot(e.clientX-cx,e.clientY-cy);
        if(d<nearestDist){nearestDist=d;nearest=card;}
      });

      const threshold=Math.min(hr.width,hr.height)*.17;
      if(!nearest || nearestDist>threshold){
        sleep();
        cards.forEach((c)=>{c.style.setProperty('--dx','0px');c.style.setProperty('--dy','0px');});
        return;
      }

      if(!nearest.classList.contains('is-active')) wakeOnly(nearest);
      const r=nearest.getBoundingClientRect();
      const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
      const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
      nearest.style.setProperty('--dx',(nx*4.2).toFixed(2)+'px');
      nearest.style.setProperty('--dy',(ny*3.0).toFixed(2)+'px');
      cards.filter((c)=>c!==nearest).forEach((c)=>{
        c.style.setProperty('--dx','0px');
        c.style.setProperty('--dy','0px');
      });
    });
    hero.addEventListener('pointerleave',()=>{
      sleep();
      cards.forEach((c)=>{c.style.setProperty('--dx','0px');c.style.setProperty('--dy','0px');});
    });
  }

  let inView=true;
  if('IntersectionObserver' in window){
    const io=new IntersectionObserver((entries)=>{inView=!!entries[0]?.isIntersecting;},{threshold:.18});
    io.observe(hero);
  }

  if(!reduce){
    let phase=0;
    const order=['leads','instagram','follow','tiktok'];
    window.setInterval(()=>{
      if(!inView || document.hidden) return;
      const name=order[phase%order.length];
      phase++;
      const card=byName[name];
      if(!fine || !hero.matches(':hover')){
        cards.forEach((c)=>c.classList.remove('is-awake'));
        card?.classList.add('is-awake');
        window.setTimeout(()=>card?.classList.remove('is-awake'),920);
      }
      if(name==='leads' || name==='follow') tick(name,phase%3===0);
      else updateSocial(name);
    },1850);
  }
})();
