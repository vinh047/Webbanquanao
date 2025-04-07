function renderMiniCart() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const itemsContainer = document.getElementById("mini-cart-items");
    const countBadge = document.getElementById("cart-count-badge");
    const itemCount = document.getElementById("cart-item-count");

    itemsContainer.innerHTML = "";
    let totalQty = 0;

    cart.forEach((item, index) => {
        totalQty += item.quantity;
        const div = document.createElement("div");
        div.className = "d-flex align-items-center mb-2 border-bottom pb-2";
        div.innerHTML = `
  <img src="${item.image || './assets/img/sanpham/sp1.jpg'}"
       style="width: 50px; height: 50px; object-fit: cover;" class="me-2 rounded">
  <div class="flex-grow-1">
      <p class="mb-0 small fw-bold">${item.name}</p>
      <p class="mb-0 text-muted small">${item.color || 'Màu'} - ${item.size || 'Size'}</p>
      <div class="d-flex align-items-center mt-1">
          <span class="small me-2">SL:</span>
         <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeMiniCartQty(${index}, -1)">
  <i class="fa fa-minus"></i>
</button>
<span class="mx-2">${item.quantity}</span>
<button class="btn btn-sm btn-outline-secondary px-2" onclick="changeMiniCartQty(${index}, 1)">
  <i class="fa fa-plus"></i>
</button>

      </div>
  </div>
  <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeMiniCartItem(${index})">
      <i class="fa fa-trash"></i>
  </button>
`;

        itemsContainer.appendChild(div);
    });

    if (countBadge) countBadge.textContent = totalQty;
    if (itemCount) itemCount.textContent = totalQty;
}

function removeMiniCartItem(index) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
}
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggle-cart");
    const closeBtn = document.getElementById("close-mini-cart");
    const miniCart = document.getElementById("mini-cart");

    if (toggleBtn && miniCart) {
        toggleBtn.addEventListener("click", () => {
            miniCart.classList.toggle("d-none"); // ← toggle ẩn hiện bằng class
        });
    }

    if (closeBtn && miniCart) {
        closeBtn.addEventListener("click", () => {
            console.log("Đã ấn nút Đóng");
            miniCart.classList.add("d-none"); // ← ẩn bằng class
        });
    }

    renderMiniCart();
});
function changeMiniCartQty(index, delta) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const item = cart[index];

    if (!item) return;

    item.quantity += delta;

    if (item.quantity < 1) {
        cart.splice(index, 1); // Xóa nếu nhỏ hơn 1
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
}

