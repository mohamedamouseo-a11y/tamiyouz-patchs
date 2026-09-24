/* TAMIYOUZ-HOMEPAGE-LUXURY-DUAL-THEME-V1 */
(() => {
  const root = document.documentElement;
  const app = document.getElementById('tyz-home-v1');
  if (!app) return;

  const header = app.querySelector('[data-tyz-header]');
  const menuButton = app.querySelector('[data-tyz-menu]');
  const nav = app.querySelector('[data-tyz-nav]');
  const themeButton = app.querySelector('[data-tyz-theme-toggle]');
  const themeMeta = document.querySelector('meta[name="theme-color"]');

  const setTheme = (theme, persist = true) => {
    const safeTheme = theme === 'dark' ? 'dark' : 'light';
    root.setAttribute('data-tyz-theme', safeTheme);

    if (themeMeta) {
      themeMeta.setAttribute('content', safeTheme === 'dark' ? '#0c0c0e' : '#f7f4ed');
    }

    if (themeButton) {
      themeButton.setAttribute(
        'aria-label',
        safeTheme === 'dark' ? 'التبديل إلى الوضع الفاتح' : 'التبديل إلى الوضع الداكن'
      );
    }

    if (persist) {
      try {
        localStorage.setItem('tamiyouz-theme', safeTheme);
      } catch (e) {}
    }

    window.dispatchEvent(new CustomEvent('tamiyouz:theme', { detail: safeTheme }));
  };

  const initialTheme = root.getAttribute('data-tyz-theme') === 'dark' ? 'dark' : 'light';
  setTheme(initialTheme, false);

  if (themeButton) {
    themeButton.addEventListener('click', () => {
      setTheme(root.getAttribute('data-tyz-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  const updateHeader = () => {
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 16);
  };

  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  if (menuButton && nav) {
    menuButton.addEventListener('click', () => {
      const open = !nav.classList.contains('is-open');
      nav.classList.toggle('is-open', open);
      menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    nav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        nav.classList.remove('is-open');
        menuButton.setAttribute('aria-expanded', 'false');
      });
    });
  }

  const revealItems = [...app.querySelectorAll('.tyz-reveal')];
  if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    revealItems.forEach((item, index) => {
      item.style.transitionDelay = Math.min(index % 4, 3) * 60 + 'ms';
      observer.observe(item);
    });
  } else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  }

  const canvas = document.getElementById('tyz-ai-canvas');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let width = 0;
  let height = 0;
  let dpr = 1;
  let raf = null;
  let points = [];

  const palette = () => {
    const dark = root.getAttribute('data-tyz-theme') === 'dark';
    return {
      node: dark ? 'rgba(226,184,104,.72)' : 'rgba(233,196,122,.78)',
      line: dark ? 'rgba(220,178,96,.17)' : 'rgba(235,194,117,.19)',
      soft: dark ? 'rgba(255,255,255,.06)' : 'rgba(255,255,255,.09)'
    };
  };

  const buildPoints = () => {
    const count = Math.max(34, Math.min(76, Math.floor((width * height) / 11000)));
    points = Array.from({ length: count }, () => ({
      x: Math.random() * width,
      y: Math.random() * height,
      vx: (Math.random() - 0.5) * 0.18,
      vy: (Math.random() - 0.5) * 0.18,
      r: Math.random() * 1.4 + 0.6
    }));
  };

  const resize = () => {
    const rect = canvas.getBoundingClientRect();
    width = Math.max(1, rect.width);
    height = Math.max(1, rect.height);
    dpr = Math.min(window.devicePixelRatio || 1, 2);

    canvas.width = Math.round(width * dpr);
    canvas.height = Math.round(height * dpr);
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    buildPoints();
  };

  const draw = () => {
    const colors = palette();
    ctx.clearRect(0, 0, width, height);

    const glow = ctx.createRadialGradient(
      width * 0.34,
      height * 0.46,
      0,
      width * 0.34,
      height * 0.46,
      Math.max(width, height) * 0.62
    );
    glow.addColorStop(0, 'rgba(205,157,78,.24)');
    glow.addColorStop(0.42, 'rgba(152,105,42,.08)');
    glow.addColorStop(1, 'rgba(0,0,0,0)');
    ctx.fillStyle = glow;
    ctx.fillRect(0, 0, width, height);

    points.forEach((p) => {
      if (!reducedMotion) {
        p.x += p.vx;
        p.y += p.vy;
        if (p.x < -10 || p.x > width + 10) p.vx *= -1;
        if (p.y < -10 || p.y > height + 10) p.vy *= -1;
      }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = colors.node;
      ctx.fill();
    });

    for (let i = 0; i < points.length; i += 1) {
      for (let j = i + 1; j < points.length; j += 1) {
        const a = points[i];
        const b = points[j];
        const dx = a.x - b.x;
        const dy = a.y - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < 112) {
          ctx.beginPath();
          ctx.moveTo(a.x, a.y);
          ctx.lineTo(b.x, b.y);
          ctx.strokeStyle = colors.line;
          ctx.lineWidth = 1 - dist / 150;
          ctx.stroke();
        }
      }
    }

    ctx.beginPath();
    ctx.arc(width * 0.63, height * 0.35, Math.min(width, height) * 0.15, 0, Math.PI * 2);
    ctx.strokeStyle = colors.soft;
    ctx.lineWidth = 1;
    ctx.stroke();

    if (!reducedMotion) {
      raf = requestAnimationFrame(draw);
    }
  };

  const onResize = () => {
    if (raf) cancelAnimationFrame(raf);
    resize();
    draw();
  };

  resize();
  draw();
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('tamiyouz:theme', () => {
    if (reducedMotion) draw();
  });
})();
