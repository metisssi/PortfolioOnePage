import { API } from '../utils/config.js';

export async function initContact() {
  // Load phone from content if present
  try {
    const res = await fetch(`${API}/content`);
    const data = await res.json();

    const phone = data.sluzby?.telefon;
    if (phone) {
      const phoneWrap = document.getElementById('contact-phone-wrap');
      const phoneEl   = document.getElementById('contact-phone');
      if (phoneWrap && phoneEl) {
        phoneEl.textContent = phone;
        phoneEl.href = `tel:${phone.replace(/\s/g, '')}`;
        phoneWrap.style.display = 'flex';
      }
      // call button
      const callBtn = document.getElementById('call-btn');
      if (callBtn) {
        callBtn.textContent = `Zavolat: ${phone}`;
        callBtn.addEventListener('click', () => {
          window.location.href = `tel:${phone.replace(/\s/g, '')}`;
        });
      }
    } else {
      // no phone yet — hide call button
      const callBtn = document.getElementById('call-btn');
      if (callBtn) callBtn.style.display = 'none';
    }

  } catch (e) {
    console.error('Chyba kontaktu:', e);
    const callBtn = document.getElementById('call-btn');
    if (callBtn) callBtn.style.display = 'none';
  }
}