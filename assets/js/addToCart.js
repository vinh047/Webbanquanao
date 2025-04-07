function addToCart(id, name, price, image) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existingItem = cart.find(item => item.id === id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1, image });
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    renderMiniCart();
    updateCartCount();
    showAddToCartNotice(name);
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
function showAddToCartNotice(productName) {
    const notice = document.getElementById('noticeAddToCart');
    const text = document.getElementById('noticeText');
    if (!notice || !text) return;
    text.textContent = `Đã thêm vào giỏ hàng!`;
    notice.classList.remove('opacity-0');
    notice.classList.add('opacity-100');
    setTimeout(() => {
        notice.classList.remove('opacity-100');
        notice.classList.add('opacity-0');
    }, 2500);
}
document.addEventListener("DOMContentLoaded", updateCartCount);
window.addToCart = addToCart;
