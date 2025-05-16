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

    // Sync cart after login (overwrite local with DB data)
    async function syncCartAfterLogin() {
        // Nếu chưa đăng nhập thì không làm gì
        if (!user_id) return;

        // Lấy giỏ hàng local
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        if (!Array.isArray(cart) || cart.length === 0) return;

        // Gửi từng item lên server qua add_to_cart.php
        for (const item of cart) {
            try {
                const response = await fetch('../ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: item.id,
                        color_id: item.color_id,
                        size_id: item.size_id,
                        quantity: item.quantity
                    })
                });

                const result = await response.json();
                if (!result.success) {
                    console.warn(`❌ Không thể đồng bộ item variant_id=${item.variant_id}: ${result.message}`);
                }
            } catch (err) {
                console.error('❌ Lỗi khi đồng bộ sản phẩm:', err);
            }
        }

        // ✅ Xóa cart local sau khi đã đồng bộ
        localStorage.removeItem('cart');

        console.log('✅ Giỏ hàng đã được đồng bộ sau khi đăng nhập.');
    }
    syncCartAfterLogin();


    // === Mini Cart Functions ===
    function renderMiniCart() {
        const itemsContainer = document.getElementById('mini-cart-items');
        if (!itemsContainer) return;
        itemsContainer.innerHTML = '';
        let totalQty = 0;

        if (user_id == null) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');

            cart.forEach((item, index) => {
                totalQty += item.quantity; // Sum quantities correctly
                const div = document.createElement('div');
                div.className = 'cart-item-wrap d-flex align-items-center mb-2 border-bottom pb-2';

                const colorData = COLOR_MAP[item.color_id] || {};
                const colorName = colorData.name || '(không màu)';
                const colorHex = colorData.hex || '#ccc';

                div.innerHTML = `
                        <img src="/assets/img/sanpham/${item.image}" style="width:50px;height:50px;object-fit:cover;" class="me-2 rounded">
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
                            <button class="btn btn-sm btn-outline-secondary px-2 btn-minus">
                            <i class="fa fa-minus"></i>
                            </button>
                            <span class="qty mx-2" data-product-id="${item.id}" data-variant-id="${item.variant_id}">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-secondary px-2 btn-plus">
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger ms-2 btn-delete-cart-item">
                        <i class="fa fa-trash"></i>
                        </button>
                `;
                itemsContainer.appendChild(div);
            });

        }
        else {
            fetch('/ajax/load_carts.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'user_id=' + encodeURIComponent(user_id)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        itemsContainer.innerHTML = data.cartItemHtml;
                    } else {
                        itemsContainer.innerHTML = '<p class="text-danger small">Không thể tải giỏ hàng.</p>';
                    }
                })
                .catch(err => {
                    console.error("Lỗi khi fetch giỏ hàng server:", err);
                    itemsContainer.innerHTML = '<p class="text-danger small">Lỗi kết nối tới máy chủ.</p>';
                });
        }
    }

    function updateCartFromMiniCart() {
        const items = [];
        const miniCart = document.querySelector('#mini-cart-items');
        const cartPage = document.querySelector('#cart-items');

        const source = miniCart && miniCart.offsetParent !== null
            ? miniCart.querySelectorAll('.cart-item-wrap')
            : cartPage?.querySelectorAll('.cart-item-wrap');

        if (!source) return;

        source.forEach(itemDiv => {
            const qtyEl = itemDiv.querySelector('.qty');
            const quantity = parseInt(qtyEl?.textContent || '0');

            // ✅ Thử lấy từ itemDiv (mini-cart), nếu không có thì lấy từ qtyEl (cart-page)
            const product_id = itemDiv.getAttribute('data-product-id') || qtyEl?.getAttribute('data-product-id');
            const variant_id = itemDiv.getAttribute('data-variant-id') || qtyEl?.getAttribute('data-variant-id');

            if (product_id && variant_id && quantity > 0) {
                items.push({
                    product_id: parseInt(product_id),
                    variant_id: parseInt(variant_id),
                    quantity: quantity
                });
            }
        });


        if (user_id) {
            fetch('/ajax/update_cart_from_ui.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    user_id: user_id,
                    cart: items
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Cập nhật giỏ hàng thất bại!');
                    }
                })
                .catch(err => {
                    console.error('Lỗi khi cập nhật cart:', err);
                });
        }
    }



    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-minus') || e.target.closest('.btn-plus')) {
            e.stopImmediatePropagation()
            const btn = e.target.closest('.btn-minus') || e.target.closest('.btn-plus');
            const parent = btn.closest('.cart-item-wrap');
            const qtyElement = parent.querySelector('.qty');
            let qty = parseInt(qtyElement.textContent);

            const isPlus = btn.classList.contains('btn-plus');
            qty = isPlus ? qty + 1 : Math.max(1, qty - 1);
            qtyElement.textContent = qty;
            updateTongTien();

            // Lấy index nếu là localStorage
            const allItems = [...document.querySelectorAll('.cart-item-wrap')];
            const index = allItems.indexOf(parent);


            if (!user_id) {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                if (cart[index]) {
                    cart[index].quantity = qty;
                    localStorage.setItem('cart', JSON.stringify(cart));
                }
            } else {
                console.log('gg');
                updateCartFromMiniCart();
            }
        }
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-cart-item');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const itemDiv = btn.closest('.cart-item-wrap');
        const qtyElement = itemDiv.querySelector('.qty');
        const index = [...document.querySelectorAll('#mini-cart-items .cart-item-wrap')].indexOf(itemDiv);

        if (!user_id) {
            // === Chưa đăng nhập → xử lý localStorage ===
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (index >= 0 && cart[index]) {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                itemDiv.remove(); // Xóa trên giao diện
            }
        } else {
            // === Đã đăng nhập → xóa khỏi UI và gọi cập nhật DB ===
            itemDiv.remove(); // Xóa trên giao diện
            updateCartFromMiniCart(); // Đồng bộ lại toàn bộ giỏ hàng với database
        }
    });



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
                renderMiniCart();
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
            const miniCart = document.getElementById('mini-cart');
            const toggleBtn = document.getElementById('toggle-cart');

            // ✅ Nếu click vào nút xoá sản phẩm trong mini-cart thì KHÔNG đóng giỏ hàng
            if (e.target.closest('.btn-delete-cart-item')) return;

            // ✅ Nếu click ngoài mini-cart và ngoài nút toggle thì mới đóng
            if (!miniCart.contains(e.target) && !toggleBtn.contains(e.target)) {
                miniCart.classList.add('d-none');
                miniCart.style.display = 'none';
            }
        });

    }
    setupMiniCartToggle();


    function renderCartPage() {
        console.log("Gọi renderCartPage");
        const cartContainer = document.getElementById('cart-items');
        const totalPriceEl = document.getElementById('total-price');

        if (user_id == null) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
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
                div.className = 'cart-item-wrap cart-item-wrap-page d-flex gap-3 border-bottom py-3 align-items-center';

                const imagePath = item.image?.includes('/')
                    ? item.image
                    : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;

                div.innerHTML = `
                    <input type="checkbox" class="form-check-input select-item me-3" data-index="${index}" style="border: 1px solid black;">
                    <img src="${imagePath}" alt="" width="100" height="100" class="rounded" style="object-fit:cover;">
                    <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">${item.name}</h6>
                    <p class="mb-1 small">Color: ${COLOR_MAP[item.color_id]?.name || '(không màu)'}</p>
                    <p class="mb-1 small">Size: ${item.size || '(không size)'}</p>
                    <p class="fw-semibold text-danger">${item.price.toLocaleString()}₫</p>
                    <div class="d-inline-flex align-items-center border rounded px-2 py-1">
                        <button class="btn btn-sm btn-outline-secondary px-2 btn-minus">-</button>
                        <span class="qty mx-3" data-product-id="${item.product_id}" data-variant-id="${item.variant_id}">${item.quantity}</span>
                        <button class="btn btn-sm btn-outline-secondary px-2 btn-plus">+</button>
                    </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger ms-2 btn-delete-cart-item-in-page">
                    <i class="fa fa-trash"></i>
                    </button>
                `;

                cartContainer.appendChild(div);
            });

        }
        else {
            fetch('/ajax/get_cart.php')
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !Array.isArray(data.data) || data.data.length === 0) {
                        if (cartContainer) {
                            cartContainer.innerHTML = `
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-shopping-cart fa-2x mb-3"></i>
                                    <p class="mb-0 fw-bold fs-5">Giỏ hàng của bạn đang trống</p>
                                </div>
                            `;
                            totalPriceEl.textContent = '0₫';
                            return;
                        }
                    }

                    data.data.forEach((item, index) => {
                        const imagePath = item.image?.includes('/')
                            ? item.image
                            : `/assets/img/sanpham/${item.image || 'sp1.jpg'}`;

                        const div = document.createElement('div');
                        div.className = 'cart-item-wrap cart-item-wrap-page d-flex gap-3 border-bottom py-3 align-items-center';

                        div.innerHTML = `
                            <input type="checkbox" class="form-check-input select-item me-3" data-index="${index}" style="border: 1px solid black;" checked>
                            <img src="${imagePath}" alt="" width="100" height="100" class="rounded" style="object-fit:cover;">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">${item.name}</h6>
                                <p class="mb-1 small">Color: ${COLOR_MAP[item.color_id]?.name || '(không màu)'}</p>
                                <p class="mb-1 small">Size: ${item.size || '(không size)'}</p>
                                <p class="fw-semibold text-danger">${item.price.toLocaleString()}₫</p>
                                <div class="d-inline-flex align-items-center border rounded px-2 py-1">
                                    <button class="btn btn-sm btn-outline-secondary px-2 btn-minus">-</button>
                                    <span class="qty mx-3" data-product-id="${item.product_id}" data-variant-id="${item.variant_id}">${item.quantity}</span>
                                    <button class="btn btn-sm btn-outline-secondary px-2 btn-plus">+</button>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger ms-2 btn-delete-cart-item-in-page">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;

                        if (cartContainer) {
                            cartContainer.appendChild(div);

                        }
                    });

                    updateTongTien(); // ✅ Gọi tính tổng
                })
                .catch(error => {
                    console.error('Lỗi khi gọi get_cart.php:', error);
                    cartContainer.innerHTML = `<p class="text-danger">Không thể tải giỏ hàng</p>`;
                    totalPriceEl.textContent = '0₫';
                });
        }

    }

    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('select-all');

        // ✅ Khi tick checkbox "Chọn tất cả"
        if (selectAll) {

            selectAll.checked = true; // Gán trạng thái ban đầu
            document.querySelectorAll('.select-item').forEach(cb => {
                cb.checked = true;
            });
            updateTongTien();

            selectAll.addEventListener('change', () => {
                const checked = selectAll.checked;
                document.querySelectorAll('.select-item').forEach(cb => {
                    cb.checked = checked;
                });
                updateTongTien();
            });
        }

        // ✅ Khi tick từng sản phẩm
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('select-item')) {
                const allItems = document.querySelectorAll('.select-item');
                const selectedItems = document.querySelectorAll('.select-item:checked');

                // ✅ Nếu số lượng đã tick bằng tổng số thì cũng tick "select-all"
                if (selectAll) {
                    selectAll.checked = selectedItems.length === allItems.length;
                }

                updateTongTien();
            }
        });
    });


    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-delete-cart-item-in-page');
        if (!btn) return;

        const itemDiv = btn.closest('.cart-item-wrap');
        const index = [...document.querySelectorAll('#cart-items .cart-item-wrap')].indexOf(itemDiv);

        if (user_id == null) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            if (index >= 0 && cart[index]) {
                cart.splice(index, 1); // xóa khỏi mảng
                localStorage.setItem('cart', JSON.stringify(cart)); // cập nhật localStorage
                itemDiv.remove(); // xóa khỏi giao diện
                updateTongTien();
            }
        } else {
            // Nếu có user_id → xử lý thêm phần gọi API nếu bạn đã có backend tương ứng
            itemDiv.remove();
            updateCartFromMiniCart();
            updateTongTien();
        }
    });

    function updateTongTien() {
        const totalPriceEl = document.getElementById('total-price');
        if (!totalPriceEl) return;

        let total = 0;
        const cartItems = document.querySelectorAll('#cart-items .cart-item-wrap');

        cartItems.forEach((itemDiv) => {
            const checkbox = itemDiv.querySelector('.select-item');
            if (!checkbox || !checkbox.checked) return; // ✅ Chỉ tính nếu được chọn

            const qty = parseInt(itemDiv.querySelector('.qty')?.textContent || '0');
            const priceText = itemDiv.querySelector('.text-danger')?.textContent?.replace(/[₫,.]/g, '') || '0';
            const price = parseInt(priceText);

            if (!isNaN(qty) && !isNaN(price)) {
                total += qty * price;
            }
        });

        totalPriceEl.textContent = total.toLocaleString() + '₫';
    }

    async function addToCart(productId, productName, productPrice, colorId, colorName, image, variantId, sizeId, sizeName) {
        const finalImage = image?.trim() !== '' ? image : 'default.jpg';

        // Nếu chưa đăng nhập
        if (typeof user_id === 'undefined' || user_id == null) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');

            const existingIndex = cart.findIndex(
                item => item.id == productId && item.variant_id == variantId
            );

            if (existingIndex >= 0) {
                cart[existingIndex].quantity += 1;
            } else {
                cart.push({
                    id: productId.toString(),
                    name: productName,
                    price: parseInt(productPrice),
                    quantity: 1,
                    image: finalImage,
                    variant_id: parseInt(variantId),
                    color_id: colorId.toString(),
                    size: sizeName,
                    size_id: sizeId
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            renderMiniCart();
            showNotification("Đã thêm vào giỏ hàng!", "success");
        } else {
            // Nếu đã đăng nhập → gửi về server
            try {
                const res = await fetch('/ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: parseInt(productId),
                        color_id: parseInt(colorId),
                        size_id: parseInt(sizeId),
                        quantity: 1
                    })
                });

                const result = await res.json();
                if (result.success) {
                    renderMiniCart();
                    showNotification("Đã thêm vào giỏ hàng!", "success");
                } else {
                    showNotification("Thêm vào giỏ hàng thất bại!", "error");
                }
            } catch (err) {
                console.error("❌ Lỗi khi thêm vào giỏ:", err);
                showNotification("Lỗi khi thêm vào giỏ hàng.", "error");
            }
        }
    }

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('#btn-checkout');
        if (!btn) return;

        e.preventDefault();

        const selectedItems = document.querySelectorAll('.select-item:checked');
        if (selectedItems.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            return;
        }

        const checkData = [];

        selectedItems.forEach(cb => {
            const itemDiv = cb.closest('.cart-item-wrap');
            const qtyEl = itemDiv.querySelector('.qty');
            const quantity = parseInt(qtyEl?.textContent || '0');

            const product_id = itemDiv.getAttribute('data-product-id') || qtyEl?.getAttribute('data-product-id');
            const variant_id = itemDiv.getAttribute('data-variant-id') || qtyEl?.getAttribute('data-variant-id');
            const product_name = itemDiv.querySelector('h6')?.textContent || 'Không rõ tên';
            const size = itemDiv.querySelector('p:nth-child(3)')?.textContent.replace('Size:', '').trim();
            const color = itemDiv.querySelector('p:nth-child(2)')?.textContent.replace('Color:', '').trim();

            if (variant_id && quantity > 0) {
                checkData.push({
                    variant_id: parseInt(variant_id),
                    quantity,
                    product_name,
                    size,
                    color
                });
            }
        });

        if (checkData.length === 0) return;

        try {
            const res = await fetch('ajax/check_inventory.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: checkData })
            });

            const result = await res.json();

            if (!result.success) {
                alert('Đã xảy ra lỗi khi kiểm tra tồn kho!');
                return;
            }

            if (result.errors && result.errors.length > 0) {
                let message = '❌ Một số sản phẩm không đủ tồn kho:\n\n';
                result.errors.forEach(err => {
                    message += `• ${err.product_name} - ${err.color} - ${err.size} chỉ còn lại ${err.stock} sản phẩm\n`;
                });
                alert(message);
                return;
            }

            // ✅ Không có lỗi → tiếp tục thanh toán
            window.location.href = '/index.php?page=pay';

        } catch (err) {
            console.error('Lỗi khi kiểm tra tồn kho:', err);
            alert('Không thể kiểm tra tồn kho. Vui lòng thử lại.');
        }
    });

    window.addToCart = addToCart;
    renderCartPage();
})();