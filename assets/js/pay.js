// assets/js/pay.js

document.addEventListener('DOMContentLoaded', () => {
  // --- Address option toggle ---
  const savedRadio      = document.getElementById('addr_saved');
  const newRadio        = document.getElementById('addr_new');
  const savedContainer  = document.getElementById('saved-container');
  const newContainer    = document.getElementById('new-container');

  function toggleAddress() {
    if (savedRadio.checked) {
      savedContainer.style.display = 'block';
      newContainer.style.display   = 'none';
    } else {
      savedContainer.style.display = 'none';
      newContainer.style.display   = 'block';
    }
  }
  savedRadio.addEventListener('change', toggleAddress);
  newRadio.addEventListener('change',   toggleAddress);
  toggleAddress();

  // --- Prefill email ---
  if (window.currentUser) {
    const emailFld = document.getElementById('email');
    if (emailFld) emailFld.value = window.currentUser.email || '';
  }

  // --- Province/District/Ward for new address ---
  const provinceSelect = document.getElementById('province');
  const districtSelect = document.getElementById('district');
  const wardSelect     = document.getElementById('ward');

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
    wardSelect.innerHTML     = '<option selected disabled>Phường/Xã</option>';
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
  const nameFld  = document.getElementById('name');
  const phoneFld = document.getElementById('sdt');
  const emailFld = document.getElementById('email');

  document.querySelectorAll('.form-control')
    .forEach(el => el.addEventListener('input', () => el.classList.remove('is-invalid')));
  document.querySelectorAll('.form-select')
    .forEach(el => el.addEventListener('change', () => el.classList.remove('is-invalid')));

  window.submitOrder = function() {
    let valid = true;
    if (!nameFld.value.trim())                       { nameFld.classList.add('is-invalid'); valid = false; }
    if (!/^0\d{9}$/.test(phoneFld.value.trim()))     { phoneFld.classList.add('is-invalid'); valid = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailFld.value.trim())) {
      emailFld.classList.add('is-invalid'); valid = false;
    }
    if (newRadio.checked) {
      if (!provinceSelect.value) { provinceSelect.classList.add('is-invalid'); valid = false; }
      if (!districtSelect.value) { districtSelect.classList.add('is-invalid'); valid = false; }
      if (!wardSelect.value)     { wardSelect.classList.add('is-invalid'); valid = false; }
    }
    if (!valid) return;

    // --- Xây payload gửi lên payment.js ---
    const payload = {
      name:  nameFld.value.trim(),
      sdt:   phoneFld.value.trim(),
      email: emailFld.value.trim(),
    };

    if (savedRadio.checked) {
      // địa chỉ đã lưu: chỉ cần ID
      payload.saved_id = document.getElementById('saved-address').value;
    } else {
      // nhập địa chỉ mới: lấy TEXT của option (tên) thay vì value (mã)
      const provText = provinceSelect.options[provinceSelect.selectedIndex]?.text;
      const distText = districtSelect.options[districtSelect.selectedIndex]?.text;
      const wardText = wardSelect.options[wardSelect.selectedIndex]?.text;
      payload.province = provText || '';
      payload.district = distText || '';
      payload.ward     = wardText || '';
      payload.address  = document.getElementById('specific-address').value.trim() || '';
    }

    // thêm phương thức thanh toán
    payload.payment_method = document.querySelector("input[name='payment_method']:checked").value;

    // call tiếp sang payment.js
    if (window.startPaymentProcess) {
      window.startPaymentProcess(payload);
    } else {
      console.error('startPaymentProcess not found');
    }
  };
});
