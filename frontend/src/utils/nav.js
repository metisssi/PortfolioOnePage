const SECTIONS = ['hero', 'sluzby', 'o-mne', 'gallery', 'reviews', 'contact'];

import { loadSluzby } from '../pages/sluzby.js';
import { loadGallery } from '../pages/gallery.js';
import { loadReviews } from '../pages/reviews.js';

export function showSection(id) {
  SECTIONS.forEach(s => {
    const el = document.getElementById(s);
    if (el) {
      el.classList.remove('active', 'visible');
      el.style.display = 'none';
    }
  });

  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.section === id);
  });

  const target = document.getElementById(id);
  if (!target) return;

  target.style.display = 'flex';
  target.classList.add('active');
  window.scrollTo({ top: 0, behavior: 'instant' });

  // footer only on contact
  const footer = document.getElementById('footer');
  if (footer) footer.style.display = id === 'contact' ? 'block' : 'none';

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      target.classList.add('visible');
    });
  });

  if (id === 'sluzby') loadSluzby();
  if (id === 'o-mne')  loadSluzby(); // o-mne also uses content API
  if (id === 'gallery') loadGallery();
  if (id === 'reviews') loadReviews();

  document.querySelector('.nav-links')?.classList.remove('open');
}

export function initNav() {
  // navbar scroll style
  window.addEventListener('scroll', () => {
    document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 30);
  });

  // nav buttons
  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.addEventListener('click', () => showSection(btn.dataset.section));
  });

  // hero CTA buttons + any [data-section] links
  document.querySelectorAll('[data-section]').forEach(el => {
    if (!el.classList.contains('nav-btn')) {
      el.addEventListener('click', () => showSection(el.dataset.section));
    }
  });

  // hamburger
  document.getElementById('hamburger')?.addEventListener('click', () => {
    document.querySelector('.nav-links')?.classList.toggle('open');
  });

  // logo click → hero
  document.querySelector('.nav-logo')?.addEventListener('click', () => showSection('hero'));
}