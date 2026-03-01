import { API } from '../utils/config.js';
import { t } from '../utils/i18n.js';

export async function loadReviews() {
  try {
    const res = await fetch(`${API}/reviews`);
    const items = await res.json();
    const grid = document.getElementById('reviews-grid');
    if (!grid) return;

    if (!items.length) {
      grid.innerHTML = `<p style="color:var(--gray);grid-column:1/-1;padding:2rem 0">${t('reviews_empty')}</p>`;
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
      if (msg) {
        msg.classList.remove('hidden', 'error');
        msg.classList.add('success');
        msg.textContent = t('review_success');
      }
      form.reset();
    } catch {
      if (msg) {
        msg.classList.remove('hidden');
        msg.classList.add('error');
        msg.textContent = t('review_error');
      }
    }
  });
}