export function initHeroSlider() {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  if (slides.length < 2) return;

  let current = 0;
  let interval = null;

  function goToSlide(index) {
    if (index === current) return;

    // Mark current as leaving
    slides[current].classList.remove('active');
    slides[current].classList.add('leaving');

    // After leaving transition, remove class
    const prev = current;
    setTimeout(() => {
      slides[prev].classList.remove('leaving');
    }, 1800);

    // Activate new slide
    current = index;
    slides[current].classList.add('active');

    // Update dots
    dots.forEach((d, i) => d.classList.toggle('active', i === current));
  }

  function nextSlide() {
    goToSlide((current + 1) % slides.length);
  }

  function startAutoplay() {
    stopAutoplay();
    interval = setInterval(nextSlide, 6000);
  }

  function stopAutoplay() {
    if (interval) clearInterval(interval);
  }

  // Dot click handlers
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      goToSlide(Number(dot.dataset.slide));
      startAutoplay(); // reset timer on manual click
    });
  });

  // Start cycling
  startAutoplay();

  // Pause on hover (optional for better UX)
  const hero = document.getElementById('hero');
  if (hero) {
    hero.addEventListener('mouseenter', stopAutoplay);
    hero.addEventListener('mouseleave', startAutoplay);
  }
}