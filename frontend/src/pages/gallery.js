import { API } from '../utils/config.js';
import { getLang, t } from '../utils/i18n.js';

export async function loadGallery() {
  try {
    const lang = getLang();
    const res  = await fetch(`${API}/gallery?lang=${lang}`);
    const items = await res.json();

    const grid  = document.getElementById('gallery-grid');
    const empty = document.getElementById('gallery-empty');
    if (!grid) return;

    if (!items.length) {
      grid.innerHTML = '';
      if (empty) empty.classList.remove('hidden');
      return;
    }

    if (empty) empty.classList.add('hidden');

    grid.innerHTML = items.map(item => `
      <div class="gallery-item"
           data-url="${item.url}"
           data-popis="${item.popis || ''}"
           data-nadpis="${item.nadpis || ''}">
        ${item.nadpis ? `<div class="gallery-nadpis">${item.nadpis}</div>` : ''}
        <div class="gallery-img-wrap">
          <img src="${item.url}"
               alt="${item.nadpis || item.popis || 'Fotografie'}"
               loading="lazy"
               onerror="this.src='https://placehold.co/400x300?text=Chyba'">
        </div>
        ${item.popis ? `<div class="gallery-popis">${item.popis}</div>` : ''}
      </div>
    `).join('');

    // Lightbox click
    grid.querySelectorAll('.gallery-item').forEach(item => {
      item.addEventListener('click', () => {
        const lightbox = document.getElementById('lightbox');
        const img      = document.getElementById('lightbox-img');
        const popis    = document.getElementById('lightbox-popis');
        if (lightbox && img) {
          img.src = item.dataset.url;
          if (popis) {
            const n = item.dataset.nadpis || '';
            const p = item.dataset.popis  || '';
            popis.innerHTML = (n ? `<strong>${n}</strong>` : '') + (p ? `<br>${p}` : '');
          }
          lightbox.classList.remove('hidden');
        }
      });
    });
  } catch (e) {
    console.error('Chyba galerie:', e);
  }
}

export function initLightbox() {
  const lightbox = document.getElementById('lightbox');
  if (!lightbox) return;
  const close = () => lightbox.classList.add('hidden');
  document.getElementById('lightbox-close')?.addEventListener('click', close);
  document.getElementById('lightbox-close-btn')?.addEventListener('click', close);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
}