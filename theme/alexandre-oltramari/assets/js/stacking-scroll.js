/* Stacking reveal scroll — premium curtain effect.
 *  - Section atual fica fixa.
 *  - Próxima seção sobe de baixo, cobrindo a atual.
 *  - Durante o scroll, acompanha o gesto suavemente (sem snap brusco).
 *  - Ao passar 30% de viewport de deslocamento, completa a transição.
 *  - Soltando antes dos 30%, volta pra atual.
 */
(() => {
  const sections = Array.from(document.querySelectorAll('.snap'));
  if (!sections.length) return;

  /* Stacking reveal runs on every viewport — mobile too. */
  const lastIndex = sections.length - 1;
  const THRESHOLD = 0.15;       // 15% do viewport pra snap completo
  const SENSITIVITY = 0.0028;   // wheel delta → progress
  const TOUCH_SENSITIVITY = 0.0055;
  const RAF_LERP = 0.18;        // suavização do progresso visual

  let current = 0;
  let progress = 0;             // 0 → 1 (forward), 0 → -1 (backward)
  let renderedProgress = 0;     // valor renderizado (lerp do progress)
  let isAnimating = false;
  let rafId = null;
  let idleTimer = null;

  // z-index: later section higher → sobe e cobre.
  sections.forEach((s, i) => {
    s.style.zIndex = String(i + 1);
    s.dataset.index = String(i);
  });

  let pageNav = null;

  function setActive(idx) {
    sections.forEach((s, i) => {
      if (i === idx) {
        s.classList.remove('is-active');
        void s.offsetWidth;
        s.classList.add('is-active');
      } else {
        s.classList.remove('is-active');
      }
    });
    if (pageNav) {
      pageNav.querySelectorAll('.page-nav__dot').forEach((dot, i) => {
        dot.classList.toggle('is-active', i === idx);
      });
    }
    // Hide nav on hero, switch tone on dark case-image sections.
    const cur = sections[idx];
    document.body.dataset.onHero = cur.classList.contains('hero') ? 'true' : 'false';
    document.body.dataset.sectionTone = cur.classList.contains('case-image') ? 'dark' : 'light';
  }

  function setBaseTransform(i, ty) {
    sections[i].style.transform = `translate3d(0, ${ty}%, 0)`;
  }

  function applyVisual() {
    // Section current is at 0%. Sections before are also at 0% (hidden by current).
    // Sections after are at 100% (off-screen below) — except next/prev being animated.
    const p = renderedProgress;

    if (p > 0 && current < lastIndex) {
      // Forward: next section rises from 100% to (100 - p*100)%
      setBaseTransform(current + 1, (1 - p) * 100);
    } else if (p < 0 && current > 0) {
      // Backward: current slides down from 0 to (-p)*100
      setBaseTransform(current, -p * 100);
    }
  }

  function rafLoop() {
    const diff = progress - renderedProgress;
    if (Math.abs(diff) > 0.001) {
      renderedProgress += diff * RAF_LERP;
      applyVisual();
      rafId = requestAnimationFrame(rafLoop);
    } else {
      renderedProgress = progress;
      applyVisual();
      rafId = null;
    }
  }

  function ensureRaf() {
    if (rafId === null) rafId = requestAnimationFrame(rafLoop);
  }

  const TRANSITION_MS = 950;

  function runTransition(el, transform, after) {
    el.classList.add('is-animating');
    // Force layout flush so the transition picks up.
    void el.offsetWidth;
    el.style.transform = transform;
    setTimeout(() => {
      el.classList.remove('is-animating');
      after();
    }, TRANSITION_MS);
  }

  function commitForward() {
    if (current >= lastIndex || isAnimating) return;
    isAnimating = true;
    cancelAnimationFrame(rafId); rafId = null;
    const target = sections[current + 1];
    runTransition(target, 'translate3d(0, 0, 0)', () => {
      current += 1;
      progress = 0;
      renderedProgress = 0;
      isAnimating = false;
      setActive(current);
    });
  }

  function commitBackward() {
    if (current <= 0 || isAnimating) return;
    isAnimating = true;
    cancelAnimationFrame(rafId); rafId = null;
    const moving = sections[current];
    runTransition(moving, 'translate3d(0, 100%, 0)', () => {
      current -= 1;
      progress = 0;
      renderedProgress = 0;
      isAnimating = false;
      setActive(current);
    });
  }

  // Jump to an arbitrary section (used by side nav). No partial animation,
  // sections in between are repositioned instantly so the perceived
  // animation is just the moving section curtain-ing in.
  function goTo(targetIdx) {
    if (targetIdx === current || isAnimating) return;
    if (targetIdx < 0 || targetIdx > lastIndex) return;
    isAnimating = true;
    cancelAnimationFrame(rafId); rafId = null;

    if (targetIdx > current) {
      // Forward: snap all sections between current and target-1 to translateY(0)
      for (let i = current + 1; i < targetIdx; i++) {
        sections[i].style.transition = 'none';
        sections[i].style.transform = 'translate3d(0, 0, 0)';
        void sections[i].offsetWidth;
        sections[i].style.transition = '';
      }
      const target = sections[targetIdx];
      runTransition(target, 'translate3d(0, 0, 0)', () => {
        current = targetIdx;
        progress = 0; renderedProgress = 0; isAnimating = false;
        setActive(current);
      });
    } else {
      // Backward: snap all sections between current and target+1 to translateY(100%)
      for (let i = current - 1; i > targetIdx; i--) {
        sections[i].style.transition = 'none';
        sections[i].style.transform = 'translate3d(0, 100%, 0)';
        void sections[i].offsetWidth;
        sections[i].style.transition = '';
      }
      const moving = sections[current];
      runTransition(moving, 'translate3d(0, 100%, 0)', () => {
        current = targetIdx;
        progress = 0; renderedProgress = 0; isAnimating = false;
        setActive(current);
      });
    }
  }

  // Expose API for the menu / other modules
  window.OltScroll = {
    goTo,
    getCurrentIndex: () => current,
    getSectionCount: () => sections.length,
    getSections: () => sections.slice()
  };

  // Build side scroll nav
  (function buildPageNav() {
    const labels = {
      hero: 'OLT',
      intro: 'Intro',
      'case-image': 'Capa',
      'case-text': 'Cases',
      'site-footer': 'Sobre'
    };
    const nav = document.createElement('nav');
    nav.className = 'page-nav';
    nav.setAttribute('aria-label', 'Navegação de seções');
    sections.forEach((s, i) => {
      const dot = document.createElement('button');
      dot.className = 'page-nav__dot';
      dot.dataset.index = String(i);
      let label = '';
      for (const key in labels) {
        if (s.classList.contains(key)) { label = labels[key]; break; }
      }
      dot.setAttribute('data-label', `${String(i + 1).padStart(2, '0')} · ${label}`);
      dot.setAttribute('aria-label', `Ir para ${label} ${i + 1}`);
      dot.addEventListener('click', () => goTo(i));
      nav.appendChild(dot);
    });
    document.body.appendChild(nav);
    pageNav = nav;
  })();

  function snapBack() {
    if (isAnimating) return;
    isAnimating = true;
    cancelAnimationFrame(rafId); rafId = null;
    const targetIdx = progress > 0 ? current + 1 : current;
    const moving = sections[targetIdx];
    if (!moving) { isAnimating = false; progress = 0; renderedProgress = 0; return; }
    const transform = progress > 0
      ? 'translate3d(0, 100%, 0)'
      : 'translate3d(0, 0, 0)';
    runTransition(moving, transform, () => {
      progress = 0;
      renderedProgress = 0;
      isAnimating = false;
    });
  }

  function scheduleIdleCheck() {
    clearTimeout(idleTimer);
    idleTimer = setTimeout(() => {
      if (isAnimating) return;
      if (Math.abs(progress) >= THRESHOLD) {
        progress > 0 ? commitForward() : commitBackward();
      } else if (Math.abs(progress) > 0.01) {
        snapBack();
      }
    }, 90);
  }

  function addDelta(deltaProgress) {
    if (isAnimating) return;
    progress += deltaProgress;

    // Clamp at boundaries
    if (current === 0 && progress < 0) progress = 0;
    if (current === lastIndex && progress > 0) progress = 0;

    progress = Math.max(-1, Math.min(1, progress));

    if (Math.abs(progress) >= THRESHOLD) {
      progress > 0 ? commitForward() : commitBackward();
      return;
    }

    ensureRaf();
    scheduleIdleCheck();
  }

  // Wheel — but let the menu (or any [data-scroll-lock-bypass]) handle its own scroll
  window.addEventListener('wheel', (e) => {
    if (document.body.dataset.menuOpen === 'true') return;
    if (e.target.closest('[data-scroll-lock-bypass], .lightbox')) return;
    e.preventDefault();
    addDelta(e.deltaY * SENSITIVITY);
  }, { passive: false });

  // Keyboard
  window.addEventListener('keydown', (e) => {
    if (isAnimating) return;
    if (['ArrowDown', 'PageDown', ' '].includes(e.key)) {
      e.preventDefault();
      commitForward();
    } else if (['ArrowUp', 'PageUp'].includes(e.key)) {
      e.preventDefault();
      commitBackward();
    } else if (e.key === 'Home') {
      e.preventDefault();
      // jump to first
      while (current > 0) { commitBackward(); break; }
    } else if (e.key === 'End') {
      e.preventDefault();
      while (current < lastIndex) { commitForward(); break; }
    }
  });

  // Touch — direction-aware: vertical drives curtain, horizontal lets native
  // scroll work (used by mobile carousel viewports).
  let touchStartY = 0;
  let touchStartX = 0;
  let touchLastY = 0;
  let touchDirection = null; // 'v' | 'h'
  let touchInCarousel = false;

  window.addEventListener('touchstart', (e) => {
    touchStartY = touchLastY = e.touches[0].clientY;
    touchStartX = e.touches[0].clientX;
    touchDirection = null;
    touchInCarousel = !!e.target.closest('.case-text__viewport');
  }, { passive: true });

  window.addEventListener('touchmove', (e) => {
    if (isAnimating) return;
    if (document.body.dataset.menuOpen === 'true') return;
    if (e.target.closest('[data-scroll-lock-bypass], .lightbox')) return;

    const y = e.touches[0].clientY;
    const x = e.touches[0].clientX;

    if (!touchDirection) {
      const dy0 = Math.abs(y - touchStartY);
      const dx0 = Math.abs(x - touchStartX);
      if (dy0 < 6 && dx0 < 6) return;
      touchDirection = dy0 > dx0 ? 'v' : 'h';
    }

    if (touchDirection === 'h' && touchInCarousel) {
      // Let native horizontal scroll handle it
      return;
    }

    const dy = touchLastY - y;
    touchLastY = y;
    addDelta(dy * TOUCH_SENSITIVITY);
    if (e.cancelable) e.preventDefault();
  }, { passive: false });

  window.addEventListener('touchend', () => {
    scheduleIdleCheck();
  });

  // Init: lift section 0 into view without transition.
  sections[0].style.transform = 'translate3d(0, 0, 0)';
  setActive(0);

  // Reduced motion: jump instantly
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    sections.forEach(s => { s.style.transition = 'none'; });
  }
})();
