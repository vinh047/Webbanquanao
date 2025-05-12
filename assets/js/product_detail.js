document.querySelectorAll('.color-option').forEach(item => {
    item.addEventListener('click', function () {
        // Xóa active của các lựa chọn khác
        document.querySelectorAll('.color-option').forEach(option => option.classList.remove('active'));

        // Thêm active vào item vừa click
        this.classList.add('active');
    });

    item.addEventListener('mouseover', function () {
        let src = item.querySelector('img').src;
        document.querySelector('.img-main').src = src;
    });

    item.addEventListener('mouseout', function () {
        let activeOption = document.querySelector('.color-option.active'); // Tìm phần tử đang active
        if (activeOption) {
            document.querySelector('.img-main').src = activeOption.querySelector('img').src; // Lấy ảnh của phần tử active
        }
    });
});

const sizeContainer = document.querySelector('.size-wrap');

if (sizeContainer) {
    sizeContainer.addEventListener('click', function (event) {
        const clicked = event.target.closest('.size-option');

        if (clicked) {
            // Xóa active của tất cả các size-option
            sizeContainer.querySelectorAll('.size-option').forEach(option =>
                option.classList.remove('active')
            );

            // Thêm active vào item vừa click
            clicked.classList.add('active');
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const btnUp = document.querySelector('.up');
    const btnDown = document.querySelector('.down');
    const input = document.querySelector('.input-qty');

    btnUp.addEventListener('click', function () {
        input.value = parseInt(input.value) + 1;
    });

    btnDown.addEventListener('click', function () {
        let current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const slider = document.querySelector('.suggest-products-scroll');
    const btnLeft = document.querySelector('.scroll-left');
    const btnRight = document.querySelector('.scroll-right');
    let isDown = false;
    let startX;
    let scrollLeft;
    let moved = false;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        moved = false;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
        slider.style.scrollBehavior = 'auto';
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX);
        if (Math.abs(walk) > 5) {
            moved = true;
            slider.scrollLeft = scrollLeft - walk;
        }
    });

    slider.addEventListener('mouseup', (e) => {
        if (moved) {
            // Nếu kéo, thì gắn chặn click 1 lần
            const preventClick = (ev) => {
                ev.stopImmediatePropagation();
                ev.preventDefault();
                slider.removeEventListener('click', preventClick, true);
            };
            slider.addEventListener('click', preventClick, true);
        }
        isDown = false;
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
    });

    // Click chính thức vào sản phẩm
    slider.addEventListener('click', function (e) {
        const productItem = e.target.closest('.product-item');
        if (productItem) {
            const productId = productItem.getAttribute('data-id');
            if (productId) {
                window.location.href = `product_detail.php?product_id=${productId}`;
            }
        }
    });

    // Scroll trái/phải
    window.scrollSuggestProducts = function (direction) {
        if (!slider) return;
        const scrollAmount = 300;
        slider.style.scrollBehavior = 'smooth';
        slider.scrollLeft += direction * scrollAmount;
        setTimeout(checkScrollButton, 300);
    }

    function checkScrollButton() {
        if (!slider) return;
        if (slider.scrollLeft <= 0) {
            btnLeft.style.display = 'none';
        } else {
            btnLeft.style.display = '';
        }

        if (slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 1) {
            btnRight.style.display = 'none';
        } else {
            btnRight.style.display = '';
        }
    }

    checkScrollButton();
});



document.querySelector(".buynow").addEventListener("click", function (e) {
    e.preventDefault(); // Ngăn chặn hành vi mặc định nếu là thẻ <a>

    const productId = document.body.dataset.productId;
    const quantity = parseInt(document.getElementById("count").value.trim());

    const activeColor = document.querySelector(".color-option.active");
    const activeSize = document.querySelector(".size-option.active");

    if (!activeColor || !activeSize) {
        alert("Vui lòng chọn màu sắc và kích thước.");
        return;
    }

    const colorId = activeColor.dataset.colorId;
    const sizeId = activeSize.dataset.sizeId;

    fetch('../ajax/check_stock_variant.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            color_id: colorId,
            size_id: sizeId,
            quantity: quantity
        })
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            if (!result.success) {
                alert(result.message);
                return;
            }
        })
        .catch(function (error) {
            console.error("Lỗi fetch:", error);
            alert("Có lỗi khi thêm vào giỏ hàng.");
        });

    if (user_id == null) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Kiểm tra trùng sản phẩm
        const index = cart.findIndex(item => item.productId === productId && item.colorId === colorId && item.sizeId === sizeId);
        if (index !== -1) {
            cart[index].quantity += quantity;
        } else {
            cart.push({ productId, quantity, colorId, sizeId });
        }

        localStorage.setItem('cart', JSON.stringify(cart));

        // Hiển thị popup thông báo
        let popup = document.querySelector(".notice-add-to-cart");
        popup.classList.add("opacity-100", "translate-middle");
        setTimeout(() => {
            popup.classList.remove("opacity-100", "translate-middle");
        }, 2000);
    }

    else {
        fetch("../ajax/add_to_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                product_id: productId,
                color_id: colorId,
                size_id: sizeId,
                quantity: quantity
            })
        })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    console.error("Thêm vào giỏ thất bại:", result.message);
                } else {
                    window.location.href = "/index.php?page=giohang";
                }
            })
            .catch(error => {
                console.error("Lỗi fetch khi mua ngay:", error);
            });
    }


});


