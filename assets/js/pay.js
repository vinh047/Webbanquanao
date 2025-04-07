// Dữ liệu địa chỉ
const provinces = ["Hồ Chí Minh", "Hà Nội"];
const districts = {
  "Hồ Chí Minh": ["Quận 1", "Quận Gò Vấp"],
  "Hà Nội": ["Hoàn Kiếm", "Cầu Giấy"]
};
const wards = {
  "Quận 1": ["Phường Bến Nghé", "Phường Cầu Ông Lãnh"],
  "Quận Gò Vấp": ["Phường 3", "Phường 4"],
  "Hoàn Kiếm": ["Phường Hàng Bạc"],
  "Cầu Giấy": ["Phường Dịch Vọng"]
};

//Load giỏ hàng
const cart = JSON.parse(localStorage.getItem("cart")) || [];
let subtotal = 0;
const orderItemsEl = document.getElementById("order-items");
cart.forEach(item => {
  const div = document.createElement("div");
  div.className = "d-flex border-bottom py-2 align-items-center";
  div.innerHTML = `
    <img src="${item.image || './assets/img/sanpham/sp1.jpg'}" class="me-2 rounded" style="width: 60px; height: 60px; object-fit: cover;">
    <div class="flex-grow-1">
      <p class="mb-0 fw-bold">${item.name}</p>
      <small>${item.color || 'Màu'} - ${item.size || 'Size'}</small>
      <p class="text-danger fw-bold mb-0">${(item.price * item.quantity).toLocaleString()}đ</p>
    </div>
  `;
  subtotal += item.price * item.quantity;
  orderItemsEl.appendChild(div);
});
document.getElementById("subtotal").textContent = subtotal.toLocaleString() + "đ";
document.getElementById("total").textContent = subtotal.toLocaleString() + "đ";
// Load dropdown địa chỉ
const provinceSelect = document.getElementById("province");
const districtSelect = document.getElementById("district");
const wardSelect = document.getElementById("ward");
provinces.forEach(p => {
  const option = document.createElement("option");
  option.value = p;
  option.textContent = p;
  provinceSelect.appendChild(option);
});
provinceSelect.addEventListener("change", () => {
  const p = provinceSelect.value;
  districtSelect.innerHTML = '<option>Chọn quận</option>';
  wardSelect.innerHTML = '<option>Chọn phường</option>';
  if (districts[p]) {
    districts[p].forEach(d => {
      const option = document.createElement("option");
      option.value = d;
      option.textContent = d;
      districtSelect.appendChild(option);
    });
  }
});
districtSelect.addEventListener("change", () => {
  const d = districtSelect.value;
  wardSelect.innerHTML = '<option>Chọn phường</option>';
  if (wards[d]) {
    wards[d].forEach(w => {
      const option = document.createElement("option");
      option.value = w;
      option.textContent = w;
      wardSelect.appendChild(option);
    });
  }
});

// Đặt hàng
function submitOrder() {
    const hoInput = document.getElementById("ho");
    const tenInput = document.getElementById("ten");
    const phoneInput = document.getElementById("sdt");
    const emailInput = document.getElementById("email");
    const province = provinceSelect.value;
    const district = districtSelect.value;
    const ward = wardSelect.value;
    document.querySelectorAll(".form-control").forEach(input => input.classList.remove("is-invalid"));
    document.querySelectorAll(".form-select").forEach(select => select.classList.remove("is-invalid"));
    let isValid = true;
    if (hoInput.value.trim() === "") {
      hoInput.classList.add("is-invalid");
      isValid = false;
    }
    if (tenInput.value.trim() === "") {
      tenInput.classList.add("is-invalid");
      isValid = false;
    }
    if (phoneInput.value.trim() === "") {
      phoneInput.classList.add("is-invalid");
      isValid = false;
    }
    if (emailInput.value.trim() === "") {
      emailInput.classList.add("is-invalid");
      isValid = false;
    }
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
  