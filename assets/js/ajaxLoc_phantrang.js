document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector(".filter_loc form");
    const productContainer = document.getElementById("product-list");

    function fetchProducts(params = "", updateURL = true) {
        fetch("/Webbanquanao/ajax/product_ajax.php?" + params)
            .then(response => response.text())
            .then(data => {

                if(data === 'REDIRECT_TO_HOME')
                {
                    window.location.href = "/Webbanquanao/index.php?page=error";
                    return;                    
                }

                productContainer.innerHTML = data;
                

                // Gắn lại sự kiện cho phân trang
// Gắn lại sự kiện cho phân trang
document.querySelectorAll(".page-link-custom").forEach(link => {
    link.addEventListener("click", function (e) {
        e.preventDefault();
        const page = this.dataset.page;
        const formData = new FormData(filterForm);
        formData.append("pageproduct", page);
        fetchProducts(new URLSearchParams(formData).toString());

        // ✅ Cập nhật URL gọn
        const newURL = window.location.pathname + "?page=sanpham&pageproduct=" + page;
        history.pushState(null, "", newURL);
    });
});

const input = document.getElementById("pageInput");
if (input) {
    input.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            let page = parseInt(this.value);
            const max = parseInt(this.max);

            if(page < 1)
            {
                page = 1;
            }

            if (page > max) {
                page = max;
            }

            if (page >= 1 && page <= max) {
                // Chỉ truyền mỗi pageproduct thôi
                fetchProducts("pageproduct=" + page);

                // Cập nhật URL gọn
                const newURL = window.location.pathname + "?page=sanpham&pageproduct=" + page;
                history.pushState(null, "", newURL);
            }
        }
    });
}



            });
    }

    // Gửi AJAX khi bấm nút "Lọc"
    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        const queryString = formDataToQueryString(formData); // dùng hàm mới
    
        fetchProducts(queryString);
    
        // ✅ Cập nhật URL đẹp
        const newURL = window.location.pathname + "?page=sanpham&" + queryString;
        history.pushState(null, "", newURL);
    });
    

    // Sắp xếp
    document.querySelectorAll(".sort-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const sapxep = this.dataset.sort;
            const formData = new FormData(filterForm);
            formData.append("sapxep", sapxep);
            fetchProducts(new URLSearchParams(formData).toString());
        });
    });

    // ✅ Khi load trang (F5), lấy lại filter từ URL
    const currentSearch = window.location.search;
    const params = currentSearch.startsWith("?") ? currentSearch.substring(1) : "";
    // fetchProducts(params, false);
     // bỏ phần đầu giữ phần sau
    if (params) {
        // Nếu có filter trên URL → fetch theo
        fetchProducts(params, false); // false = không cập nhật URL lại nữa
    } else {
        // Nếu không có filter → gọi mặc định
        fetchProducts();
    }
});

function formDataToQueryString(formData) {
    const params = {};

    for (const [key, value] of formData.entries()) {
        if (params[key]) {
            if (Array.isArray(params[key])) {
                params[key].push(value);
            } else {
                params[key] = [params[key], value];
            }
        } else {
            params[key] = value;
        }
    }

    const query = Object.entries(params)
        .map(([key, val]) => {
            if (Array.isArray(val)) {
                return `${key}=${val.join(',')}`;
            } else {
                return `${key}=${val}`;
            }
        })
        .join('&');

    return query;
}





