document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const currentPage = params.get('page') || 'taikhoan';
    document.querySelectorAll('aside .list-group-item').forEach(item => {
      if (item.getAttribute('href').includes(`page=${currentPage}`) ||
          (currentPage === 'taikhoan' && item.textContent.includes('Thông tin tài khoản'))) {
        item.classList.add('active');
      }
    });
});
  