const API = 'http://localhost:5000/api';

const sections = ['hero', 'sluzby', 'gallery', 'reviews', 'contact'];

function showSection(id) {
  sections.forEach(s => {
    const el = document.getElementById(s);
    el.classList.remove('active', 'visible');
  });

  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.section === id);
  });

  const target = document.getElementById(id);
  target.classList.add('active');
  window.scrollTo(0, 0);

  setTimeout(() => {
    target.classList.add('visible');
    if (id === 'sluzby') loadSluzby();
    if (id === 'gallery') loadGallery();
    if (id === 'reviews') loadReviews();
  }, 10);

  document.querySelector('.nav-links').classList.remove('open');
}

window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 30);
});

document.querySelectorAll('.nav-btn').forEach(btn => {
  btn.addEventListener('click', () => showSection(btn.dataset.section));
});

document.querySelectorAll('[data-section]').forEach(el => {
  if (!el.classList.contains('nav-btn')) {
    el.addEventListener('click', () => showSection(el.dataset.section));
  }
});

document.getElementById('hamburger').addEventListener('click', () => {
  document.querySelector('.nav-links').classList.toggle('open');
});

async function loadSluzby() {
  try {
    const res = await fetch(`${API}/content`);
    const data = await res.json();

    document.getElementById('sluzby-nadpis').textContent = data.sluzby?.nadpis || 'Léčba bolestí zad';
    document.getElementById('proc-nadpis').textContent = data.proc_za_mnou?.nadpis || 'Proč za mnou?';

    const sluzbyList = document.getElementById('sluzby-list');
    const lines = (data.sluzby?.text || '').split('\n').filter(l => l.trim());
    sluzbyList.innerHTML = lines.map(line => {
      const clean = line.replace(/^\d+\.\s*/, '');
      return `<div class="sluzby-item">${clean}</div>`;
    }).join('');

    const procList = document.getElementById('proc-list');
    procList.innerHTML = (data.proc_za_mnou?.body || []).map(item => `<li>${item}</li>`).join('');
  } catch (e) {
    console.error('Chyba obsahu:', e);
  }
}

async function loadGallery() {
  try {
    const res = await fetch(`${API}/gallery`);
    const items = await res.json();
    const grid = document.getElementById('gallery-grid');
    const empty = document.getElementById('gallery-empty');

    if (!items.length) { empty.classList.remove('hidden'); return; }

    empty.classList.add('hidden');
    grid.innerHTML = items.map(item => `
      <div class="gallery-item" onclick="${item.url}', '${item.popis}')">
        <img src="${item.url}}" alt="${item.popis || ''}" loading="lazy">
        ${item.popis ? `<div class="gallery-caption">${item.popis}</div>` : ''}
      </div>
    `).join('');
  } catch (e) {
    console.error('Chyba galerie:', e);
  }
}

window.openLightbox = (url, popis) => {
  document.getElementById('lightbox-img').src = url;
  document.getElementById('lightbox-popis').textContent = popis || '';
  document.getElementById('lightbox').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
  document.getElementById('lightbox').classList.add('hidden');
  document.body.style.overflow = '';
};

document.getElementById('lightbox-close').addEventListener('click', closeLightbox);
document.getElementById('lightbox-close-btn').addEventListener('click', closeLightbox);
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

async function loadReviews() {
  try {
    const res = await fetch(`${API}/reviews`);
    const items = await res.json();
    const grid = document.getElementById('reviews-grid');

    if (!items.length) {
      grid.innerHTML = `<p style="color: var(--gray); grid-column: 1/-1;">Zatím žádné recenze. Buďte první!</p>`;
      return;
    }

    grid.innerHTML = items.map(r => `
      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">${r.text}</p>
        <p class="review-author">${r.jmeno} ${r.prijmeni}</p>
      </div>
    `).join('');
  } catch (e) {
    console.error('Chyba recenzí:', e);
  }
}

document.getElementById('review-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('review-msg');

  try {
    const res = await fetch(`${API}/reviews`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        jmeno: form.jmeno.value,
        prijmeni: form.prijmeni.value,
        email: form.email.value,
        text: form.text.value
      })
    });
    const result = await res.json();
    msg.classList.remove('hidden', 'error');
    msg.classList.add('success');
    msg.textContent = result.message;
    form.reset();
  } catch {
    msg.classList.remove('hidden');
    msg.classList.add('error');
    msg.textContent = 'Chyba při odesílání.';
  }
});

showSection('hero');