import { API } from '../utils/config.js';

export async function loadGallery() {
  try {
    const res = await fetch(`${API}/gallery`);
    const items = await res.json();
    const grid = document.getElementById('gallery-grid');
    const empty = document.getElementById('gallery-empty');
    if (!grid) return;

    if (!items.length) {
      grid.innerHTML = '';
      if (empty) empty.classList.remove('hidden');
      return;
    }

    if (empty) empty.classList.add('hidden');

    grid.innerHTML = items.map(item => `
      <div class="gallery-item" data-url="${item.url}" data-popis="${item.popis || ''}">
        <img src="${item.url}" alt="${item.popis || 'Fotografie'}" loading="lazy"
             onerror="this.src='https://placehold.co/400x300?text=Chyba'">
        ${item.popis ? `<div class="gallery-caption">${item.popis}</div>` : ''}
      </div>
    `).join('');

    // attach lightbox click
    grid.querySelectorAll('.gallery-item').forEach(item => {
      item.addEventListener('click', () => {
        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const popis = document.getElementById('lightbox-popis');
        if (lightbox && img) {
          img.src = item.dataset.url;
          if (popis) popis.textContent = item.dataset.popis || '';
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

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}