import { showSection, initNav } from './utils/nav.js';
import { loadSluzby } from './pages/sluzby.js';
import { loadGallery, initLightbox } from './pages/gallery.js';
import { loadReviews, initReviewForm } from './pages/reviews.js';

// Boot
initNav();
initLightbox();
initReviewForm();