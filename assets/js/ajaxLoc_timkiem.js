document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector(".filter_loc form");
    const productContainer = document.getElementById("product-list");
    let currentSort = "";

    function fetchProducts(params = "", updateURL = true) {
        fetch("ajax/search_ajax.php?" + params)
            .then(response => response.text())
            .then(data => {
                productContainer.innerHTML = data;

                attachColorHoverEvents();
                attachAddToCartEvents();
                attachProductClickEvents();

                // Gán lại sự kiện phân trang
                document.querySelectorAll(".page-link-custom").forEach(link => {
                    link.addEventListener("click", function (e) {
                        e.preventDefault();
                        const page = this.dataset.page;
                        const query = new URLSearchParams(window.location.search);
                        query.set("pageproduct", page);
                        fetchProducts(query.toString());
                        history.pushState(null, "", "index.php?page=search&" + query.toString());
                    });
                });

                // Gán lại enter input page
                const input = document.getElementById("pageInput");
                if (input) {
                    input.addEventListener("keypress", function (e) {
                        if (e.key === "Enter") {
                            e.preventDefault();
                            let page = parseInt(this.value);
                            const max = parseInt(this.max);
                            if (page < 1) page = 1;
                            if (page > max) page = max;
                            const query = new URLSearchParams(window.location.search);
                            query.set("pageproduct", page);
                            fetchProducts(query.toString());
                            history.pushState(null, "", "index.php?page=search&" + query.toString());
                        }
                    });
                }
            });
    }

    // Khi submit lọc
    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();
    
        // 1. Lấy FormData gốc để kiểm tra filter (chưa delete bất cứ thứ gì)
        const rawForm = new FormData(this);
    
        // 2. Lọc ra những entry thực sự là filter: bỏ pageproduct, q và bỏ giá trị rỗng
        const filterEntries = Array.from(rawForm.entries()).filter(([key, val]) => {
            if (key === "page" || key === "pageproduct" || key === "q") return false;
            return val.toString().trim() !== "";
        });
    
        // 3. Nếu không có bất kỳ filter nào được chọn thì cấm submit
        if (filterEntries.length === 0) {
            alert("Vui lòng chọn ít nhất một tiêu chí lọc trước khi nhấn Lọc");
            return;  // dừng luôn, không load lại dữ liệu hay thay đổi URL
        }
    
        // 4. Ngược lại build FormData và gửi request như bình thường
        const formData = new FormData(this);
        formData.delete("page");
    
        const currentPage = new URLSearchParams(window.location.search).get("pageproduct") || "1";
        formData.set("pageproduct", currentPage);
    
        const searchQuery = new URLSearchParams(window.location.search).get("q");
        if (searchQuery) {
            formData.set("q", searchQuery);
        }
    
        const queryString = formDataToQueryString(formData);
        fetchProducts(queryString);
        history.pushState(null, "", "index.php?page=search&" + queryString);
    });
    

    // Sắp xếp
    document.querySelectorAll(".sort-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const sapxep = this.dataset.sort;

            document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const formData = new FormData(filterForm);
            formData.set("sapxep", sapxep);

            const currentPage = new URLSearchParams(window.location.search).get("pageproduct") || "1";
            formData.set("pageproduct", currentPage);

            const searchQuery = new URLSearchParams(window.location.search).get("q");
            if (searchQuery) {
                formData.set("q", searchQuery);
            }

            const queryString = formDataToQueryString(formData);
            fetchProducts(queryString);

            const newURL = "index.php?page=search&" + queryString;
            history.pushState(null, "", newURL);
        });
    });

    // Gán lại giá trị từ URL khi F5
    const urlParams = new URLSearchParams(window.location.search);
    syncFilterWithURL(urlParams, filterForm);
    if (urlParams.get("sapxep")) {
        document.querySelectorAll(".sort-btn").forEach(btn => {
            if (btn.dataset.sort === urlParams.get("sapxep")) {
                btn.classList.add("active");
            }
        });
    }

    const params = window.location.search.startsWith("?") ? window.location.search.substring(1) : "";
    if (params) {
        fetchProducts(params);
    } else {
        fetchProducts();
    }
    window.addEventListener("popstate", function () {
        const urlParams = window.location.search.startsWith("?")
            ? window.location.search.substring(1)
            : "";
    
        // Gọi lại fetchProducts để cập nhật giao diện đúng với URL hiện tại
        fetchProducts(urlParams);
    });
});

function formDataToQueryString(formData) {
    const params = {};
    for (const [key, value] of formData.entries()) {
        if (value.trim() === "") continue;
        const cleanKey = key.endsWith("[]") ? key.slice(0, -2) : key;
        if (!params[cleanKey]) params[cleanKey] = [];
        params[cleanKey].push(value);
    }
    return Object.entries(params)
        .map(([key, values]) => `${encodeURIComponent(key)}=${encodeURIComponent(values.join(","))}`)
        .join("&");
}

function syncFilterWithURL(urlParams, filterForm) {
    for (const [key, value] of urlParams.entries()) {
        if (key === 'colors') {
            const values = value.split(',');
            values.forEach(val => {
                const checkbox = filterForm.querySelector(`input[name="colors[]"][value="${val}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    const box = document.querySelector(`.color-option[data-color-id="${val}"]`);
                    if (box) box.classList.add("selected");
                }
            });
        } else if (key === 'sizes') {
            const values = value.split(',');
            values.forEach(val => {
                const checkbox = filterForm.querySelector(`input[name="sizes[]"][value="${val}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    const box = document.querySelector(`.size-option[data-size-id="${val}"]`);
                    if (box) box.classList.add("selected");
                }
            });
        } else {
            const input = filterForm.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = value;
            }
        }
    }
}

function attachColorHoverEvents() {
    document.querySelectorAll(".color-thumb").forEach(img => {
        const productId = img.dataset.productId;
        const newSrc = img.dataset.image;

        img.addEventListener("mouseover", () => {
            const mainImg = document.querySelector(`#main-image-${productId}`);
            if (mainImg) {
                mainImg.style.opacity = "0";
                mainImg.style.transform = "translateX(-20px)";
                setTimeout(() => {
                    mainImg.src = newSrc;
                    mainImg.style.transform = "translateX(0)";
                    mainImg.style.opacity = "1";
                }, 200);
            }
        });

        img.addEventListener("click", () => {
            document.querySelectorAll(`.color-thumb[data-product-id="${productId}"]`).forEach(el => {
                el.classList.remove("selected");
            });
            img.classList.add("selected");
        });
    });
}

function attachAddToCartEvents() {
    document.querySelectorAll(".btn-add-to-cart").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const image = this.dataset.image;
            const variant_id = this.dataset.variantId;

            const selectedColor = document.querySelector(".color-option.selected");
            const colorName = selectedColor?.title || "Màu";

            const selectedSize = document.querySelector(".size-option.selected");
            const sizeName = selectedSize?.title || "Size";

            addToCart(id, name, price, image, variant_id, colorName, sizeName);
        });
    });
}

function attachProductClickEvents() {
    document.querySelectorAll("img[id^='main-image-']").forEach(img => {
        img.addEventListener("click", function () {
            const idParts = this.id.split("-");
            const productId = idParts[idParts.length - 1];
            if (productId) {
                window.location.href = `layout/product_detail.php?product_id=${productId}`;
            }
        });
    });
}
