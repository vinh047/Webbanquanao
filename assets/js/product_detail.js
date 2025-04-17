document.querySelectorAll('.color-option').forEach(item => {
    item.addEventListener('click', function() {
        // Xóa active của các lựa chọn khác
        document.querySelectorAll('.color-option').forEach(option => option.classList.remove('active'));

        // Thêm active vào item vừa click
        this.classList.add('active');
    });

    item.addEventListener('mouseover', function() {
        let src = item.querySelector('img').src;
        document.querySelector('.img-main').src = src;
    });

    item.addEventListener('mouseout', function() {
        let activeOption = document.querySelector('.color-option.active'); // Tìm phần tử đang active
        if (activeOption) {
            document.querySelector('.img-main').src = activeOption.querySelector('img').src; // Lấy ảnh của phần tử active
        }
    });
});

document.querySelectorAll('.size-option').forEach(item => {
    item.addEventListener('click', function() {
        // Xóa active của các lựa chọn khác
        document.querySelectorAll('.size-option').forEach(option => option.classList.remove('active'));

        // Thêm active vào item vừa click
        this.classList.add('active');
    });
});

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


const slider = document.querySelector('.suggest-products-scroll');
let isDown = false;
let startX;
let scrollLeft;

slider.addEventListener('mousedown', (e) => {
    isDown = true;
    slider.classList.add('active');
    slider.style.scrollBehavior = 'auto';
    startX = e.pageX - slider.offsetLeft;
    scrollLeft = slider.scrollLeft;
});

slider.addEventListener('mouseleave', () => {
    isDown = false;
});

slider.addEventListener('mouseup', () => {
    isDown = false;
});

slider.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - slider.offsetLeft;
    const walk = (x - startX) * 1; // tốc độ kéo
    slider.scrollLeft = scrollLeft - walk;
});

function scrollSuggestProducts(direction) {
    const container = document.querySelector('.suggest-products-scroll');
    container.style.scrollBehavior = 'smooth';
    const scrollAmount = 300; // chỉnh khoảng cách cuộn
    container.scrollLeft += direction * scrollAmount;
}




document.querySelector(".add-to-cart").addEventListener("click", function() {
    let popup = document.querySelector(".notice-add-to-cart");
    popup.classList.add("show"); // Hiện popup

    // Ẩn popup sau 2 giây (tùy chỉnh thời gian)
    setTimeout(() => {
        popup.classList.remove("show");
    }, 2000);
});

let isLoading = false; // Biến kiểm tra trạng thái tải

// Ajax phân trang
function loadReviews(page = 1, rating = 'all') {
    if(isLoading) return;
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

    document.addEventListener("DOMContentLoaded", function() {
        loadReviews();
        
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Enter' && e.target.classList.contains('pag')) {
                e.preventDefault();
                let page = parseInt(e.target.value) || 1;
                loadReviews(page);
            }
        });
        
        
        document.addEventListener("click", function(e) {
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
            if(e.target.classList.contains('btn-star')) {
                // Xóa active của các lựa chọn khác
                document.querySelectorAll('.btn-star').forEach(option => option.classList.remove('active'));

                // Thêm active vào item vừa click
                e.target.classList.add('active');

                let selectRating = e.target.getAttribute('data-rating');
                loadReviews(1, selectRating);
            }
        });

    
});

document.querySelector('.suggest-products-scroll').addEventListener('click', function(e) {
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

        fetch('../ajax/get_sizes_by_color.php', {
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
                sizeContainer.appendChild(sizeDiv);
            });
        })
        .catch(err => console.error('Lỗi khi tải size:', err));
    });
});