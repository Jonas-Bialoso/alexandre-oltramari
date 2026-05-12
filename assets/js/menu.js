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

    sections.forEach((sec, i) => {
      const item = document.createElement('button');
      item.className = 'menu__item';
      item.dataset.index = String(i);
      item.style.setProperty('--delay', `${0.08 + i * 0.025}s`);

      // Derive label: hero/intro/footer have direct names;
      // case-image and case-text use the tag/title.
      let label = '';
      if (labels[Object.keys(labels).find(k => sec.classList.contains(k))]) {
        label = labels[Object.keys(labels).find(k => sec.classList.contains(k))];
      } else if (sec.classList.contains('case-image')) {
        const tag = sec.querySelector('.case-image__tag');
        if (tag) {
          const clone = tag.cloneNode(true);
          clone.querySelectorAll('br').forEach(br => br.replaceWith(' '));
          label = clone.textContent.replace(/\s+/g, ' ').trim();
        } else {
          label = `Capa ${i}`;
        }
      } else if (sec.classList.contains('case-text')) {
        const title = sec.querySelector('.case-text__title');
        label = title ? title.textContent.trim() : `Case ${i}`;
      } else {
        label = `Seção ${i + 1}`;
      }

      item.innerHTML = `
        <span class="menu__index">${String(i + 1).padStart(2, '0')}</span>
        <span class="menu__label">${label}</span>
      `;

      item.addEventListener('click', () => {
        close();
        // Slight delay so the menu close animation feels smooth before the
        // section transition begins.
        setTimeout(() => window.OltScroll.goTo(i), 180);
      });

      menuNav.appendChild(item);
    });

    syncActive();
  });

  function syncActive() {
    if (!window.OltScroll) return;
    const cur = window.OltScroll.getCurrentIndex();
    menuNav.querySelectorAll('.menu__item').forEach((el, i) => {
      el.classList.toggle('is-active', i === cur);
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