// Thêm vào giỏ hàng
document.querySelector(".add-to-cart").addEventListener("click", function () {
    const productId = document.body.dataset.productId;
    const quantity = parseInt(document.getElementById("count").value.trim());

    const activeColor = document.querySelector(".color-option.active");
    const activeSize = document.querySelector(".size-option.active");

    if (!activeColor || !activeSize) {
        alert("Vui lòng chọn màu sắc và kích thước.");
        return;
    }

    const colorId = activeColor.dataset.colorId;
    const sizeId = activeSize.dataset.sizeId;
    const sizeName = activeSize.textContent;

    // Kiểm tra tồn kho còn đủ không
    fetch('../ajax/check_stock_variant.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            color_id: colorId,
            size_id: sizeId,
            quantity: quantity
        })
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            if (!result.success) {
                alert(result.message);
                return;
            }
        })
        .catch(function (error) {
            console.error("Lỗi fetch:", error);
            alert("Có lỗi khi thêm vào giỏ hàng.");
        });

    // nếu chưa đăng nhập thfi thêm vào localStorage
    if (user_id == null) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        let variant = null;

        fetch('../ajax/get_variant_id.php', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                product_id: productId,
                color_id: colorId,
                size_id: sizeId
            })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (!result.success) {
                    alert(result.message);
                    return;
                }
                else {
                    variant = data.message;
                }
            })
            .catch(function (error) {
                console.error("Lỗi fetch:", error);
                alert("Có lỗi khi thêm vào giỏ hàng.");
            });
        // Kiểm tra trùng sản phẩm
        const index = cart.findIndex(item => item.variant_id === variant['variant_id']);
        if (index !== -1) {
            cart[index].quantity += quantity;
        } else {
            cart.push({ product_id: productId, quantity, color_id:colorId, size_id: sizeName, variant_id: variant_id, image: variant['image'], price: variant['price'], });
        }

        localStorage.setItem('cart', JSON.stringify(cart));

        // Hiển thị popup thông báo
        let popup = document.querySelector(".notice-add-to-cart");
        popup.classList.add("opacity-100", "translate-middle");
        setTimeout(() => {
            popup.classList.remove("opacity-100", "translate-middle");
        }, 2000);
    }

    else {
        fetch("../ajax/add_to_cart.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                product_id: productId,
                color_id: colorId,
                size_id: sizeId,
                quantity: quantity
            })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (result.success) {
                    // Hiển thị popup thông báo
                    let popup = document.querySelector(".notice-add-to-cart");
                    popup.classList.add("opacity-100", "translate-middle");
                    setTimeout(() => {
                        popup.classList.remove("opacity-100", "translate-middle");
                    }, 2000);
                } else {
                    alert(result.message || "Thêm vào giỏ hàng thất bại.");
                }
            })
            .catch(function (error) {
                console.error("Lỗi fetch:", error);
                alert("Có lỗi khi thêm vào giỏ hàng.");
            });
    }
});

