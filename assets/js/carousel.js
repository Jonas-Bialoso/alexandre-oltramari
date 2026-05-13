/* Carousel — desktop: transform-based with arrows + pagination.
 *           mobile: native horizontal swipe; pagination synced to scroll.
 */
(() => {
  const MOBILE = () => window.matchMedia('(max-width: 899px)').matches;

  const carousels = document.querySelectorAll('[data-carousel]');

  carousels.forEach(root => {
    const carouselEl = root.querySelector('.case-text__carousel');
    const viewport = root.querySelector('.case-text__viewport');
    const track = root.querySelector('.case-text__track');
    const cards = track ? Array.from(track.children) : [];
    if (!viewport || !track || !cards.length) return;

    // Pull the nav OUT of the scrolling viewport so it doesn't ride
    // along with native horizontal scroll on mobile. Place it as a
    // sibling of the viewport, positioned absolutely against the
    // carousel container.
    const liveNav = root.querySelector('.case-text__viewport > .case-text__nav');
    if (liveNav && carouselEl) carouselEl.insertBefore(liveNav, viewport);

    const perPageDesktop = parseInt(root.dataset.perPage || '2', 10);
    const totalSlides = cards.length;
    const prevBtn = root.querySelector('[data-carousel-prev]');
    const nextBtn = root.querySelector('[data-carousel-next]');
    const paginationEl = root.querySelector('[data-carousel-pagination]');
    const navEl = root.querySelector('.case-text__nav');

    // Helpers
    function pageCount() {
      const perPage = MOBILE() ? 1 : perPageDesktop;
      return Math.max(1, Math.ceil(totalSlides / perPage));
    }

    // If carousel can't scroll on either mode, strip controls.
    // (Single slide regardless of viewport.)
    if (totalSlides <= 1) {
      if (paginationEl) paginationEl.remove();
      if (navEl) navEl.remove();
      return;
    }

    let index = 0;

    function update() {
      const pages = pageCount();
      if (!MOBILE()) {
        // CSS shifts the track by --slide-index * --slide-step, where
        // --slide-step is the width of ONE video card + gap. To advance a
        // full page (perPageDesktop cards) per arrow click, multiply.
        viewport.style.setProperty('--slide-index', String(index * perPageDesktop));
      } else {
        // Mobile uses native scroll — programmatic scroll on arrow click.
      }
      if (paginationEl) {
        [...paginationEl.children].forEach((dot, i) => {
          dot.classList.toggle('is-active', i === index);
        });
        paginationEl.style.display = pages <= 1 ? 'none' : '';
      }
      if (navEl) {
        navEl.style.display = pages <= 1 ? 'none' : '';
      }
      if (prevBtn) prevBtn.toggleAttribute('disabled', index === 0);
      if (nextBtn) nextBtn.toggleAttribute('disabled', index === pages - 1);
    }

    function go(delta) {
      const pages = pageCount();
      const next = index + delta;
      if (next < 0 || next > pages - 1) return;
      index = next;
      if (MOBILE()) {
        // Scroll the viewport to the target card
        const card = cards[next]; // perPage=1 on mobile → card index = page index
        if (card) {
          const left = card.offsetLeft - viewport.offsetLeft;
          viewport.scrollTo({ left, behavior: 'smooth' });
        }
      }
      update();
    }

    function buildDots() {
      if (!paginationEl) return;
      paginationEl.innerHTML = '';
      const pages = pageCount();
      for (let i = 0; i < pages; i++) {
        const dot = document.createElement('button');
        dot.className = 'case-text__dot';
        dot.setAttribute('aria-label', `Ir para slide ${i + 1}`);
        dot.addEventListener('click', () => {
          index = i;
          if (MOBILE()) {
            const card = cards[i];
            if (card) {
              const left = card.offsetLeft - viewport.offsetLeft;
              viewport.scrollTo({ left, behavior: 'smooth' });
            }
          }
          update();
        });
        paginationEl.appendChild(dot);
      }
    }

    buildDots();

    if (prevBtn) prevBtn.addEventListener('click', () => go(-1));
    if (nextBtn) nextBtn.addEventListener('click', () => go(1));

    // Mobile: detect which card is currently in view as user swipes
    if (MOBILE()) {
      let ticking = false;
      viewport.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
          ticking = false;
          const vpLeft = viewport.scrollLeft;
          let closest = 0;
          let minDist = Infinity;
          cards.forEach((c, i) => {
            const dist = Math.abs(c.offsetLeft - viewport.offsetLeft - vpLeft);
            if (dist < minDist) { minDist = dist; closest = i; }
          });
          if (closest !== index) {
            index = closest;
            update();
          }
        });
      }, { passive: true });
    }

    // On resize crossing the mobile/desktop threshold, rebuild controls
    let wasMobile = MOBILE();
    window.addEventListener('resize', () => {
      const nowMobile = MOBILE();
      if (nowMobile !== wasMobile) {
        wasMobile = nowMobile;
        index = 0;
        viewport.scrollLeft = 0;
        viewport.style.removeProperty('--slide-index');
        buildDots();
        update();
      }
    });

    update();
  });
})();
