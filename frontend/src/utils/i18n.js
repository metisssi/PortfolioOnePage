export const translations = {
  cs: {
    nav_home: 'Domů',
    nav_services: 'Služby',
    nav_about: 'O mně',
    nav_gallery: 'Galerie',
    nav_reviews: 'Recenze',
    nav_contact: 'Kontakt',

    hero_title_1: 'Zbavíme vás',
    hero_title_2: 'bolesti zad',
    hero_sub: 'Trápí vás bolesti? Naše metoda pomůže i tam, kde jiní neuspěli.<br>Bez operace. Bez léků. S výsledky.',
    hero_btn: 'Zavolat — konzultace zdarma',
    hero_img1: 'Trápí vás záda?',
    hero_img2: 'Pomůžeme vám.',

    tag_services: 'Co léčím',
    tag_about: 'Kdo jsem',
    tag_gallery: 'Naše prostory',
    tag_reviews: 'Co říkají pacienti',
    tag_contact: 'Spojte se s námi',

    title_gallery: 'Galerie',
    title_reviews: 'Recenze',
    title_contact: 'Kontakt',

    sluzby_intro: 'Nabízím léčení bolestí zad včetně onemocnění páteře. Každý případ je individuální a přistupuji ke každému pacientovi s maximální péčí.',

    stat_years: 'Let zkušeností',
    stat_patients: 'Spokojených pacientů',
    stat_success: 'Úspěšnost léčby',

    gallery_empty: 'Galerie je zatím prázdná.',

    reviews_empty: 'Zatím žádné schválené recenze. Buďte první!',
    review_form_title: 'Napište recenzi',
    review_name: 'Jméno',
    review_surname: 'Příjmení',
    review_email: 'E-mail',
    review_text: 'Vaše zkušenost...',
    review_submit: 'Odeslat recenzi',
    review_success: 'Recenze odeslána ke schválení',
    review_error: 'Chyba při odesílání. Zkuste to znovu.',
    ext_reviews_title: 'Další recenze najdete zde',

    contact_phone_label: 'Telefon',
    contact_email_label: 'E-mail',
    contact_address_label: 'Adresa',
    contact_cta: 'Zavolat nyní',

    footer: '© 2025 Léčba bolestí zad. Všechna práva vyhrazena.',
  },

  en: {
    nav_home: 'Home',
    nav_services: 'Services',
    nav_about: 'About',
    nav_gallery: 'Gallery',
    nav_reviews: 'Reviews',
    nav_contact: 'Contact',

    hero_title_1: "We'll relieve your",
    hero_title_2: 'back pain',
    hero_sub: 'Struggling with pain? Our method works even where others have failed.<br>No surgery. No drugs. Real results.',
    hero_btn: 'Call — free consultation',
    hero_img1: 'Suffering from back pain?',
    hero_img2: 'We can help you.',

    tag_services: 'What I treat',
    tag_about: 'Who I am',
    tag_gallery: 'Our spaces',
    tag_reviews: 'What patients say',
    tag_contact: 'Get in touch',

    title_gallery: 'Gallery',
    title_reviews: 'Reviews',
    title_contact: 'Contact',

    sluzby_intro: 'I offer treatment for back pain including spinal conditions. Every case is individual and I approach each patient with maximum care.',

    stat_years: 'Years of experience',
    stat_patients: 'Satisfied patients',
    stat_success: 'Treatment success rate',

    gallery_empty: 'Gallery is currently empty.',

    reviews_empty: 'No approved reviews yet. Be the first!',
    review_form_title: 'Write a review',
    review_name: 'First name',
    review_surname: 'Last name',
    review_email: 'E-mail',
    review_text: 'Your experience...',
    review_submit: 'Submit review',
    review_success: 'Review submitted for approval',
    review_error: 'Error submitting. Please try again.',
    ext_reviews_title: 'More reviews can be found here',

    contact_phone_label: 'Phone',
    contact_email_label: 'E-mail',
    contact_address_label: 'Address',
    contact_cta: 'Call now',

    footer: '© 2025 Back Pain Treatment. All rights reserved.',
  }
};

export function getLang() {
  return localStorage.getItem('lang') || 'cs';
}

export function setLang(lang) {
  localStorage.setItem('lang', lang);
}

export function t(key) {
  const lang = getLang();
  return translations[lang]?.[key] ?? translations['cs'][key] ?? key;
}

export function applyTranslations() {
  const lang = getLang();
  const tr = translations[lang];

  // Nav buttons
  const navMap = {
    hero:     'nav_home',
    sluzby:   'nav_services',
    'o-mne':  'nav_about',
    gallery:  'nav_gallery',
    reviews:  'nav_reviews',
    contact:  'nav_contact',
  };
  document.querySelectorAll('.nav-btn[data-section]').forEach(btn => {
    const key = navMap[btn.dataset.section];
    if (key) btn.textContent = tr[key];
  });

  // data-i18n elements (innerHTML so <br> works)
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n;
    if (tr[key] !== undefined) el.innerHTML = tr[key];
  });

  // Placeholders
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.dataset.i18nPlaceholder;
    if (tr[key] !== undefined) el.placeholder = tr[key];
  });

  // Lang toggle button state
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });

  // html lang attribute
  document.documentElement.lang = lang === 'en' ? 'en' : 'cs';
}