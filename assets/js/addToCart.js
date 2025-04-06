function addToCart(id, name, price) {
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let existing = cart.find(item => item.id == id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    localStorage.setItem("cart", JSON.stringify(cart)); 
    showAddToCartNotice(name);
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
