document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector(".filter_loc form");
    const productContainer = document.getElementById("product-list");

    function fetchProducts(params = "", updateURL = true) {
        fetch("/Webbanquanao/ajax/product_ajax.php?" + params)
            .then(response => response.text())
            .then(data => {
                productContainer.innerHTML = data;

                // Cập nhật URL
                // if (updateURL) {
                //     const newURL = window.location.pathname + "?page=sanpham&" + params;
                //     history.pushState(null, "", newURL);
                // }

                // Gắn lại sự kiện cho phân trang
                document.querySelectorAll(".page-link-custom").forEach(link => {
                    link.addEventListener("click", function (e) {
                        e.preventDefault();
                        const page = this.dataset.page;
                        const formData = new FormData(filterForm);
                        formData.append("page", page);
                        fetchProducts(new URLSearchParams(formData).toString());
                    });
                });
            });
    }

    // Gửi AJAX khi bấm nút "Lọc"
    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetchProducts(new URLSearchParams(formData).toString());
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
    const params = currentSearch.replace("?page=sanpham&", ""); // bỏ phần đầu giữ phần sau
    if (params) {
        // Nếu có filter trên URL → fetch theo
        fetchProducts(params, false); // false = không cập nhật URL lại nữa
    } else {
        // Nếu không có filter → gọi mặc định
        fetchProducts();
    }
});
