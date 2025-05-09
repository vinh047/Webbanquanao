document.addEventListener('DOMContentLoaded', () => {
  // Highlight active menu item in sidebar
  const params = new URLSearchParams(window.location.search);
  const currentPage = params.get('page') || 'taikhoan';
  document.querySelectorAll('aside .list-group-item').forEach(item => {
    if (
      item.getAttribute('href').includes(`page=${currentPage}`) ||
      (currentPage === 'taikhoan' && item.textContent.includes('Thông tin tài khoản'))
    ) {
      item.classList.add('active');
    }
  });

  // Utility: show toast for server-side messages
  function showToast(message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'position-fixed top-0 end-0 p-3';
      document.body.appendChild(container);
    }
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center text-white bg-primary border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    container.appendChild(toastEl);
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  }

  // Toggle password visibility
  document.querySelectorAll('.input-group-text i').forEach(icon => {
    icon.style.cursor = 'pointer';
    icon.addEventListener('click', () => {
      const input = icon.closest('.input-group').querySelector('input');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      }
    });
  });

  // Edit Profile form validation and submission
  const formEdit = document.getElementById('formEditProfile');
  formEdit.setAttribute('novalidate', '');
  formEdit.addEventListener('submit', async e => {
    e.preventDefault();
    const nameField = document.getElementById('editName');
    const phoneField = document.getElementById('editPhone');
    const name = nameField.value.trim();
    const phone = phoneField.value.trim();

    // Reset previous validation
    [nameField, phoneField].forEach(field => {
      field.classList.remove('is-invalid');
      const feedback = field.nextElementSibling;
      if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = '';
      }
    });

    // Required checks
    if (!name) {
      nameField.classList.add('is-invalid');
      nameField.nextElementSibling.textContent = 'Họ và tên không được để trống';
      nameField.focus();
      return;
    }
    if (!phone) {
      phoneField.classList.add('is-invalid');
      phoneField.nextElementSibling.textContent = 'Số điện thoại không được để trống';
      phoneField.focus();
      return;
    }

    // Name: at least 2 words
    if (!/^[\p{L}]+(?: [\p{L}]+)+$/u.test(name)) {
      nameField.classList.add('is-invalid');
      nameField.nextElementSibling.textContent = 'Họ và tên phải gồm ít nhất hai từ';
      nameField.focus();
      return;
    }

    // Phone: starts 0 + 9 digits
    if (!/^0\d{9}$/.test(phone)) {
      phoneField.classList.add('is-invalid');
      phoneField.nextElementSibling.textContent = 'Số điện thoại phải 10 chữ số, bắt đầu 0';
      phoneField.focus();
      return;
    }

    // Submit data via AJAX
    try {
      const res = await fetch('/ajax/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, phone })
      });
      const result = await res.json();
      if (result.success) {
        bootstrap.Modal.getInstance(document.getElementById('modalEditProfile')).hide();
        location.reload();
      } else {
        showToast(result.message || 'Cập nhật thất bại');
      }
    } catch (err) {
      console.error(err);
      showToast('Lỗi kết nối');
    }
  });

  // Change Password form submission with client-side validation
  const formPass = document.getElementById('formChangePassword');
  formPass.setAttribute('novalidate', '');

  // Lắng nghe submit
  formPass.addEventListener('submit', async e => {
    e.preventDefault();
    const oldFld = document.getElementById('oldPassword');
    const newFld = document.getElementById('newPassword');
    const confFld = document.getElementById('confirmPassword');
    const oldPw = oldFld.value.trim();
    const newPw = newFld.value.trim();
    const confPw = confFld.value.trim();

    // Reset invalid
    [oldFld, newFld, confFld].forEach(f => {
      f.classList.remove('is-invalid');
      f.nextElementSibling.textContent = '';
    });

    // Required
    if (!oldPw) {
      oldFld.classList.add('is-invalid');
      oldFld.nextElementSibling.textContent = 'Vui lòng nhập mật khẩu cũ';
      oldFld.focus();
      return;
    }
    if (!newPw) {
      newFld.classList.add('is-invalid');
      newFld.nextElementSibling.textContent = 'Vui lòng nhập mật khẩu mới';
      newFld.focus();
      return;
    }
    if (!confPw) {
      confFld.classList.add('is-invalid');
      confFld.nextElementSibling.textContent = 'Vui lòng xác nhận mật khẩu mới';
      confFld.focus();
      return;
    }

    // Match
    if (newPw !== confPw) {
      const confirmField = document.getElementById('confirmPassword');
      confirmField.classList.add('is-invalid');
      document.getElementById('confirmPasswordFeedback').textContent =
        'Mật khẩu xác nhận không khớp';
      confirmField.focus();
      return;
    }


    // Strength
    const pwdRe = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;
    if (!pwdRe.test(newPw)) {
      newFld.classList.add('is-invalid');
      newFld.nextElementSibling.textContent =
        'Mật khẩu phải ≥8 ký tự, có hoa, thường, số & ký tự đặc biệt';
      newFld.focus();
      return;
    }

    // Gửi AJAX
    try {
      const res = await fetch('/ajax/change_password.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ old_password: oldPw, new_password: newPw })
      });
      const result = await res.json();
      if (result.success) {
        bootstrap.Modal.getInstance(document.getElementById('modalChangePassword')).hide();
        formPass.reset();
        // Có thể show toast success ở đây nếu muốn
      } else {
        // Lỗi server: toast vẫn ok
        showToast(result.message || 'Đổi mật khẩu thất bại');
      }
    } catch (err) {
      console.error(err);
      showToast('Lỗi kết nối');
    }
  });

});