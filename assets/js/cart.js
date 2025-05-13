(function () {
  // Default COLOR_MAP if not defined
  const COLOR_MAP = window.COLOR_MAP || {};

  // === Shared Utility Functions ===

  // Show notification with consistent styling
  function showNotification(message, type = 'success') {
    const notice = document.getElementById('noticeAddToCart');
    const icon = document.getElementById('noticeIcon');
    const text = document.getElementById('noticeText');

    if (!notice || !icon || !text) return;

    text.textContent = message;
    icon.className = 'fa-solid fa-3x mb-3';
    icon.style.color = '#fff';

    if (type === 'success') icon.classList.add('fa-circle-check');
    if (type === 'error') icon.classList.add('fa-circle-xmark');
    if (type === 'warning') icon.classList.add('fa-triangle-exclamation');

    notice.classList.remove('opacity-0');
    notice.classList.add('opacity-100');

    setTimeout(() => {
      notice.classList.remove('opacity-100');
      notice.classList.add('opacity-0');
    }, 2500);
  }

  // Update cart count badge
  function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const countBadge = document.getElementById('cart-count-badge');
    const itemCount = document.getElementById('cart-item-count');
    const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);

    if (countBadge) countBadge.textContent = totalQty;
    if (itemCount) itemCount.textContent = totalQty;
  }

  // Check stock before adding/updating quantity
  async function checkStockBeforeAdd(variant_id, quantity) {
    try {
      const res = await fetch('/ajax/check_stock.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ variant_id }),
      });
      const data = await res.json();

      if (!data.success) {
        showNotification('Không kiểm tra được tồn kho.', 'error');
        return false;
      }

      if (data.stock <= 0) {
        showNotification('Sản phẩm này đã tạm hết hàng.', 'warning');
        return false;
      }

      if (quantity > data.stock) {
        showNotification(`Chỉ còn ${data.stock} sản phẩm trong kho.`, 'warning');
        return false;
      }

      return true;
    } catch (error) {
      console.error('Error checking stock:', error);
      showNotification('Lỗi kiểm tra tồn kho.', 'error');
      return false;
    }
  }

  // Sync cart to server
  async function syncCartToServer() {
    try {
      const checkLoginRes = await fetch('/ajax/check_login.php');
      const checkLoginData = await checkLoginRes.json();
      if (!checkLoginData.loggedIn) {
        console.log('⚠️ Not logged in, skipping cart sync.');
        return;
      }

      const cart = JSON.parse(localStorage.getItem('cart') || '[]');
      if (!Array.isArray(cart) || cart.length === 0) {
        console.log('⚠️ No items to sync.');
        return;
      }

      const res = await fetch('/ajax/sync_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cart),
      });

      const result = await res.json();
      if (!result.success) {
        console.warn('❌ Sync failed:', result.message);
        showNotification('Không thể đồng bộ giỏ hàng.', 'error');
      } else {
        console.log('✅ Cart synced to server.');
      }
    } catch (err) {
      console.error('❌ Sync error:', err);
      showNotification('Lỗi đồng bộ giỏ hàng.', 'error');
    }
  }

  // Sync cart after login (overwrite local with DB data)
  async function syncCartAfterLogin() {
    try {
      const res = await fetch('/ajax/get_cart.php');
      const result = await res.json();
  
      if (result.success && Array.isArray(result.data)) {
        const dbCart = result.data;
        const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
  
        const hasPrompted = sessionStorage.getItem('cart_merge_prompted');
        if (hasPrompted === '1') return; // đã hỏi rồi thì không hỏi lại
  
        // Nếu localCart không rỗng thì mới hỏi
        if (localCart.length > 0) {
          const userChoice = confirm('Bạn có muốn thêm sản phẩm ở giỏ hàng vừa rồi vào giỏ hàng của bạn không?');
          if (!userChoice) {
            // Nếu không muốn gộp thì xóa localStorage
            localStorage.removeItem('cart');
            localStorage.setItem('cart', JSON.stringify(dbCart));
            renderMiniCart();
            renderCartPage();
            updateCartCount();
            sessionStorage.setItem('cart_merge_prompted', '1');
            return;
          }
        }
  
        // Merge logic nếu đồng ý hoặc localCart rỗng
        const mergedMap = new Map();
        dbCart.forEach(item => {
          const key = `${item.product_id}-${item.variant_id}`;
          mergedMap.set(key, { ...item });
        });
        localCart.forEach(item => {
          const key = `${item.product_id}-${item.variant_id}`;
          if (mergedMap.has(key)) {
            mergedMap.get(key).quantity += item.quantity;
          } else {
            mergedMap.set(key, { ...item });
          }
        });
  
        const merged = Array.from(mergedMap.values());
        localStorage.setItem('cart', JSON.stringify(merged));
        renderMiniCart();
        renderCartPage();
        updateCartCount();
  
        await fetch('/ajax/sync_cart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(merged)
        });
  
        sessionStorage.setItem('cart_merge_prompted', '1');
      }
    } catch (err) {
      console.error('❌ Sync error', err);
    }
  }
  
  
  // === Mini Cart Functions ===

  function renderMiniCart() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const itemsContainer = document.getElementById('mini-cart-items');
    if (!itemsContainer) return;
  
    itemsContainer.innerHTML = '';
    let totalQty = 0;
  
    cart.forEach((item, index) => {
      totalQty += item.quantity; // Sum quantities correctly
      const div = document.createElement('div');
      div.className = 'd-flex align-items-center mb-2 border-bottom pb-2';
      const imagePath = item.image?.includes('/')
        ? item.image
        : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
  
      const colorData = COLOR_MAP[item.color_id] || {};
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
            <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeMiniCartQty(${index}, -1)">
              <i class="fa fa-minus"></i>
            </button>
            <span class="mx-2">${item.quantity}</span>
            <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeMiniCartQty(${index}, 1)">
              <i class="fa fa-plus"></i>
            </button>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-danger ms-2" onclick="window.removeMiniCartItem(${index})">
          <i class="fa fa-trash"></i>
        </button>
      `;
      itemsContainer.appendChild(div);
    });
  
    updateCartCount();
  }

  async function changeMiniCartQty(index, delta) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const item = cart[index];
    if (!item) return;

    // Check stock if increasing quantity
    if (delta > 0) {
      const ok = await checkStockBeforeAdd(item.variant_id, item.quantity + 1);
      if (!ok) return;
    }

    const checkLoginRes = await fetch('/ajax/check_login.php');
    const checkLoginData = await checkLoginRes.json();

    if (!checkLoginData.loggedIn) {
      // Update local cart only
      if (delta > 0) {
        item.quantity += 1;
      } else if (item.quantity <= 1) {
        cart.splice(index, 1);
      } else {
        item.quantity -= 1;
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      return;
    }

    // Update server if logged in
    const isRemoving = delta < 0 && item.quantity === 1;
    const action = isRemoving ? 'remove' : delta > 0 ? 'increase' : 'decrease';

    const response = await fetch('/ajax/update_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, variant_id: item.variant_id }),
    });

    const data = await response.json();
    if (data.success) {
      if (isRemoving || item.quantity + delta < 1) {
        cart.splice(index, 1);
      } else {
        item.quantity += delta;
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      renderCartPage();
    } else {
      console.error('Update cart error:', data.message);
      showNotification('Không thể cập nhật giỏ hàng.', 'error');
    }
  }

  async function removeMiniCartItem(index) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const item = cart[index];
    if (!item) return;

    const checkLoginRes = await fetch('/ajax/check_login.php');
    const checkLoginData = await checkLoginRes.json();

    if (!checkLoginData.loggedIn) {
      // Update local cart only
      cart.splice(index, 1);
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      return;
    }

    const response = await fetch('/ajax/update_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'remove', variant_id: item.variant_id }),
    });

    const data = await response.json();
    if (data.success) {
      cart.splice(index, 1);
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      renderCartPage();
    } else {
      console.error('Remove item error:', data.message);
      showNotification('Không thể xóa sản phẩm.', 'error');
    }
  }
  function setupMiniCartToggle() {
    const toggleBtn = document.getElementById('toggle-cart');
    const closeBtn = document.getElementById('close-mini-cart');
    const miniCart = document.getElementById('mini-cart');
  
    if (!toggleBtn || !miniCart) {
      console.warn('Không tìm thấy #toggle-cart hoặc #mini-cart');
      return;
    }
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = miniCart.classList.contains('d-none');
      miniCart.classList.toggle('d-none');
      if (isHidden) {
        miniCart.style.display = 'flex'; // ép hiện lại
      } else {
        miniCart.style.display = 'none';
      }
    });
    if (closeBtn) {
      closeBtn.addEventListener('click', () => {
        miniCart.classList.add('d-none');
        miniCart.style.display = 'none';
      });
    }
    
    document.addEventListener('click', (e) => {
      if (!miniCart.contains(e.target) && !toggleBtn.contains(e.target)) {
        miniCart.classList.add('d-none');
        miniCart.style.display = 'none';
      }
    });
    
  }
  
  
  // === Add to Cart Functions ===

  async function addToCart(product_id, name, price, image, variant_id, color_id = 0, size = '') {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingItemIndex = cart.findIndex(
      (item) =>
        parseInt(item.product_id) === parseInt(product_id) &&
        parseInt(item.variant_id) === parseInt(variant_id)
    );
    const newQuantity = existingItemIndex >= 0 ? cart[existingItemIndex].quantity + 1 : 1;
  
    const ok = await checkStockBeforeAdd(variant_id, newQuantity);
    if (!ok) return;
  
    const finalImage = image && image.trim() !== '' ? image : 'default.jpg';
  
    if (existingItemIndex >= 0) {
      cart[existingItemIndex].quantity += 1; // Update existing item
    } else {
      cart.push({
        product_id: parseInt(product_id),
        name,
        price: parseFloat(price),
        quantity: 1,
        image: finalImage,
        variant_id: parseInt(variant_id),
        color_id: parseInt(color_id) || 0,
        size: size && typeof size === 'string' ? size.trim() : '(không size)',
      });
    }
  
    localStorage.setItem('cart', JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    showNotification('Đã thêm vào giỏ hàng!', 'success');
    syncCartToServer();
  }

  function handleAddToCartClick(e) {
    if (!e.target.closest('.add-to-cart-btn')) return;

    const btn = e.target.closest('.add-to-cart-btn');
    const productId = btn.getAttribute('data-product-id');
    const productName = btn.getAttribute('data-product-name');
    const productPrice = btn.getAttribute('data-product-price');
    const productContainer = btn.closest('.border.rounded-1');

    const selectedColor = productContainer.querySelector('.color-thumb.selected');
    const selectedSize = productContainer.querySelector('.size-thumb.selected');

    if (!selectedColor || !selectedSize) {
      showNotification('Vui lòng chọn màu và size trước khi thêm vào giỏ hàng!', 'warning');
      return;
    }

    const variantImageFull = selectedColor.getAttribute('data-image');
    const variantImage = variantImageFull ? variantImageFull.split('/').pop() : 'default.jpg';
    const variantId = selectedSize.getAttribute('data-variant-id');
    const colorId = selectedColor.getAttribute('data-color-id');
    const sizeName = selectedSize.getAttribute('data-size-name') || '(không size)';

    if (!variantId) {
      showNotification('Không xác định được biến thể sản phẩm.', 'error');
      return;
    }

    addToCart(productId, productName, productPrice, variantImage, variantId, colorId, sizeName);
  }

  // === Cart Page Functions ===

  function renderCartPage() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const cartContainer = document.getElementById('cart-items');
    const totalPriceEl = document.getElementById('total-price');
    if (!cartContainer || !totalPriceEl) return;
  
    cartContainer.innerHTML = '';
  
    if (cart.length === 0) {
      cartContainer.innerHTML = `
        <div class="text-center text-muted py-5">
          <i class="fa fa-shopping-cart fa-2x mb-3"></i>
          <p class="mb-0 fw-bold fs-5">Giỏ hàng của bạn đang trống</p>
        </div>
      `;
      totalPriceEl.textContent = '0₫';
      return;
    }
  
    cart.forEach((item, index) => {
      const div = document.createElement('div');
      div.className = 'd-flex gap-3 border-bottom py-3 align-items-center';
  
      const imagePath = item.image?.includes('/')
        ? item.image
        : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;
  
      div.innerHTML = `
        <input type="checkbox" class="form-check-input select-item me-3" data-index="${index}">
        <img src="${imagePath}" alt="" width="100" height="100" class="rounded" style="object-fit:cover;">
        <div class="flex-grow-1">
          <h6 class="fw-bold mb-1">${item.name}</h6>
          <p class="mb-1 small">Color: ${COLOR_MAP[item.color_id]?.name || '(không màu)'}</p>
          <p class="mb-1 small">Size: ${item.size || '(không size)'}</p>
          <p class="fw-semibold text-danger">${item.price.toLocaleString()}₫</p>
          <div class="d-inline-flex align-items-center border rounded px-2 py-1">
            <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeCartQty(${index}, -1)">-</button>
            <span class="mx-3">${item.quantity}</span>
            <button class="btn btn-sm btn-outline-secondary px-2" onclick="window.changeCartQty(${index}, 1)">+</button>
          </div>
        </div>
        <button class="btn btn-sm btn-outline-danger ms-2" onclick="window.removeCartItem(${index})">
          <i class="fa fa-trash"></i>
        </button>
      `;
  
      cartContainer.appendChild(div);
    });
  
    // Gọi sau khi render xong để xử lý tổng và checkbox "chọn tất cả"
    updateSelectedTotal();
  
    // Sự kiện: chọn tất cả
    document.getElementById('select-all')?.addEventListener('change', (e) => {
      const checked = e.target.checked;
      document.querySelectorAll('.select-item').forEach(cb => {
        cb.checked = checked;
      });
      updateSelectedTotal();
    });
  
    // Sự kiện: tick từng sản phẩm
    document.querySelectorAll('.select-item').forEach(cb => {
      cb.addEventListener('change', updateSelectedTotal);
    });
  
    // Tính tổng tiền các sản phẩm đã chọn
    // Tính tổng tiền các sản phẩm đã chọn
function updateSelectedTotal() {
  const cart = JSON.parse(localStorage.getItem('cart') || '[]');
  const checkboxes = document.querySelectorAll('.select-item');
  const selectedCheckboxes = document.querySelectorAll('.select-item:checked');
  let total = 0;
  const selectedItems = [];

  selectedCheckboxes.forEach(cb => {
    const index = parseInt(cb.getAttribute('data-index'));
    const item = cart[index];
    if (item) {
      total += item.price * item.quantity;
      selectedItems.push(item); // ✅ gom sản phẩm đã chọn
    }
  });

  // ✅ Cập nhật tổng tiền
  totalPriceEl.textContent = total.toLocaleString() + '₫';

  // ✅ Tự động tick "Chọn tất cả" nếu cần
  const selectAll = document.getElementById('select-all');
  if (selectAll && checkboxes.length > 0) {
    selectAll.checked = selectedCheckboxes.length === checkboxes.length;
  }

  // ✅ Lưu sản phẩm đã chọn sang sessionStorage
  sessionStorage.setItem('selectedCartItems', JSON.stringify(selectedItems));
}

  }
  

  async function changeCartQty(index, delta) {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const item = cart[index];
    if (!item) return;

    // Check stock if increasing quantity
    if (delta > 0) {
      const ok = await checkStockBeforeAdd(item.variant_id, item.quantity + 1);
      if (!ok) return;
    }

    const checkLoginRes = await fetch('/ajax/check_login.php');
    const checkLoginData = await checkLoginRes.json();

    if (!checkLoginData.loggedIn) {
      // Update local cart only
      if (delta > 0) {
        item.quantity += 1;
      } else if (item.quantity <= 1) {
        cart.splice(index, 1);
      } else {
        item.quantity -= 1;
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      renderCartPage();
      return;
    }

    // Update server if logged in
    const isRemoving = delta < 0 && item.quantity === 1;
    const action = isRemoving ? 'remove' : delta > 0 ? 'increase' : 'decrease';

    const response = await fetch('/ajax/update_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action, variant_id: item.variant_id }),
    });

    const data = await response.json();
    if (data.success) {
      if (isRemoving || item.quantity + delta < 1) {
        cart.splice(index, 1);
      } else {
        item.quantity += delta;
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      renderMiniCart();
      renderCartPage();
    } else {
      console.error('Update cart error:', data.message);
      showNotification('Không thể cập nhật giỏ hàng.', 'error');
    }
  }

  async function removeCartItem(index) {
    await removeMiniCartItem(index); // Reuse mini cart removal logic
    renderCartPage(); // Ensure cart page is updated
  }

  // === Initialization ===

  document.addEventListener('DOMContentLoaded', () => {
    setupMiniCartToggle();
    renderMiniCart();
    updateCartCount();
    cleanCartDuplicates(); 
    // Render cart page if on cart page
    if (window.location.href.includes('page=giohang')) {
      renderCartPage();
    }

    // Handle add to cart clicks
    document.body.addEventListener('click', handleAddToCartClick);
  });
  function cleanCartDuplicates() {
    const cart = JSON.parse(localStorage.getItem('cart') || '[]');
    const uniqueCart = [];
    const seenVariants = new Map();
  
    cart.forEach((item) => {
      const key = `${item.product_id}-${item.variant_id}`;
      if (seenVariants.has(key)) {
        const existingItem = seenVariants.get(key);
        existingItem.quantity += item.quantity;
      } else {
        seenVariants.set(key, { ...item });
        uniqueCart.push(item);
      }
    });
  
    localStorage.setItem('cart', JSON.stringify(uniqueCart));
    renderMiniCart();
  }
  // Expose global functions
  window.renderMiniCart = renderMiniCart;
  window.changeMiniCartQty = changeMiniCartQty;
  window.removeMiniCartItem = removeMiniCartItem;
  window.addToCart = addToCart;
  window.handleAddToCartClick = handleAddToCartClick;
  window.updateCartCount = updateCartCount;
  window.syncCartToServer = syncCartToServer;
  window.syncCartAfterLogin = syncCartAfterLogin;
  window.renderCartPage = renderCartPage;
  window.changeCartQty = changeCartQty;
  window.removeCartItem = removeCartItem;
})();