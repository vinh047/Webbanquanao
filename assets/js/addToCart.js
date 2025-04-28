async function checkStockBeforeAdd(variant_id, quantity) {
    try {
        const res = await fetch('ajax/check_stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ variant_id: variant_id })
        });
        const data = await res.json();

        if (!data.success) {
            showNotification('Không kiểm tra được tồn kho.', 'error');
            return false;
        }

        const stock = data.stock;

        if (stock <= 0) {
            showNotification('Sản phẩm này đã tạm hết hàng.', 'warning');
            return false;
        }

        if (quantity > stock) {
            showNotification(`Sản phẩm này chỉ còn ${stock} sản phẩm trong kho.`, 'warning');
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

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1, image, variant_id, color, size });
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    showAddToCartNotice(name);
}

function showNotification(message, type = 'success') {
    const notice = document.getElementById('noticeAddToCart');
    const icon = document.getElementById('noticeIcon');
    const text = document.getElementById('noticeText');

    if (!notice || !icon || !text) return;

    text.textContent = message;
    icon.className = 'fa-solid fa-3x mb-3';
    icon.style = '';

    if (type === 'success') {
        icon.classList.add('fa-circle-check');
        icon.style.color = '#ffffff';
    } else if (type === 'error') {
        icon.classList.add('fa-circle-xmark');
        icon.style.color = '#ffffff';
    } else if (type === 'warning') {
        icon.classList.add('fa-triangle-exclamation');
        icon.style.color = '#ffffff';
    }

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
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const countBadge = document.getElementById("cart-count-badge");
    let totalQty = 0;
    cart.forEach(item => {
        totalQty += item.quantity;
    });
    if (countBadge) {
        countBadge.textContent = totalQty;
    }
}

document.addEventListener("DOMContentLoaded", updateCartCount);
window.addToCart = addToCart;
