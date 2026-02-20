import { API } from './utils/config.js';
import { showSection, initNav } from './utils/nav.js';
import { loadSluzby } from './pages/sluzby.js';
import { loadGallery, initLightbox } from './pages/gallery.js';
import { loadReviews, initReviewForm } from './pages/reviews.js';
import { initContact } from './pages/contact.js';
import { initHeroSlider } from './pages/hero.js';

// Boot
initNav();
initLightbox();
initReviewForm();
initContact();
initHeroSlider();

// Show hero on load
showSection('hero');