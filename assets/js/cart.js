// Hàm hiển thị giỏ hàng
function renderCart() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartItems = document.getElementById("cart-items");
  const totalPriceEl = document.getElementById("total-price");

  if (!cartItems || !totalPriceEl) {
    console.warn("Không tìm thấy phần tử hiển thị giỏ hàng.");
    return;
  }

  cartItems.innerHTML = ""; // Xóa nội dung cũ

  if (cart.length === 0) {
    cartItems.innerHTML = `
      <div class="p-5 border rounded text-center">
        <p class="mb-3 fs-5">🛒 Giỏ hàng của bạn đang trống.</p>
      </div>
    `;
    totalPriceEl.textContent = "0₫";
    return;
  }

  let total = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const div = document.createElement("div");
    div.className = "d-flex border-bottom py-3 align-items-center";

    div.innerHTML = `
      <img src="${item.image || '/assets/img/sanpham/sp1.jpg'}" 
           class="me-3 rounded" 
           style="width: 100px; height: 100px; object-fit: cover;">
      <div class="flex-grow-1">
        <h6>${item.name}</h6>
        <p class="mb-1">Color: ${item.color || 'đen'} &nbsp;&nbsp;&nbsp; Size: ${item.size || 'L'}</p>
        <p class="fw-bold text-danger mb-1">${Math.round(Number(item.price)).toLocaleString('vi-VN')}₫</p>
        <div class="input-group input-group-sm" style="width: 100px;">
          <button class="btn btn-outline-secondary" onclick="updateQty(${index}, -1)">-</button>
          <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
          <button class="btn btn-outline-secondary" onclick="updateQty(${index}, 1)">+</button>
        </div>
      </div>
      <button class="btn btn-link text-danger ms-3" onclick="removeItem(${index})">
        <i class="fa fa-trash"></i>
      </button>
    `;

    cartItems.appendChild(div);
  });

  totalPriceEl.textContent = Math.round(total).toLocaleString('vi-VN') + "₫";
}

// Cập nhật số lượng sản phẩm
function updateQty(index, delta) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const item = cart[index];
  if (!item) return;

  item.quantity += delta;

  if (item.quantity <= 0) {
    cart.splice(index, 1);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

// Xoá sản phẩm khỏi giỏ
function removeItem(index) {
  if (!confirm("Bạn có chắc chắn muốn xoá sản phẩm này khỏi giỏ hàng?")) return;

  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

// Gọi renderCart khi trang đã sẵn sàng
document.addEventListener("DOMContentLoaded", renderCart);

// Gán global để dùng được bên ngoài nếu cần
window.renderCart = renderCart;
window.updateQty = updateQty;
window.removeItem = removeItem;
