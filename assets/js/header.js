document.addEventListener('DOMContentLoaded', () => {
  // 1) Toggle mobile menu
  const menuToggle = document.querySelector('.menu-toggle');
  const menuClose  = document.querySelector('.menu-close');
  const menuHeader = document.querySelector('.menu_header');
  menuToggle?.addEventListener('click', () => menuHeader.classList.toggle('active'));
  menuClose?.addEventListener('click',  () => menuHeader.classList.remove('active'));

  // 2) Xử lý logout
  const params = new URLSearchParams(window.location.search);
  if (params.get('loggedout') === '1') {
    localStorage.removeItem('cart');
    if (typeof updateCartCount === 'function') {
      updateCartCount();
    } else {
      const b = document.getElementById('cart-count-badge');
      if (b) b.textContent = '0';
    }
    params.delete('loggedout');
    history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
  }
});
