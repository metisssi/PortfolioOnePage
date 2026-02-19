import { API } from '../utils/config.js';

export async function loadReviews() {
  try {
    const res = await fetch(`${API}/reviews`);
    const items = await res.json();
    const grid = document.getElementById('reviews-grid');
    if (!grid) return;

    if (!items.length) {
      grid.innerHTML = `<p style="color:var(--gray);grid-column:1/-1;padding:2rem 0">
        Zatím žádné schválené recenze. Buďte první!
      </p>`;
      return;
    }

    grid.innerHTML = items.map(r => `
      <div class="review-card">
        <div class="review-stars">★★★★★</div>
        <p class="review-text">${r.text}</p>
        <p class="review-author">— ${r.jmeno} ${r.prijmeni}</p>
      </div>
    `).join('');
  } catch (e) {
    console.error('Chyba recenzí:', e);
  }
}

export function initReviewForm() {
  document.getElementById('review-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const msg = document.getElementById('review-msg');

    try {
      const res = await fetch(`${API}/reviews`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          jmeno:    form.jmeno.value,
          prijmeni: form.prijmeni.value,
          email:    form.email.value,
          text:     form.text.value
        })
      });
      const result = await res.json();
      if (msg) {
        msg.classList.remove('hidden', 'error');
        msg.classList.add('success');
        msg.textContent = result.message;
      }
      form.reset();
    } catch {
      if (msg) {
        msg.classList.remove('hidden');
        msg.classList.add('error');
        msg.textContent = 'Chyba při odesílání. Zkuste to znovu.';
      }
    }
  });
}