const carouselTrack = document.querySelector('.carousel-track');
let isDragging = false;
let startX;
let scrollLeft;

carouselTrack.addEventListener('mousedown', (e) => {
  isDragging = true;
  startX = e.pageX - carouselTrack.offsetLeft;
  scrollLeft = carouselTrack.scrollLeft;
  carouselTrack.style.cursor = 'grabbing';
});

carouselTrack.addEventListener('mouseleave', () => {
  isDragging = false;
  carouselTrack.style.cursor = 'grab';
});

carouselTrack.addEventListener('mouseup', () => {
  isDragging = false;
  carouselTrack.style.cursor = 'grab';
});

carouselTrack.addEventListener('mousemove', (e) => {
  if (!isDragging) return;
  e.preventDefault();
  const x = e.pageX - carouselTrack.offsetLeft;
  const walk = (x - startX) * 3; // Scroll sensitivity
  carouselTrack.scrollLeft = scrollLeft - walk;
});
