document.addEventListener('DOMContentLoaded', () => {
  // --- Highlight menu đang chọn ---
  const params = new URLSearchParams(window.location.search);
  const currentPage = params.get('page') || 'taikhoan';
  document.querySelectorAll('aside .list-group-item').forEach(item => {
    if (
      item.getAttribute('href')?.includes(`page=${currentPage}`) ||
      (currentPage === 'taikhoan' && item.textContent.includes('Thông tin tài khoản'))
    ) {
      item.classList.add('active');
    }
  });

  // --- Reset form đổi mật khẩu khi mở modal ---
  const modalChange = document.getElementById('modalChangePassword');
  modalChange?.addEventListener('show.bs.modal', () => {
    const form = document.getElementById('formChangePassword');
    form?.reset();
    form?.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form?.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

    document.querySelectorAll('#formChangePassword .input-group-text').forEach(span => {
      const icon = span.querySelector('i');
      const input = span.closest('.input-group').querySelector('input');
  
      span.style.cursor = 'pointer';
      span.onclick = () => {
        if (!input || !icon) return;
        if (input.type === 'password') {
          input.type = 'text';
          icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
          input.type = 'password';
          icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
      };
    });
  });

  // --- Show toast ---
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
    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    container.appendChild(toastEl);
    new bootstrap.Toast(toastEl, { delay: 3000 }).show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  }

  // --- Cập nhật thông tin cá nhân ---
  const formEdit = document.getElementById('formEditProfile');
  formEdit?.addEventListener('submit', async e => {
    e.preventDefault();
    const nameField = formEdit.querySelector('#editName');
    const phoneField = formEdit.querySelector('#editPhone');
    const name = nameField.value.trim();
    const phone = phoneField.value.trim();

    [nameField, phoneField].forEach(f => {
      f.classList.remove('is-invalid');
      f.nextElementSibling.textContent = '';
    });

    if (!name) {
      nameField.classList.add('is-invalid');
      nameField.nextElementSibling.textContent = 'Họ và tên không được để trống';
      return;
    }
    if (!/^[\p{L}]+(?: [\p{L}]+)+$/u.test(name)) {
      nameField.classList.add('is-invalid');
      nameField.nextElementSibling.textContent = 'Họ và tên phải gồm ít nhất hai từ';
      return;
    }
    if (!/^0\d{9}$/.test(phone)) {
      phoneField.classList.add('is-invalid');
      phoneField.nextElementSibling.textContent = 'Số điện thoại không hợp lệ';
      return;
    }

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
      showToast('Lỗi kết nối máy chủ');
    }
  });

  // --- Xử lý đổi mật khẩu ---
  const formPass = document.getElementById('formChangePassword');
  formPass?.addEventListener('submit', async e => {
    e.preventDefault();
    const oldFld = formPass.querySelector('#oldPassword');
    const newFld = formPass.querySelector('#newPassword');
    const confFld = formPass.querySelector('#confirmPassword');

    const oldPw = oldFld.value.trim();
    const newPw = newFld.value.trim();
    const confPw = confFld.value.trim();

    [oldFld, newFld, confFld].forEach(f => {
      f.classList.remove('is-invalid');
      f.nextElementSibling.textContent = '';
    });

    if (!oldPw) {
      oldFld.classList.add('is-invalid');
      oldFld.nextElementSibling.textContent = 'Vui lòng nhập mật khẩu cũ';
      return;
    }

    if (!newPw) {
      newFld.classList.add('is-invalid');
      newFld.nextElementSibling.textContent = 'Vui lòng nhập mật khẩu mới';
      return;
    }

    if (newPw !== confPw) {
      confFld.classList.add('is-invalid');
      confFld.nextElementSibling.textContent = 'Mật khẩu xác nhận không khớp';
      return;
    }

    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(newPw)) {
      newFld.classList.add('is-invalid');
      newFld.nextElementSibling.textContent =
        'Mật khẩu phải ≥8 ký tự, có hoa, thường, số & ký tự đặc biệt';
      return;
    }

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
        showToast('Đổi mật khẩu thành công');
      } else {
        showToast(result.message || 'Đổi mật khẩu thất bại');
      }
    } catch (err) {
      console.error(err);
      showToast('Lỗi máy chủ khi đổi mật khẩu');
    }
  });
});
