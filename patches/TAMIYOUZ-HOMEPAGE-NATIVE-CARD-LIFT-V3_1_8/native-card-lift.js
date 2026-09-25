/* TAMIYOUZ-HOMEPAGE-NATIVE-CARD-LIFT-V3_1_8 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 .tyz-hero__media');
  if(!hero || hero.dataset.tyzNativeCards==='318') return;
  const baseImg=hero.querySelector('.tyz-hero-media__image');
  if(!baseImg) return;

  hero.dataset.tyzNativeCards='318';
  hero.classList.add('tyz-native-cards-v318');

  // V3.1.8 supersedes V3.1.7 card layer only.
  hero.querySelectorAll(':scope > .tyz-living-cards,:scope > .tyz-native-cards').forEach((el)=>el.remove());

  const src=baseImg.currentSrc || baseImg.src;
  const defs={
    leads:{cls:'leads',l:6.2,t:5.4,w:18.5,h:19.2,value:29,min:24,max:39,suffix:''},
    follow:{cls:'follow',l:77.2,t:5.4,w:18.5,h:19.2,value:16,min:11,max:25,suffix:''},
    tiktok:{cls:'tiktok',l:.2,t:25.0,w:10.1,h:12.3},
    instagram:{cls:'instagram',l:.2,t:39.0,w:10.1,h:12.3}
  };

  const layer=document.createElement('div');
  layer.className='tyz-native-cards';
  layer.setAttribute('aria-hidden','true');

  const cloneMarkup=(name,d)=>{
    const iw=(100/d.w).toFixed(5);
    const ih=(100/d.h).toFixed(5);
    const il=(-(d.l/d.w)*100).toFixed(5);
    const it=(-(d.t/d.h)*100).toFixed(5);
    const metric=(name==='leads'||name==='follow')
      ? '<div class="tyz-native-card__readout"><strong data-value>'+String(d.value).padStart(name==='follow'?2:1,'0')+'</strong><small>DEMO</small></div>'+
        '<div class="tyz-native-card__microline"><svg viewBox="0 0 100 24" preserveAspectRatio="none"><polyline points="0,21 18,18 33,19 48,13 63,14 78,8 100,4"></polyline></svg></div>'
      : '';
    return '<div class="tyz-native-card tyz-native-card--'+d.cls+'" data-native-card="'+name+'">'+
      '<div class="tyz-native-card__clip">'+
        '<img class="tyz-native-card__img" src="'+src.replace(/"/g,'&quot;')+'" alt="" style="width:'+iw+'00%;height:'+ih+'00%;left:'+il+'%;top:'+it+'%;">'+
        '<span class="tyz-native-card__shine"></span>'+
        '<span class="tyz-native-card__pulse"></span>'+
        metric+
      '</div>'+
    '</div>';
  };

  layer.innerHTML=Object.entries(defs).map(([n,d])=>cloneMarkup(n,d)).join('');
  hero.appendChild(layer);

  const cards=[...layer.querySelectorAll('[data-native-card]')];
  const byName=Object.fromEntries(cards.map(c=>[c.dataset.nativeCard,c]));
  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));

  const tick=(name,boost=false)=>{
    const d=defs[name];
    const card=byName[name];
    if(!d || !card || typeof d.value!=='number') return;
    d.value=clamp(d.value+rand(boost?1:-1,boost?3:2),d.min,d.max);
    const el=card.querySelector('[data-value]');
    if(el) el.textContent=name==='follow'?String(d.value).padStart(2,'0'):String(d.value);
    card.classList.remove('is-ticking');
    void card.offsetWidth;
    card.classList.add('is-ticking');
    window.setTimeout(()=>card.classList.remove('is-ticking'),470);
  };

  const wake=(card,strong=false)=>{
    cards.forEach(c=>{
      c.classList.toggle('is-active',strong && c===card);
      if(c!==card) c.classList.remove('is-awake');
    });
    if(!card) return;
    if(!strong) card.classList.add('is-awake');
    const n=card.dataset.nativeCard;
    if(n==='leads'||n==='follow') tick(n,strong);
  };

  const resetOffsets=()=>{
    cards.forEach(c=>{
      c.style.setProperty('--dx','0px');
      c.style.setProperty('--dy','0px');
      c.classList.remove('is-active');
    });
  };

  if(fine && !reduce){
    hero.addEventListener('pointermove',(e)=>{
      let nearest=null,dist=Infinity;
      cards.forEach(card=>{
        const r=card.getBoundingClientRect();
        const d=Math.hypot(e.clientX-(r.left+r.width/2),e.clientY-(r.top+r.height/2));
        if(d<dist){dist=d;nearest=card;}
      });
      if(!nearest){resetOffsets();return;}
      const r=nearest.getBoundingClientRect();
      const threshold=Math.max(r.width,r.height)*1.05;
      if(dist>threshold){resetOffsets();return;}

      if(!nearest.classList.contains('is-active')) wake(nearest,true);

      const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
      const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
      nearest.style.setProperty('--dx',(nx*5.2).toFixed(2)+'px');
      nearest.style.setProperty('--dy',(ny*3.8).toFixed(2)+'px');
      cards.filter(c=>c!==nearest).forEach(c=>{
        c.style.setProperty('--dx','0px');
        c.style.setProperty('--dy','0px');
        c.classList.remove('is-active');
      });
    });
    hero.addEventListener('pointerleave',resetOffsets);
  }

  let inView=true;
  if('IntersectionObserver' in window){
    const io=new IntersectionObserver((entries)=>{inView=!!entries[0]?.isIntersecting;},{threshold:.18});
    io.observe(hero);
  }

  if(!reduce){
    const order=['leads','tiktok','follow','instagram'];
    let phase=0;
    window.setInterval(()=>{
      if(!inView || document.hidden) return;
      if(fine && hero.matches(':hover')) return;
      const name=order[phase%order.length];
      phase++;
      const card=byName[name];
      cards.forEach(c=>c.classList.remove('is-awake'));
      if(card){
        wake(card,false);
        window.setTimeout(()=>card.classList.remove('is-awake'),900);
      }
    },1750);
  }
})();
