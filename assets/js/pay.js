document.addEventListener('DOMContentLoaded', () => {
  // --- Address option toggle ---
  const savedRadio = document.getElementById('addr_saved');
  const newRadio = document.getElementById('addr_new');
  const savedContainer = document.getElementById('saved-container');
  const newContainer = document.getElementById('new-container');
  function toggleAddress() {
    if (savedRadio.checked) {
      savedContainer.style.display = 'block';
      newContainer.style.display = 'none';
    } else {
      savedContainer.style.display = 'none';
      newContainer.style.display = 'block';
    }
  }
  savedRadio.addEventListener('change', toggleAddress);
  newRadio.addEventListener('change', toggleAddress);
  toggleAddress();

  // --- Prefill email ---
  if (window.currentUser) {
    const emailFld = document.getElementById('email');
    if (emailFld) emailFld.value = window.currentUser.email || '';
  }

  // --- Cart & summary ---
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  let subtotal = 0;
  let discount = 0;
  const shippingFee = 30000;
  const orderItemsEl = document.getElementById('order-items');

  cart.forEach(item => {
    subtotal += item.price * item.quantity;
    const div = document.createElement('div');
    div.className = 'd-flex border-bottom py-2 align-items-center';
    const imgSrc = item.image?.includes('/') ? item.image : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
    div.innerHTML = `
      <img src="${imgSrc}" class="me-2 rounded" style="width:60px;height:60px;object-fit:cover;">
      <div class="flex-grow-1">
        <p class="mb-0 fw-bold">${item.name}</p>
        <small>${item.color || 'Màu'} - ${item.size || 'Size'}</small>
        <div><small><strong>Số lượng: ${item.quantity}</strong></small></div>
        <p class="text-danger fw-bold mb-0">${(item.price * item.quantity).toLocaleString()}đ</p>
      </div>`;
    orderItemsEl.appendChild(div);
  });

  document.getElementById('subtotal').textContent = subtotal.toLocaleString() + 'đ';
  document.querySelector('.list-group-item:nth-child(2) span:nth-child(2)').textContent = shippingFee.toLocaleString() + 'đ';
  document.getElementById('total').textContent = (subtotal + shippingFee - discount).toLocaleString() + 'đ';

  // --- Voucher logic ---
  const voucherInput = document.querySelector('.input-group input');
  const applyVoucherBtn = document.querySelector('.input-group button');
  applyVoucherBtn.addEventListener('click', () => {
    const code = voucherInput.value.trim().toUpperCase();
    if (code === 'HUYDEPTRAI') discount = subtotal * 0.1;
    else if (code === 'MINHHUY') discount = shippingFee;
    else { discount = 0; alert('Mã giảm giá không hợp lệ!'); }
    document.querySelector('.list-group-item:nth-child(3) span:nth-child(2)').textContent = discount > 0 ? '-' + discount.toLocaleString() + 'đ' : '0đ';
    document.getElementById('total').textContent = (subtotal + shippingFee - discount).toLocaleString() + 'đ';
  });

  // --- QR code section ---
  const paymentContainer = document.querySelector('.border.p-3.rounded');
  const qrSection = document.createElement('div');
  qrSection.id = 'qr-section'; qrSection.className = 'mt-3';
  paymentContainer.appendChild(qrSection);
  paymentContainer.style.position = 'relative';
  document.querySelectorAll("input[name='payment_method']").forEach(radio => {
    radio.addEventListener('change', () => {
      const id = Number(radio.value);
      if (id === 2) qrSection.innerHTML = `<div class='text-center'><p>Quét mã QR ngân hàng</p><img src='./assets/img/pay/qr-bank.png' width='150'></div>`;
      else if (id === 3) qrSection.innerHTML = `<div class='text-center'><p>Quét mã QR Momo</p><img src='./assets/img/pay/qr-momo.png' width='150'></div>`;
      else qrSection.innerHTML = '';
    });
  });

  // --- Province/District/Ward for new address ---
  const provinceSelect = document.getElementById('province');
  const districtSelect = document.getElementById('district');
  const wardSelect = document.getElementById('ward');

  fetch('https://provinces.open-api.vn/api/p/')
    .then(res => res.json())
    .then(data => {
      provinceSelect.innerHTML = '<option selected disabled>Tỉnh/TP</option>';
      data.forEach(p => {
        const name = p.name.replace(/^Tỉnh |^Thành phố /, '');
        provinceSelect.add(new Option(name, p.code));
      });
    });

  provinceSelect.addEventListener('change', () => {
    districtSelect.innerHTML = '<option selected disabled>Quận/Huyện</option>';
    wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
    fetch(`https://provinces.open-api.vn/api/p/${provinceSelect.value}?depth=2`)
      .then(res => res.json())
      .then(obj => {
        obj.districts.forEach(d => districtSelect.add(new Option(d.name, d.code)));
      });
  });

  districtSelect.addEventListener('change', () => {
    wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
    fetch(`https://provinces.open-api.vn/api/d/${districtSelect.value}?depth=2`)
      .then(res => res.json())
      .then(obj => {
        obj.wards.forEach(w => wardSelect.add(new Option(w.name, w.code)));
      });
  });

  // --- Form Validation & Submit ---
  const nameFld = document.getElementById('name');
  const phoneFld = document.getElementById('sdt');
  const emailFld = document.getElementById('email');

  document.querySelectorAll('.form-control').forEach(el => el.addEventListener('input', () => el.classList.remove('is-invalid')));
  document.querySelectorAll('.form-select').forEach(el => el.addEventListener('change', () => el.classList.remove('is-invalid')));

  window.submitOrder = function() {
    let valid = true;
    if (!nameFld.value.trim()) { nameFld.classList.add('is-invalid'); valid = false; }
    if (!/^0\d{9}$/.test(phoneFld.value.trim())) { phoneFld.classList.add('is-invalid'); valid = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailFld.value.trim())) { emailFld.classList.add('is-invalid'); valid = false; }
    if (newRadio.checked) {
      if (!provinceSelect.value) { provinceSelect.classList.add('is-invalid'); valid = false; }
      if (!districtSelect.value) { districtSelect.classList.add('is-invalid'); valid = false; }
      if (!wardSelect.value) { wardSelect.classList.add('is-invalid'); valid = false; }
    }
    if (!valid) return;

    const payload = {
      name: nameFld.value.trim(),
      sdt: phoneFld.value.trim(),
      email: emailFld.value.trim(),
      saved_address: savedRadio.checked ? document.getElementById('saved-address').value : null,
      province: newRadio.checked ? provinceSelect.value : null,
      district: newRadio.checked ? districtSelect.value : null,
      ward: newRadio.checked ? wardSelect.value : null,
      address: newRadio.checked ? document.getElementById('specific-address').value.trim() : null,
      cart, discount, shippingFee,
      total: subtotal + shippingFee - discount,
      payment_method: document.querySelector("input[name='payment_method']:checked").value
    };

    fetch('../ajax/save_order.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(res => {
      if (res.success) {
        alert('Đặt hàng thành công!');
        localStorage.removeItem('cart');
        window.location = 'index.php';
      } else {
        alert('Lỗi: ' + res.message);
      }
    });
  };

});