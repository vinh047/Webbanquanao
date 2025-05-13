const openBtn = document.getElementById('openSearch');
const closeBtn = document.getElementById('closeSearch');
const overlay = document.getElementById('searchOverlay');

openBtn.addEventListener('click', function(e) {
  e.preventDefault();
  overlay.style.display = 'flex';
});

closeBtn.addEventListener('click', function() {
  overlay.style.display = 'none';
});

document.addEventListener('click', function(e) {
  if (overlay.style.display === 'flex' && !e.target.closest('.search-box') && !e.target.closest('#openSearch')) {
    overlay.style.display = 'none';
  }
});