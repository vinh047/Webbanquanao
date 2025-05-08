async function checkStockBeforeAdd(variant_id, quantity) {
    try {
        const res = await fetch('ajax/check_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ variant_id })
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
        console.error(error);
        showNotification('Lỗi kiểm tra tồn kho.', 'error');
        return false;
    }
}

async function addToCart(id, name, price, image, variant_id, color = '', size = '') {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingItem = cart.find(item => item.id === id && item.variant_id === variant_id);

    let newQuantity = existingItem ? existingItem.quantity + 1 : 1;

    const ok = await checkStockBeforeAdd(variant_id, newQuantity);
    if (!ok) return;

    const finalImage = image && image.trim() !== "" ? image : "default.jpg";

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
      

cart.push({
  id,
  name,
  price,
  quantity: 1,
  image: finalImage,
  variant_id,
  color: color && color.trim() !== "" ? color : "(không màu)",
  size: size && size.trim() !== "" ? size : "(không size)"
});
        
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    showAddToCartNotice(name);
    syncCartToServer();
}

window.addToCart = addToCart;

function changeMiniCartQty(index, delta) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (!cart[index]) return;

    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    syncCartToServer();
}
window.changeMiniCartQty = changeMiniCartQty;

function removeMiniCartItem(index) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);

    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    syncCartToServer();
}
window.removeMiniCartItem = removeMiniCartItem;

function showNotification(message, type = 'success') {
    const notice = document.getElementById('noticeAddToCart');
    const icon = document.getElementById('noticeIcon');
    const text = document.getElementById('noticeText');

    if (!notice || !icon || !text) return;

    text.textContent = message;
    icon.className = 'fa-solid fa-3x mb-3';
    icon.style = '';

    if (type === 'success') icon.classList.add('fa-circle-check');
    if (type === 'error') icon.classList.add('fa-circle-xmark');
    if (type === 'warning') icon.classList.add('fa-triangle-exclamation');

    icon.style.color = '#fff';

    notice.classList.remove('opacity-0');
    notice.classList.add('opacity-100');

    setTimeout(() => {
        notice.classList.remove('opacity-100');
        notice.classList.add('opacity-0');
    }, 2500);
}

function showAddToCartNotice(productName) {
    showNotification('Đã thêm vào giỏ hàng!', 'success');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    const countBadge = document.getElementById("cart-count-badge");
    let totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (countBadge) {
        countBadge.textContent = totalQty;
    }
}
window.updateCartCount = updateCartCount;

async function syncCartToServer() {
    try {
        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        if (cart.length === 0) return;

        const res = await fetch("/ajax/sync_cart.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(cart)
        });

        const result = await res.json();
        if (!result.success) {
            console.warn("❌ Sync DB thất bại:", result.message);
        } else {
            console.log("✅ Giỏ hàng đã đồng bộ với DB");
        }
    } catch (err) {
        console.error("❌ Lỗi syncCartToServer:", err);
    }
}
window.syncCartToServer = syncCartToServer;

async function syncCartAfterLogin() {
    try {
        const localCart = JSON.parse(localStorage.getItem("cart") || "[]");

        if (localCart.length > 0) {
            const syncRes = await fetch("/ajax/sync_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(localCart)
            });

            const syncResult = await syncRes.json();
            if (syncResult.success) {
                localStorage.removeItem("cart");
            } else {
                console.warn("⚠️ Sync thất bại:", syncResult.message);
            }
        }

        const res = await fetch("/ajax/get_cart.php");
        const result = await res.json();

        if (result.success && Array.isArray(result.data)) {
            localStorage.setItem("cart", JSON.stringify(result.data));
            renderMiniCart();
            updateCartCount();
        }
    } catch (err) {
        console.error("❌ syncCartAfterLogin lỗi:", err);
    }
}
// Cuối file mini_cart.js (hoặc addToCart.js nếu gộp):
window.addToCart = addToCart;
window.changeMiniCartQty = changeMiniCartQty;
window.removeMiniCartItem = removeMiniCartItem;
window.updateCartCount = updateCartCount;
console.log(">> Hàm syncCartToServer đã khai báo");
window.syncCartToServer = syncCartToServer;
window.syncCartAfterLogin = syncCartAfterLogin;
window.renderMiniCart = renderMiniCart; // thêm dòng này

document.addEventListener("DOMContentLoaded", updateCartCount);
