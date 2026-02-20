import { API } from '../utils/config.js';

let contentCache = null;

async function fetchContent() {
  if (contentCache) return contentCache;
  const res = await fetch(`${API}/content`);
  contentCache = await res.json();
  return contentCache;
}

export async function loadSluzby() {
  try {
    const data = await fetchContent();

    // --- SLUZBY section ---
    const nadpisEl = document.getElementById('sluzby-nadpis');
    if (nadpisEl) nadpisEl.textContent = data.sluzby?.nadpis || 'Léčba bolestí zad';

    const procNadpisEl = document.getElementById('proc-nadpis');
    if (procNadpisEl) procNadpisEl.textContent = data.proc_za_mnou?.nadpis || 'Proč za mnou?';

    const sluzbyList = document.getElementById('sluzby-list');
    if (sluzbyList) {
      const lines = (data.sluzby?.text || '').split('\n').filter(l => l.trim());
      sluzbyList.innerHTML = lines.map(line => {
        const clean = line.replace(/^\d+\.\s*/, '');
        return `<div class="sluzby-item">${clean}</div>`;
      }).join('') || '<div class="sluzby-item">Výhřezlé ploténky</div>';
    }

    const procList = document.getElementById('proc-list');
    if (procList) {
      procList.innerHTML = (data.proc_za_mnou?.body || [])
        .map(item => `<li>${item}</li>`).join('');
    }

    // --- O MNE section ---
    const omneNadpisEl = document.getElementById('omne-nadpis');
    if (omneNadpisEl) omneNadpisEl.textContent = data.o_mne?.nadpis || 'O mně';

    const omneTextEl = document.getElementById('omne-text');
    if (omneTextEl) omneTextEl.textContent = data.o_mne?.text || '';

    const omneList = document.getElementById('omne-list');
    if (omneList) {
      omneList.innerHTML = (data.o_mne?.body || [])
        .map(item => `<li>${item}</li>`).join('');
    }

    // --- O MNE foto ---
    const omneImg = document.getElementById('omne-foto');
    const omneImgWrap = document.getElementById('omne-foto-wrap');
    if (omneImg && data.o_mne?.foto) {
      omneImg.src = data.o_mne.foto;
      omneImg.alt = data.o_mne?.nadpis || 'O mně';
      if (omneImgWrap) omneImgWrap.style.display = 'block';
    } else if (omneImgWrap && !data.o_mne?.foto) {
      omneImgWrap.style.display = 'none';
    }

  } catch (e) {
    console.error('Chyba načítání obsahu:', e);
  }
}