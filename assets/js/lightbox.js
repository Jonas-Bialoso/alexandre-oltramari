/* Lightbox — image lightbox + Vimeo/YouTube video lightbox.
 *
 *  data-video attribute on the card holds the video ID.
 *  data-video-provider="vimeo"|"youtube" can be set explicitly;
 *  if omitted, all-numeric IDs are treated as Vimeo and everything
 *  else as YouTube (sensible default — YouTube IDs always contain
 *  letters or dashes; Vimeo IDs are always numeric).
 */
(() => {
  const lb = document.getElementById('lightbox');
  const content = document.getElementById('lightbox-content') || document.querySelector('.lightbox__content');
  if (!lb || !content) return;

  // Fallback if a card is missing data-video.
  const PLACEHOLDER_VIDEO_ID = 'MLpWrANjFbI'; // YouTube fallback

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
    document.documentElement.style.overflow = '';
  }

  // Click outside content / close button closes
  lb.addEventListener('click', (e) => {
    if (e.target === lb || e.target.matches('[data-lightbox-close], .lightbox__close') || e.target.closest('[data-lightbox-close], .lightbox__close')) {
      close();
    }
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && lb.classList.contains('is-open')) close();
  });

  function buildEmbedUrl(id, provider) {
    if (!provider) {
      provider = /^\d+$/.test(id) ? 'vimeo' : 'youtube';
    }
    if (provider === 'vimeo') {
      return `https://player.vimeo.com/video/${id}?autoplay=1&color=00fff2`;
    }
    return `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
  }

  // Video cards → embed
  document.querySelectorAll('[data-video]').forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.video || PLACEHOLDER_VIDEO_ID;
      const provider = card.dataset.videoProvider || '';
      const iframe = document.createElement('iframe');
      iframe.className = 'lightbox__video';
      iframe.src = buildEmbedUrl(id, provider);
      iframe.allow = 'autoplay; encrypted-media; fullscreen; picture-in-picture';
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
