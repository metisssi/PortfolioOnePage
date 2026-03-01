import { loadSluzby, clearContentCache } from '../pages/sluzby.js';
import { loadGallery } from '../pages/gallery.js';
import { loadReviews } from '../pages/reviews.js';
import { getLang, setLang, applyTranslations } from './i18n.js';

let dataLoaded = false;

function loadAllData() {
  if (dataLoaded) return;
  dataLoaded = true;
  loadSluzby();
  loadGallery();
  loadReviews();
}

export function showSection(id) {
  const target = document.getElementById(id);
  if (!target) return;
  target.scrollIntoView({ behavior: 'smooth' });
}

export function initNav() {
  loadAllData();

  // Apply saved language on boot
  applyTranslations();

  // Language switcher buttons
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const newLang = btn.dataset.lang;
      if (newLang === getLang()) return;
      setLang(newLang);
      applyTranslations();
      // Re-render dynamic content in the new language
      clearContentCache();
      loadSluzby();
      loadGallery();
    });
  });

  // Navbar background on scroll
  window.addEventListener('scroll', () => {
    document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 30);
  });

  // Highlight active nav button based on scroll
  const sections = document.querySelectorAll('.section');
  const navBtns = document.querySelectorAll('.nav-btn');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navBtns.forEach(btn => {
          btn.classList.toggle('active', btn.dataset.section === entry.target.id);
        });
      }
    });
  }, { threshold: 0.3, rootMargin: '-70px 0px 0px 0px' });

  sections.forEach(section => observer.observe(section));

  // Fade-in sections on scroll
  const fadeObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) entry.target.classList.add('visible');
    });
  }, { threshold: 0.1 });

  sections.forEach(section => {
    if (section.id === 'hero') section.classList.add('visible');
    else fadeObserver.observe(section);
  });

  // Nav button clicks → smooth scroll
  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      showSection(btn.dataset.section);
      document.querySelector('.nav-links')?.classList.remove('open');
    });
  });

  // Any [data-section] → smooth scroll
  document.querySelectorAll('[data-section]').forEach(el => {
    if (!el.classList.contains('nav-btn')) {
      el.addEventListener('click', () => showSection(el.dataset.section));
    }
  });

  // Hamburger menu
  document.getElementById('hamburger')?.addEventListener('click', () => {
    document.querySelector('.nav-links')?.classList.toggle('open');
  });

  // Logo → scroll to hero
  document.querySelector('.nav-logo')?.addEventListener('click', () => showSection('hero'));

  // Footer always visible
  const footer = document.getElementById('footer');
  if (footer) footer.style.display = 'block';
}