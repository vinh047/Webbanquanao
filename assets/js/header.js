document.addEventListener('DOMContentLoaded', () => {
  // Toggle mobile menu
  const menuToggle = document.querySelector('.menu-toggle');
  const menuClose = document.querySelector('.menu-close');
  const menuHeader = document.querySelector('.menu_header');
  menuToggle?.addEventListener('click', () => menuHeader.classList.toggle('active'));
  menuClose?.addEventListener('click', () => menuHeader.classList.remove('active'));

  // User-menu dropdown
  const accountLink = document.getElementById('account-link');
  const userMenu = document.getElementById('user-menu');

  if (accountLink && userMenu) {
    accountLink.addEventListener('click', e => {
      e.preventDefault();                 // không redirect
      userMenu.classList.toggle('visible');
    });
    // Click bất kỳ chỗ khác thì đóng menu
    document.addEventListener('click', e => {
      if (!accountLink.contains(e.target) && !userMenu.contains(e.target)) {
        userMenu.classList.remove('visible');
      }
    });
  }
});