let isLoading = false; // Biến kiểm tra trạng thái tải

// Ajax phân trang
function loadReviews(page = 1, rating = 'all') {
    if (isLoading) return;
    isLoading = true;

    const productId = document.querySelector('.review-list').dataset.productId;
    fetch('../ajax/load_reviews.php?product_id=' + productId + '&page=' + page + '&rating=' + rating)
        .then(responsive => responsive.text())
        .then(data => {
            const [html, pagination] = data.split('SPLIT');
            document.querySelector('.review-list').innerHTML = html;
            document.querySelector('.pagination_wrap').innerHTML = pagination;
        })
        .finally(() => {
            isLoading = false;
        })

}

document.addEventListener("DOMContentLoaded", function () {
    loadReviews();

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.classList.contains('pag')) {
            e.preventDefault();
            let page = parseInt(e.target.value) || 1;
            loadReviews(page);
        }
    });


    document.addEventListener("click", function (e) {
        e.stopImmediatePropagation();

        let rating = 'all';


        // Sự kiện của nút tăng giảm phân trang
        if (e.target.classList.contains("btn-prev")) {
            let page = document.querySelector('.pag');
            let i = parseInt(page.value);
            if (i > 1) {
                page.value = i - 1;
                loadReviews(i - 1, rating);
            }
        }

        if (e.target.classList.contains("btn-next")) {
            let page = document.querySelector('.pag');
            let i = parseInt(page.value);
            let max_pag = parseInt(document.querySelector('.max-pag').textContent);
            if (i < max_pag) {
                page.value = i + 1;
                loadReviews(i + 1, rating);
            }
        }

        // Sự kiện của nút đánh giá theo sao
        if (e.target.classList.contains('btn-star')) {
            // Xóa active của các lựa chọn khác
            document.querySelectorAll('.btn-star').forEach(option => option.classList.remove('active'));

            // Thêm active vào item vừa click
            e.target.classList.add('active');

            let selectRating = e.target.getAttribute('data-rating');
            loadReviews(1, selectRating);
        }
    });


});

document.querySelector('.suggest-products-scroll').addEventListener('click', function (e) {
    const productItem = e.target.closest('.product-item');
    if (productItem && this.contains(productItem)) {
        const productId = productItem.getAttribute('data-id');
        if (productId) {
            window.location.href = `product_detail.php?product_id=${productId}`;
        }
    }
});

document.querySelectorAll('.color-option').forEach(colorEl => {
    colorEl.addEventListener('click', function () {
        const colorId = this.dataset.colorId;
        const productId = document.body.dataset.productId;

        fetch('../ajax/get_sizes_by_color_product_detail.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ color_id: colorId, product_id: productId })
        })
            .then(res => res.json())
            .then(data => {
                const sizeContainer = document.querySelector('.size-option').parentElement;
                sizeContainer.innerHTML = '';
                data.forEach(size => {
                    const sizeDiv = document.createElement('div');
                    sizeDiv.className = 'size-option border py-2 px-3';
                    sizeDiv.textContent = size.size_name;
                    sizeDiv.dataset.sizeId = size.size_id;
                    sizeContainer.appendChild(sizeDiv);
                });
            })
            .catch(err => console.error('Lỗi khi tải size:', err));
    });
});