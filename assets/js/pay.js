// Load giỏ hàng và hiển thị số lượng
const cart = JSON.parse(localStorage.getItem("cart")) || [];
let subtotal = 0;
let discount = 0;
const shippingFee = 30000;
const orderItemsEl = document.getElementById("order-items");
cart.forEach(item => {
  const div = document.createElement("div");
  div.className = "d-flex border-bottom py-2 align-items-center";
  div.innerHTML = `
    <img src="${item.image || './assets/img/sanpham/sp1.jpg'}" class="me-2 rounded" style="width: 60px; height: 60px; object-fit: cover;">
    <div class="flex-grow-1">
      <p class="mb-0 fw-bold">${item.name}</p>
      <small>${item.color || 'Màu'} - ${item.size || 'Size'}</small>
      <div><small><strong>Số lượng: ${item.quantity}</strong></small></div>
      <p class="text-danger fw-bold mb-0">${(item.price * item.quantity).toLocaleString()}đ</p>
    </div>
  `;
  subtotal += item.price * item.quantity;
  orderItemsEl.appendChild(div);
});

document.getElementById("subtotal").textContent = subtotal.toLocaleString() + "đ";
document.querySelector(".list-group-item:nth-child(2) span:nth-child(2)").textContent = shippingFee.toLocaleString() + "đ";
document.getElementById("total").textContent = (subtotal + shippingFee - discount).toLocaleString() + "đ";

// Áp dụng mã giảm giá
const voucherInput = document.querySelector(".input-group input");
const applyVoucherBtn = document.querySelector(".input-group button");

applyVoucherBtn.addEventListener("click", () => {
  const code = voucherInput.value.trim().toUpperCase();
  if (code === "HUYDEPTRAI") {
    discount = subtotal * 0.1;
  } else if (code === "MINHHUY") {
    discount = shippingFee; // freeship giảm đúng phí ship
  } else {
    discount = 0;
    alert("Mã giảm giá không hợp lệ!");
  }

  document.querySelector(".list-group-item:nth-child(3) span:nth-child(2)").textContent = discount > 0 ? '-' + discount.toLocaleString() + 'đ' : '0đ';
  document.getElementById("total").textContent = (subtotal + shippingFee - discount).toLocaleString() + "đ";
});
// Hiển thị QR nếu chọn phương thức thanh toán là ngân hàng hoặc momo
const paymentRadios = document.querySelectorAll("input[name='payment_method']");
const qrSection = document.createElement("div");
qrSection.id = "qr-section";
qrSection.className = "mt-3";
const paymentContainer = document.querySelector(".border.p-3.rounded");
paymentContainer.appendChild(qrSection);
paymentContainer.style.position = "relative";
qrSection.style.transition = "all 0.3s ease";

paymentRadios.forEach(radio => {
  radio.addEventListener("change", () => {
    const methodId = parseInt(radio.value);
    if (methodId === 2) { // ngân hàng
      qrSection.innerHTML = `<div class='text-center'><p class='mb-2'>Quét mã QR để thanh toán ngân hàng</p><img src='./assets/img/pay/qr-bank.png' width='150'></div>`;
    } else if (methodId === 3) { // momo
      qrSection.innerHTML = `<div class='text-center'><p class='mb-2'>Quét mã QR để thanh toán qua Momo</p><img src='./assets/img/pay/qr-momo.png' width='150'></div>`;
    } else {
      qrSection.innerHTML = "";
    }
  });
});

// Tải tỉnh/thành từ API
const provinceSelect = document.getElementById("province");
const districtSelect = document.getElementById("district");
const wardSelect = document.getElementById("ward");

fetch("https://provinces.open-api.vn/api/p/")
  .then(res => res.json())
  .then(data => {
    data.forEach(p => {
      const option = document.createElement("option");
      option.value = p.code;
      option.textContent = p.name.replace(/^Tỉnh |^Thành phố /, '');
      provinceSelect.appendChild(option);
    });
  });

provinceSelect.addEventListener("change", () => {
  const provinceCode = provinceSelect.value;
  districtSelect.innerHTML = '<option selected disabled>Quận/Huyện</option>';
  wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
  fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
    .then(res => res.json())
    .then(data => {
      data.districts.forEach(d => {
        const option = document.createElement("option");
        option.value = d.code;
        option.textContent = d.name;
        districtSelect.appendChild(option);
      });
    });
});

