/* Lightbox — image lightbox + YouTube video lightbox */
(() => {
  const lb = document.getElementById('lightbox');
  const content = document.getElementById('lightbox-content');
  if (!lb || !content) return;

  // Placeholder YouTube video for all video cards
  const PLACEHOLDER_VIDEO_ID = 'MLpWrANjFbI';

  function open(node) {
    content.innerHTML = '';
    content.appendChild(node);
    lb.classList.add('is-open');
    lb.setAttribute('aria-hidden', 'false');
    document.documentElement.style.overflow = 'hidden';
  }

  function close() {
    lb.classList.remove('is-open');
    lb.setAttribute('aria-hidden', 'true');
    setTimeout(() => { content.innerHTML = ''; }, 250);
  }

  // Click outside content / close button closes
  lb.addEventListener('click', (e) => {
    if (e.target === lb || e.target.matches('[data-lightbox-close]') || e.target.closest('[data-lightbox-close]')) {
      close();
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lb.classList.contains('is-open')) close();
  });

  // Video cards → YouTube embed
  document.querySelectorAll('[data-video]').forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.video || PLACEHOLDER_VIDEO_ID;
      const iframe = document.createElement('iframe');
      iframe.className = 'lightbox__video';
      iframe.src = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
      iframe.allow = 'autoplay; encrypted-media; fullscreen';
      iframe.allowFullscreen = true;
      open(iframe);
    });
  });

  // Image lightbox triggers
  document.querySelectorAll('[data-lightbox-image]').forEach(img => {
    img.addEventListener('click', (e) => {
      // Only allow click when its section is active (not in transition)
      const section = img.closest('.snap');
      if (section && !section.classList.contains('is-active')) return;
      const big = document.createElement('img');
      big.className = 'lightbox__image';
      big.src = img.src;
      big.alt = img.alt || '';
      open(big);
    });
  });
})();
