// fix: mini_cart.js
(function() {
    // Render mini cart items
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
        const imagePath = item.image?.includes('/') ? item.image : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
      
        // ✅ xử lý màu
        const colorData = COLOR_MAP?.[item.color_id] || {};
        const colorName = colorData.name || '(không màu)';
        const colorHex = colorData.hex || '#ccc';
      
        div.innerHTML = `
          <img src="${imagePath}" style="width:50px;height:50px;object-fit:cover;" class="me-2 rounded">
          <div class="flex-grow-1">
            <p class="mb-0 small fw-bold">${item.name}</p>
            <p class="mb-0 text-muted small">
              <span class="me-1 d-inline-block rounded-circle" 
                    style="width:12px; height:12px; background-color:${colorHex}; border:1px solid #aaa;">
              </span>
              ${colorName} - ${item.size || '(không size)'}
            </p>
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
  
    async function changeMiniCartQty(index, delta) {
      const cart = JSON.parse(localStorage.getItem("cart") || []);
      const item = cart[index];
      if (!item) return;
    
      const res = await fetch('/ajax/check_login.php');
      const check = await res.json();
    
      if (!check.loggedIn) {
        // ✅ Chỉ sửa local nếu chưa đăng nhập
        if (delta > 0) {
          item.quantity += 1;
        } else {
          if (item.quantity <= 1) {
            cart.splice(index, 1);
          } else {
            item.quantity -= 1;
          }
        }
        localStorage.setItem("cart", JSON.stringify(cart));
        renderMiniCart();
        return;
      }
    
      // ✅ Nếu đăng nhập thì gửi request lên server
      const isRemoving = delta < 0 && item.quantity === 1;
      const action = isRemoving ? 'remove' : (delta > 0 ? 'increase' : 'decrease');
    
      const response = await fetch('/ajax/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, variant_id: item.variant_id })
      });
    
      const data = await response.json();
      if (data.success) {
        if (isRemoving || item.quantity + delta < 1) {
          cart.splice(index, 1);
        } else {
          item.quantity += delta;
        }
        localStorage.setItem("cart", JSON.stringify(cart));
        renderMiniCart();
      }
    }
    
    
    function removeMiniCartItem(index) {
      const cart = JSON.parse(localStorage.getItem("cart")) || [];
      const item = cart[index];
      if (!item) return;
    
      fetch('/ajax/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'remove', variant_id: item.variant_id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          cart.splice(index, 1);
          localStorage.setItem("cart", JSON.stringify(cart));
          renderMiniCart();
        } else {
          console.error('Lỗi xoá DB:', data.message);
        }
      });
    }
    
  
    function setupToggle() {
      const toggleBtn = document.getElementById("toggle-cart");
      const closeBtn = document.getElementById("close-mini-cart");
      const miniCart = document.getElementById("mini-cart");
      if (!toggleBtn || !miniCart) return;
  
      // Initial state hidden
      miniCart.style.display = 'none';
  
      toggleBtn.addEventListener('click', () => {
        miniCart.style.display = (miniCart.style.display === 'flex') ? 'none' : 'flex';
      });
  
      if (closeBtn) {
        closeBtn.addEventListener('click', () => {
          miniCart.style.display = 'none';
        });
      }
    }
  
    document.addEventListener('DOMContentLoaded', () => {
      setupToggle();
      renderMiniCart();
    });
  
    // export
    window.renderMiniCart = renderMiniCart;
    window.changeMiniCartQty = changeMiniCartQty;
    window.removeMiniCartItem = removeMiniCartItem;
  })();