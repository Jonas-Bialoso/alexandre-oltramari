/* Popup menu — opens from persistent burger, lists all sections,
 * click → navigate via OltScroll.goTo, then close.
 */
(() => {
  const burger = document.getElementById('burger');
  const menu = document.getElementById('menu');
  const menuNav = document.getElementById('menu-nav');
  if (!burger || !menu || !menuNav) return;

  // Wait for OltScroll API (stacking-scroll loads as defer too)
  function whenReady(cb) {
    if (window.OltScroll) return cb();
    requestAnimationFrame(() => whenReady(cb));
  }

  whenReady(() => {
    const sections = window.OltScroll.getSections();
    const labels = {
      hero: 'OLT — Início',
      intro: 'Apresentação',
      'site-footer': 'Quem é Alexandre'
    };

    // Build a filtered list: only "main" sections appear in the menu.
    // case-text sections collapse into their preceding case-image entry
    // (clicking still navigates to the case-image — the photo plate).
    const entries = [];
    sections.forEach((sec, i) => {
      if (sec.classList.contains('case-text')) return; // skip — merged into prev case-image
      entries.push({ sec, sectionIndex: i });
    });

    entries.forEach(({ sec, sectionIndex }, displayIdx) => {
      const item = document.createElement('button');
      item.className = 'menu__item';
      item.dataset.index = String(sectionIndex);
      item.style.setProperty('--delay', `${0.08 + displayIdx * 0.025}s`);

      // Derive label: hero/intro/footer have direct names; case-image uses tag.
      let label = '';
      const namedKey = Object.keys(labels).find(k => sec.classList.contains(k));
      if (namedKey) {
        label = labels[namedKey];
      } else if (sec.classList.contains('case-image')) {
        const tag = sec.querySelector('.case-image__tag');
        if (tag) {
          const clone = tag.cloneNode(true);
          clone.querySelectorAll('br').forEach(br => br.replaceWith(' '));
          label = clone.textContent.replace(/\s+/g, ' ').trim();
        } else {
          label = `Capa ${displayIdx}`;
        }
      } else {
        label = `Seção ${displayIdx + 1}`;
      }

      item.innerHTML = `
        <span class="menu__index">${String(displayIdx + 1).padStart(2, '0')}</span>
        <span class="menu__label">${label}</span>
      `;

      item.addEventListener('click', () => {
        close();
        // Slight delay so the menu close animation feels smooth before the
        // section transition begins.
        setTimeout(() => window.OltScroll.goTo(sectionIndex), 180);
      });

      menuNav.appendChild(item);
    });

    syncActive();
  });

  function syncActive() {
    if (!window.OltScroll) return;
    const cur = window.OltScroll.getCurrentIndex();
    // Each menu item carries data-index = real section index. An item is
    // active when current section is exactly that index, OR when current is
    // a case-text immediately after this case-image (i.e. between this
    // item's section index and the next item's section index).
    const items = Array.from(menuNav.querySelectorAll('.menu__item'));
    const indices = items.map(el => parseInt(el.dataset.index, 10));
    items.forEach((el, i) => {
      const start = indices[i];
      const end = i + 1 < indices.length ? indices[i + 1] : Infinity;
      el.classList.toggle('is-active', cur >= start && cur < end);
    });
  }

  function open() {
    syncActive();
    menu.classList.add('is-open');
    menu.setAttribute('aria-hidden', 'false');
    burger.setAttribute('aria-expanded', 'true');
    document.body.dataset.menuOpen = 'true';
  }

  function close() {
    menu.classList.remove('is-open');
    menu.setAttribute('aria-hidden', 'true');
    burger.setAttribute('aria-expanded', 'false');
    document.body.dataset.menuOpen = 'false';
  }

  burger.addEventListener('click', () => {
    if (menu.classList.contains('is-open')) close(); else open();
  });

  menu.addEventListener('click', (e) => {
    if (e.target === menu || e.target.matches('[data-menu-close]') || e.target.closest('[data-menu-close]')) {
      close();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && menu.classList.contains('is-open')) close();
  });
})();
