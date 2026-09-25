/* TAMIYOUZ-HOMEPAGE-LIVE-COMMAND-CENTER-V3_1_6 */
(()=> {
  const hero=document.querySelector('#tyz-home-v21 .tyz-hero__media');
  if(!hero || hero.dataset.tyzLiveCommand==='316') return;
  const img=hero.querySelector('.tyz-hero-media__image');
  if(!img) return;

  hero.dataset.tyzLiveCommand='316';
  hero.classList.add('tyz-live-command-v316');

  // V3.1.6 supersedes the visible V3.1.5 effect layer. Keep the approved image,
  // but remove floating telemetry/lens UI and neutralize whole-image tilt via CSS.
  hero.querySelectorAll('.tyz-command-hero__fx,.tyz-live-command').forEach((el)=>el.remove());

  const layer=document.createElement('div');
  layer.className='tyz-live-command';
  layer.setAttribute('aria-hidden','true');
  layer.innerHTML=`
    <div class="tyz-live-command__screen tyz-live-command__screen--main" data-zone="main">
      <div class="tyz-live-command__scan"></div>
      <div class="tyz-live-command__counter tyz-live-command__counter--a"><strong data-counter="health">84</strong><small>%</small></div>
      <div class="tyz-live-command__counter tyz-live-command__counter--b"><strong data-counter="flow">06</strong><small>FLOW</small></div>
      <div class="tyz-live-command__counter tyz-live-command__counter--c"><strong data-counter="signal">18</strong><small>AI</small></div>
      <div class="tyz-live-command__bars">${'<i></i>'.repeat(7)}</div>
      <div class="tyz-live-command__trend">
        <svg viewBox="0 0 100 40" preserveAspectRatio="none" focusable="false">
          <polyline points="0,34 16,31 32,29 48,24 64,22 80,15 100,9"></polyline>
          <circle cx="100" cy="9" r="1.7"></circle>
        </svg>
      </div>
      <span class="tyz-live-command__demo">DEMO</span>
    </div>
    <div class="tyz-live-command__screen tyz-live-command__screen--right" data-zone="right">
      <div class="tyz-live-command__scan"></div>
      <div class="tyz-live-command__status"><i></i><span>SYNCING</span></div>
      <div class="tyz-live-command__activity">
        <b></b><b></b><b></b><b></b><b></b>
      </div>
    </div>
    <div class="tyz-live-command__screen tyz-live-command__screen--top" data-zone="top">
      <i class="tyz-live-command__top-pulse"></i>
      <i class="tyz-live-command__top-pulse"></i>
      <i class="tyz-live-command__top-pulse"></i>
      <i class="tyz-live-command__top-pulse"></i>
      <i class="tyz-live-command__top-pulse"></i>
    </div>`;
  hero.appendChild(layer);

  const reduce=window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const fine=window.matchMedia?.('(hover:hover) and (pointer:fine)').matches;
  const screens=[...layer.querySelectorAll('[data-zone]')];
  const main=layer.querySelector('[data-zone="main"]');
  const right=layer.querySelector('[data-zone="right"]');
  const status=right?.querySelector('.tyz-live-command__status');
  const statusText=status?.querySelector('span');
  const bars=[...(main?.querySelectorAll('.tyz-live-command__bars i')||[])];
  const activity=[...(right?.querySelectorAll('.tyz-live-command__activity b')||[])];
  const counterEls={
    health:main?.querySelector('[data-counter="health"]'),
    flow:main?.querySelector('[data-counter="flow"]'),
    signal:main?.querySelector('[data-counter="signal"]')
  };

  const clamp=(v,min,max)=>Math.max(min,Math.min(max,v));
  const rand=(min,max)=>Math.round(min+Math.random()*(max-min));
  const pad2=(v)=>String(v).padStart(2,'0');

  let values={health:84,flow:6,signal:18};
  const updateCounters=(boost=false)=>{
    values.health=clamp(values.health+rand(boost?1:0,boost?3:2)-1,82,97);
    values.flow=clamp(values.flow+rand(0,2)-1,4,9);
    values.signal=clamp(values.signal+rand(0,3)-1,14,29);
    if(counterEls.health) counterEls.health.textContent=String(values.health);
    if(counterEls.flow) counterEls.flow.textContent=pad2(values.flow);
    if(counterEls.signal) counterEls.signal.textContent=pad2(values.signal);
  };

  const updateBars=(boost=false)=>{
    bars.forEach((bar,i)=>{
      const floor=boost ? 38 : 26;
      const ceiling=boost ? 94 : 82;
      const bias=i>3 ? 8 : 0;
      bar.style.setProperty('--h',clamp(rand(floor,ceiling)+bias,22,98)+'%');
    });
  };

  const updateActivity=()=>{
    activity.forEach((item)=>item.style.setProperty('--w',(rand(35,100)/100).toFixed(2)));
  };

  let live=false;
  const updateStatus=()=>{
    if(!status || !statusText) return;
    live=!live;
    status.classList.toggle('is-live',live);
    statusText.textContent=live?'LIVE':'SYNCING';
  };

  const wake=(zone)=>{
    screens.forEach((s)=>s.classList.toggle('is-active',s===zone));
    if(zone===main){
      updateCounters(true);
      updateBars(true);
    } else if(zone===right){
      updateStatus();
      updateActivity();
    }
  };
  const sleep=()=>screens.forEach((s)=>s.classList.remove('is-active'));

  if(fine && !reduce){
    layer.addEventListener('pointermove',(e)=>{
      const hr=hero.getBoundingClientRect();
      if(!hr.width || !hr.height) return;
      const px=(e.clientX-hr.left)/hr.width;
      const py=(e.clientY-hr.top)/hr.height;

      let nearest=null;
      let nearestDist=Infinity;
      screens.forEach((screen)=>{
        const r=screen.getBoundingClientRect();
        const cx=(r.left+r.width/2-hr.left)/hr.width;
        const cy=(r.top+r.height/2-hr.top)/hr.height;
        const d=Math.hypot(px-cx,py-cy);
        if(d<nearestDist){ nearestDist=d; nearest=screen; }
      });
      if(!nearest || nearestDist>.28){ sleep(); return; }

      wake(nearest);

      const r=nearest.getBoundingClientRect();
      const nx=clamp((e.clientX-r.left)/r.width,0,1)-.5;
      const ny=clamp((e.clientY-r.top)/r.height,0,1)-.5;
      nearest.style.transform=`translate3d(${(nx*3.2).toFixed(2)}px,${(ny*2.4).toFixed(2)}px,0)`;
    });
    layer.addEventListener('pointerleave',()=>{
      sleep();
      screens.forEach((s)=>s.style.transform='translate3d(0,0,0)');
    });
  }

  let inView=true;
  if('IntersectionObserver' in window){
    const io=new IntersectionObserver((entries)=>{
      inView=!!entries[0]?.isIntersecting;
    },{threshold:.18});
    io.observe(hero);
  }

  updateCounters();
  updateBars();
  updateActivity();

  if(!reduce){
    let phase=0;
    window.setInterval(()=>{
      if(!inView || document.hidden) return;
      phase=(phase+1)%4;
      updateCounters(phase===2);
      updateBars(phase===2);
      updateActivity();
      if(phase===0 || phase===2) updateStatus();

      // Idle cinematic focus: wake one dashboard zone at a time only when
      // the pointer is not actively controlling the scene.
      if(!fine || !layer.matches(':hover')){
        const target=phase===0?main:phase===2?right:null;
        sleep();
        if(target){
          target.classList.add('is-active');
          window.setTimeout(()=>target.classList.remove('is-active'),950);
        }
      }
    },2300);
  }
})();
