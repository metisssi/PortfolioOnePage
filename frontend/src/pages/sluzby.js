import { API } from '../utils/config.js';
import { getLang } from '../utils/i18n.js';

let contentCache = null;

async function fetchContent() {
  if (contentCache) return contentCache;
  const res = await fetch(`${API}/content`);
  contentCache = await res.json();
  return contentCache;
}

// Call this to clear cache & re-render when language changes
export function clearContentCache() {
  contentCache = null;
}

/**
 * Pick the right language version from a section.
 * New format:  { cs: { nadpis, text, body }, en: { nadpis, text, body } }
 * Old format (fallback): { nadpis, text, body }  — treated as Czech
 */
function pick(section) {
  if (!section) return {};
  const lang = getLang();
  // New bilingual format
  if (section.cs || section.en) {
    return section[lang] ?? section.cs ?? {};
  }
  // Legacy format — just return as-is
  return section;
}

export async function loadSluzby() {
  try {
    const data = await fetchContent();

    // --- SLUZBY section ---
    const sluzby = pick(data.sluzby);
    const proc   = pick(data.proc_za_mnou);
    const omne   = pick(data.o_mne);

    const nadpisEl = document.getElementById('sluzby-nadpis');
    if (nadpisEl) nadpisEl.textContent = sluzby.nadpis || 'Léčba bolestí zad';

    const procNadpisEl = document.getElementById('proc-nadpis');
    if (procNadpisEl) procNadpisEl.textContent = proc.nadpis || 'Proč za mnou?';

    const sluzbyList = document.getElementById('sluzby-list');
    if (sluzbyList) {
      const lines = (sluzby.text || '').split('\n').filter(l => l.trim());
      sluzbyList.innerHTML = lines.map(line => {
        const clean = line.replace(/^\d+\.\s*/, '');
        return `<div class="sluzby-item">${clean}</div>`;
      }).join('') || '<div class="sluzby-item">Výhřezlé ploténky</div>';
    }

    const procList = document.getElementById('proc-list');
    if (procList) {
      procList.innerHTML = (proc.body || []).map(item => `<li>${item}</li>`).join('');
    }

    // --- O MNE section ---
    const omneNadpisEl = document.getElementById('omne-nadpis');
    if (omneNadpisEl) omneNadpisEl.textContent = omne.nadpis || 'O mně';

    const omneTextEl = document.getElementById('omne-text');
    if (omneTextEl) omneTextEl.textContent = omne.text || '';

    const omneList = document.getElementById('omne-list');
    if (omneList) {
      omneList.innerHTML = (omne.body || []).map(item => `<li>${item}</li>`).join('');
    }

    // --- O MNE foto ---
    const omneImg    = document.getElementById('omne-foto');
    const omneImgWrap = document.getElementById('omne-foto-wrap');
    if (omneImg && omne.foto) {
      omneImg.src = omne.foto;
      omneImg.alt = omne.nadpis || 'O mně';
      if (omneImgWrap) omneImgWrap.style.display = 'block';
    } else if (omneImgWrap && !omne.foto) {
      omneImgWrap.style.display = 'none';
    }

  } catch (e) {
    console.error('Chyba načítání obsahu:', e);
  }
}