// fix: mini_cart.js
function renderMiniCart() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const itemsContainer = document.getElementById("mini-cart-items");
  const countBadge = document.getElementById("cart-count-badge");
  const itemCount = document.getElementById("cart-item-count");

  if (!itemsContainer) return;
  itemsContainer.innerHTML = "";
  let totalQty = 0;

  cart.forEach((item, index) => {
    totalQty += item.quantity;
    const div = document.createElement("div");
    div.className = "d-flex align-items-center mb-2 border-bottom pb-2";
    div.innerHTML = `
      <img src="${item.image || '/assets/img/sanpham/sp1.jpg'}" style="width:50px; height:50px; object-fit:cover;" class="me-2 rounded">
      <div class="flex-grow-1">
        <p class="mb-0 small fw-bold">${item.name}</p>
        <p class="mb-0 text-muted small">${COLOR_MAP?.[item.color_id]?.name || '(không màu)'} - ${item.size || '(không size)'}</p>
        <div class="d-flex align-items-center mt-1">
          <span class="small me-2">SL:</span>
          <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeMiniCartQty(${index}, -1)"><i class="fa fa-minus"></i></button>
          <span class="mx-2">${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeMiniCartQty(${index}, 1)"><i class="fa fa-plus"></i></button>
        </div>
      </div>
      <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeMiniCartItem(${index})"><i class="fa fa-trash"></i></button>
    `;
    itemsContainer.appendChild(div);
  });

  if (countBadge) countBadge.textContent = totalQty;
  if (itemCount) itemCount.textContent = totalQty;
}

function changeMiniCartQty(index, delta) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const item = cart[index]; if (!item) return;
  item.quantity += delta; if (item.quantity < 1) cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderMiniCart();
}

function removeMiniCartItem(index) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  renderMiniCart();
}

function setupMiniCartToggle() {
  const toggleBtn = document.getElementById("toggle-cart");
  const closeBtn  = document.getElementById("close-mini-cart");
  const miniCart  = document.getElementById("mini-cart");
  if (!toggleBtn || !miniCart) return;
  toggleBtn.addEventListener("click", () => {
    miniCart.classList.toggle("d-none");
  });
  if (closeBtn) {
    closeBtn.addEventListener("click", () => {
      miniCart.classList.add("d-none");
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  setupMiniCartToggle();
  renderMiniCart();

  // Tự động render nếu đang ở trang giỏ hàng
  if (window.location.href.includes("page=giohang")) {
    renderCartPage();
  }

  document.addEventListener("click", function (e) {
    const miniCart = document.getElementById("mini-cart");
    const toggleBtn = document.getElementById("toggle-cart");
    if (!miniCart || !toggleBtn) return;

    const clickedInsideCart = miniCart.contains(e.target);
    const clickedToggleBtn  = toggleBtn.contains(e.target);

    if (!clickedInsideCart && !clickedToggleBtn) {
      miniCart.classList.add("d-none");
    }
  });
});

function renderCartPage() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const cartContainer = document.getElementById("cart-items");
  const totalPriceEl = document.getElementById("total-price");

  if (!cartContainer || !totalPriceEl) return;

  cartContainer.innerHTML = "";

  if (cart.length === 0) {
    cartContainer.innerHTML = `
      <div class="text-center text-muted py-5">
        <i class="fa fa-shopping-cart fa-2x mb-3"></i>
        <p class="mb-0 fw-bold fs-5">Giỏ hàng của bạn đang trống</p>
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
    div.className = "d-flex gap-3 border-bottom py-3 align-items-center";

    const imagePath = item.image?.includes('/') ? item.image : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
div.innerHTML = `
  <img src="${imagePath}" alt="" width="100" height="100" class="rounded" style="object-fit:cover;">
      <div class="flex-grow-1">
        <h6 class="fw-bold mb-1">${item.name}</h6>
        <p class="mb-1 small">Color : ${COLOR_MAP?.[item.color_id]?.name || '(không màu)'}</p>
        <p class="mb-1 small">Size : ${item.size || '(không size)'}</p>
        <p class="fw-semibold text-danger">${item.price.toLocaleString()}₫</p>
        <div class="d-inline-flex align-items-center border rounded px-2 py-1">
          <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeCartQty(${index}, -1)">-</button>
          <span class="mx-3">${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeCartQty(${index}, 1)">+</button>
        </div>
      </div>
      <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeCartItem(${index})">
        <i class="fa fa-trash"></i>
      </button>
    `;
    cartContainer.appendChild(div);
  });

  totalPriceEl.textContent = total.toLocaleString() + "₫";
}


async function changeCartQty(index, delta) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const item = cart[index];
  if (!item) return;

  // Nếu tăng thì kiểm tra tồn kho trước
  if (delta > 0) {
    const res = await fetch('ajax/check_stock.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ variant_id: item.variant_id })
    });

    const data = await res.json();
    if (!data.success || item.quantity + 1 > data.stock) {
      alert(`Chỉ còn ${data.stock} sản phẩm trong kho.`);
      return;
    }
  }

  const isRemoving = delta < 0 && item.quantity === 1;
  const action = isRemoving ? 'remove' : (delta > 0 ? 'increase' : 'decrease');

  fetch('ajax/update_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action, variant_id: item.variant_id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      if (isRemoving || item.quantity + delta < 1) {
        cart.splice(index, 1);
      } else {
        item.quantity += delta;
      }
      localStorage.setItem("cart", JSON.stringify(cart));
      renderCartPage();
      renderMiniCart();
    }
  });
}


function removeCartItem(index) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const item = cart[index];
  if (!item) return;

  fetch('ajax/update_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'remove', variant_id: item.variant_id })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      cart.splice(index, 1);
      localStorage.setItem("cart", JSON.stringify(cart));
      renderCartPage();
      renderMiniCart();
    }
  });
}


// ✓ Gán global
window.renderMiniCart = renderMiniCart;
window.changeMiniCartQty = changeMiniCartQty;
window.removeMiniCartItem = removeMiniCartItem;