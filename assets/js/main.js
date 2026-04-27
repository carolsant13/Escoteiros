/* ============================================================
   71º Grupo de Escoteiros Minuano — main.js
   ============================================================ */

/* ── CARROSSEL HERO ── */
(function () {
  const slidesEl = document.getElementById('slides');
  if (!slidesEl) return;

  const dots  = document.querySelectorAll('.hero-dot');
  const total = dots.length;
  let current = 0, autoTimer;

  function goSlide(n) {
    current = (n + total) % total;
    slidesEl.style.transform = `translateX(-${current * 100}%)`;
    dots.forEach((d, i) => d.classList.toggle('active', i === current));
  }

  function resetTimer() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => goSlide(current + 1), 5000);
  }

  document.querySelector('.hero-btn.prev')?.addEventListener('click', () => { goSlide(current - 1); resetTimer(); });
  document.querySelector('.hero-btn.next')?.addEventListener('click', () => { goSlide(current + 1); resetTimer(); });

  dots.forEach((dot, i) => dot.addEventListener('click', () => { goSlide(i); resetTimer(); }));

  document.addEventListener('keydown', e => {
    if (e.key === 'ArrowLeft')  { goSlide(current - 1); resetTimer(); }
    if (e.key === 'ArrowRight') { goSlide(current + 1); resetTimer(); }
  });

  let touchX = 0;
  slidesEl.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
  slidesEl.addEventListener('touchend',   e => {
    const diff = touchX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 50) { goSlide(current + (diff > 0 ? 1 : -1)); resetTimer(); }
  }, { passive: true });

  resetTimer();
})();

/* ── MENU MOBILE ── */
(function () {
  const toggle = document.getElementById('menu-toggle');
  const inner  = document.getElementById('topnav-inner');
  if (!toggle || !inner) return;

  toggle.addEventListener('click', () => {
    const open = inner.classList.toggle('mobile-open');
    toggle.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', open);
  });

  inner.querySelectorAll('.has-dropdown > a').forEach(link => {
    link.addEventListener('click', e => {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        link.parentElement.classList.toggle('open');
      }
    });
  });

  document.addEventListener('click', e => {
    if (!inner.contains(e.target) && !toggle.contains(e.target)) {
      inner.classList.remove('mobile-open');
      toggle.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
})();

/* ── ACTIVE NAV LINK ── */
(function () {
  const page = location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.topnav a[href]').forEach(a => {
    const href = a.getAttribute('href').split('#')[0].split('/').pop();
    if (href && href === page) a.style.background = 'rgba(255,255,255,0.18)';
  });
})();

/* ── SCROLL REVEAL ── */
(function () {
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('revealed'));
    return;
  }
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); obs.unobserve(e.target); } });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
})();

/* ── COUNTER ANIMATION ── */
(function () {
  const counters = document.querySelectorAll('.counter');
  if (!counters.length) return;

  const obs = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.dataset.target, 10);
      const suffix = el.dataset.suffix || '';
      const dur = 2000;
      const start = performance.now();

      (function tick(now) {
        const p = Math.min((now - start) / dur, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.floor(eased * target) + suffix;
        if (p < 1) requestAnimationFrame(tick);
      })(start);

      obs.unobserve(el);
    });
  }, { threshold: 0.5 });

  counters.forEach(c => obs.observe(c));
})();

/* ── TAB SYSTEM ── */
(function () {
  document.querySelectorAll('.tabs-wrapper').forEach(wrapper => {
    const btns     = Array.from(wrapper.querySelectorAll('.tab-btn'));
    const contents = Array.from(wrapper.querySelectorAll('.tab-content'));

    function activate(idx) {
      btns.forEach((b, i) => b.classList.toggle('active', i === idx));
      contents.forEach((c, i) => c.classList.toggle('active', i === idx));
    }

    btns.forEach((btn, i) => btn.addEventListener('click', () => activate(i)));

    // Activate based on URL hash
    const hash = location.hash.slice(1);
    if (hash) {
      const idx = btns.findIndex(b => b.dataset.tab === hash);
      if (idx !== -1) {
        activate(idx);
        setTimeout(() => wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
      }
    }
  });
})();

/* ── ACCORDION ── */
(function () {
  document.querySelectorAll('.accordion-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const item = btn.parentElement;
      const isOpen = item.classList.contains('open');
      item.closest('.accordion').querySelectorAll('.accordion-item').forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });
})();

/* ── LIGHTBOX ── */
(function () {
  const lightbox = document.getElementById('lightbox');
  if (!lightbox) return;

  const inner   = lightbox.querySelector('.lightbox-inner');
  const caption = lightbox.querySelector('.lightbox-caption');
  const closeBtn = lightbox.querySelector('.lightbox-close');

  document.querySelectorAll('[data-lightbox]').forEach(item => {
    item.addEventListener('click', () => {
      inner.innerHTML = item.dataset.content || item.querySelector('.gallery-thumb')?.innerHTML || '';
      if (caption) caption.textContent = item.dataset.caption || '';
      lightbox.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  });

  function close() { lightbox.classList.remove('open'); document.body.style.overflow = ''; }
  closeBtn?.addEventListener('click', close);
  lightbox.addEventListener('click', e => { if (e.target === lightbox) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();

/* ── GALLERY FILTER ── */
(function () {
  const filterBtns = document.querySelectorAll('.filter-btn');
  if (!filterBtns.length) return;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.filter;
      document.querySelectorAll('.gallery-item').forEach(item => {
        item.style.display = (cat === 'all' || item.dataset.cat === cat) ? '' : 'none';
      });
    });
  });
})();

/* ── BACK TO TOP ── */
(function () {
  const btn = document.getElementById('back-to-top');
  if (!btn) return;
  window.addEventListener('scroll', () => btn.classList.toggle('visible', window.scrollY > 400), { passive: true });
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
})();

/* ── FORM VALIDATION ── */
(function () {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      let valid = true;

      form.querySelectorAll('[required]').forEach(field => {
        const group = field.closest('.form-group');
        if (!field.value.trim()) {
          group?.classList.add('error');
          valid = false;
        } else {
          group?.classList.remove('error');
        }
      });

      if (!valid) return;

      const btn = form.querySelector('[type="submit"]');
      btn.textContent = 'Enviando…';
      btn.disabled = true;
      setTimeout(() => {
        form.innerHTML = '<div class="success-msg"><p>✅ Mensagem enviada com sucesso!<br>Entraremos em contato em breve.</p></div>';
      }, 1400);
    });

    form.querySelectorAll('[required]').forEach(field => {
      field.addEventListener('input', () => field.closest('.form-group')?.classList.remove('error'));
    });
  });
})();
