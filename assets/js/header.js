document.addEventListener('DOMContentLoaded', () => {
  // Toggle mobile menu
  const menuToggle = document.querySelector('.menu-toggle');
  const menuClose = document.querySelector('.menu-close');
  const menuHeader = document.querySelector('.menu_header');
  menuToggle?.addEventListener('click', () => menuHeader.classList.toggle('active'));
  menuClose?.addEventListener('click', () => menuHeader.classList.remove('active'));
});