districtSelect.addEventListener("change", () => {
  const districtCode = districtSelect.value;
  wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
  fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
    .then(res => res.json())
    .then(data => {
      data.wards.forEach(w => {
        const option = document.createElement("option");
        option.value = w.code;
        option.textContent = w.name;
        wardSelect.appendChild(option);
      });
    });
});
// Tự động ẩn cảnh báo khi người dùng bắt đầu nhập/chọn lại
document.querySelectorAll(".form-control").forEach(input => {
  input.addEventListener("input", () => {
    if (input.value.trim() !== "") {
      input.classList.remove("is-invalid");
    }
  });
});

document.querySelectorAll(".form-select").forEach(select => {
  select.addEventListener("change", () => {
    if (select.value !== "Tỉnh/TP" && select.value !== "Quận/Huyện" && select.value !== "Phường/Xã") {
      select.classList.remove("is-invalid");
    }
  });
});
const phoneInput = document.getElementById("sdt");
const emailInput = document.getElementById("email");

function submitOrder() {
  const hoInput = document.getElementById("ho");
  const tenInput = document.getElementById("ten");
  const phoneVal = phoneInput.value.trim();
  const emailVal = emailInput.value.trim();

  const province = provinceSelect.value;
  const district = districtSelect.value;
  const ward = wardSelect.value;

  const phoneFeedback = document.getElementById("phone-feedback");
  const emailFeedback = document.getElementById("email-feedback");

  let isValid = true;

  // Reset lỗi text
  phoneFeedback.textContent = "";
  emailFeedback.textContent = "";

  // Họ
  if (hoInput.value.trim() === "") {
    hoInput.classList.add("is-invalid");
    isValid = false;
  }

  // Tên
  if (tenInput.value.trim() === "") {
    tenInput.classList.add("is-invalid");
    isValid = false;
  }

  // SĐT
  if (phoneVal === "") {
    phoneInput.classList.add("is-invalid");
    phoneFeedback.textContent = "Số điện thoại là bắt buộc";
    isValid = false;
  } else if (!/^0\d{9}$/.test(phoneVal)) {
    phoneInput.classList.add("is-invalid");
    phoneFeedback.textContent = "Số điện thoại không hợp lệ";
    isValid = false;
  }

  // Email
  if (emailVal === "") {
    emailInput.classList.add("is-invalid");
    emailFeedback.textContent = "Email là bắt buộc";
    isValid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
    emailInput.classList.add("is-invalid");
    emailFeedback.textContent = "Email không đúng định dạng";
    isValid = false;
  }

  // Tỉnh/Huyện/Xã
  if (province === "Tỉnh/TP") {
    provinceSelect.classList.add("is-invalid");
    isValid = false;
  }
  if (district === "Quận/Huyện") {
    districtSelect.classList.add("is-invalid");
    isValid = false;
  }
  if (ward === "Phường/Xã") {
    wardSelect.classList.add("is-invalid");
    isValid = false;
  }

  if (!isValid) return;

  alert("Đơn hàng đã được đặt thành công!");
  localStorage.removeItem("cart");
  window.location.href = "index.php";
}
// Realtime kiểm tra SĐT
phoneInput.addEventListener("input", () => {
  const phonePattern = /^0\d{9}$/;
  const phoneVal = phoneInput.value.trim();
  const feedback = document.getElementById("phone-feedback");

  if (phoneVal === "") {
    phoneInput.classList.add("is-invalid");
    feedback.textContent = "Số điện thoại là bắt buộc";
  } else if (!phonePattern.test(phoneVal)) {
    phoneInput.classList.add("is-invalid");
    feedback.textContent = "Số điện thoại không hợp lệ";
  } else {
    phoneInput.classList.remove("is-invalid");
    feedback.textContent = "";
  }
});

// Realtime kiểm tra Email
emailInput.addEventListener("input", () => {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const emailVal = emailInput.value.trim();
  const feedback = document.getElementById("email-feedback");

  if (emailVal === "") {
    emailInput.classList.add("is-invalid");
    feedback.textContent = "Email là bắt buộc";
  } else if (!emailPattern.test(emailVal)) {
    emailInput.classList.add("is-invalid");
    feedback.textContent = "Email không đúng định dạng";
  } else {
    emailInput.classList.remove("is-invalid");
    feedback.textContent = "";
  }
});
