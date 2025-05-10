// Kiểm tra tồn kho trước khi thêm vào giỏ
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

// Thêm sản phẩm vào giỏ
async function addToCart(id, name, price, image, variant_id, color = '', size = '') {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const existingItem = cart.find(item => item.id === id && item.variant_id === variant_id);
    const newQuantity = existingItem ? existingItem.quantity + 1 : 1;

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
            color: color?.trim() || "(không màu)",
            size: size?.trim() || "(không size)"
        });
    }

    localStorage.setItem("cart", JSON.stringify(cart));

    // Bọc để tránh lỗi khi renderMiniCart chưa sẵn sàng
    if (typeof renderMiniCart === "function") renderMiniCart();
    updateCartCount();
    showAddToCartNotice(name);
    syncCartToServer();
}

// Cập nhật số lượng trong mini cart
function changeMiniCartQty(index, delta) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    if (!cart[index]) return;

    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    if (typeof renderMiniCart === "function") renderMiniCart();
    updateCartCount();
    syncCartToServer();
}

// Xóa sản phẩm khỏi mini cart
function removeMiniCartItem(index) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    if (typeof renderMiniCart === "function") renderMiniCart();
    updateCartCount();
    syncCartToServer();
}

// Hiển thị thông báo
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

// Cập nhật số lượng ở icon giỏ hàng
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    const countBadge = document.getElementById("cart-count-badge");
    const totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (countBadge) countBadge.textContent = totalQty;
}

// Đồng bộ giỏ hàng với server
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

// Sau khi đăng nhập, đồng bộ lại giỏ hàng
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
            if (typeof renderMiniCart === "function") renderMiniCart();
            updateCartCount();
        }
    } catch (err) {
        console.error("❌ syncCartAfterLogin lỗi:", err);
    }
}

// Gán global để các file khác gọi được
window.addToCart = addToCart;
window.changeMiniCartQty = changeMiniCartQty;
window.removeMiniCartItem = removeMiniCartItem;
window.updateCartCount = updateCartCount;
window.syncCartToServer = syncCartToServer;
window.syncCartAfterLogin = syncCartAfterLogin;

// ✅ Gán sau cùng, đảm bảo
