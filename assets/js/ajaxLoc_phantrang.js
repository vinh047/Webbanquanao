document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector(".filter_loc form");
    const productContainer = document.getElementById("product-list");
    let currentSort = "";

    function fetchProducts(params = "", updateURL = true) {
        fetch("../ajax/product_ajax.php?" + params)
            .then(response => response.text())
            .then(data => {
                if (data === 'REDIRECT_TO_HOME') {
                    window.location.href = "index.php?page=error";
                    return;
                }

                productContainer.innerHTML = data;

                // ⏳ Gọi lại filter sync nếu có urlParams
                const currentSearch = window.location.search;
                const urlParams = new URLSearchParams(currentSearch);
                
                // Gắn lại sự kiện cho phân trang
                document.querySelectorAll(".page-link-custom").forEach(link => {
                    link.addEventListener("click", function (e) {
                        e.preventDefault();
                        const page = this.dataset.page;
                        const query = new URLSearchParams(window.location.search);
                        query.set("pageproduct", page);

                        fetchProducts(query.toString());

                        const newURL = window.location.pathname + "?" + query.toString();
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
                            if (page < 1) page = 1;
                            if (page > max) page = max;

                            const query = new URLSearchParams(window.location.search);
                            query.set("pageproduct", page);

                            fetchProducts(query.toString());

                            const newURL = window.location.pathname + "?" + query.toString();
                            history.pushState(null, "", newURL);
                        }
                    });
                }
            });
    }

    // Gửi AJAX khi bấm nút "Lọc"
    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();
    
        const formData = new FormData(this);
        formData.delete("page");
    
        // 👉 Lấy trang hiện tại
        const currentPage = new URLSearchParams(window.location.search).get("pageproduct") || "1";
        formData.set("pageproduct", currentPage);
    
        const queryString = formDataToQueryString(formData);
        fetchProducts(queryString);
    
        // 👉 Sắp xếp lại thứ tự: page -> pageproduct -> còn lại
        const oldParams = new URLSearchParams(queryString);
        const queryParams = new URLSearchParams();
    
        queryParams.set("page", "sanpham");
        queryParams.set("pageproduct", currentPage);
    
        for (const [key, value] of oldParams.entries()) {
            if (key !== "page" && key !== "pageproduct") {
                queryParams.append(key, value);
            }
        }
    
        const newURL = window.location.pathname + "?" + queryParams.toString();
        history.replaceState(null, "", newURL);
    });
    
    
    
    
    
    
    
    

    // Sắp xếp
    document.querySelectorAll(".sort-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const sapxep = this.dataset.sort;
    
            // Nếu đã được chọn => hủy sắp xếp
            if (this.classList.contains("active")) {
                this.classList.remove("active");
                currentSort = "";
    
                const formData = new FormData(filterForm);

                // ✅ Thêm dòng này để giữ lại page hiện tại
                const currentPage = new URLSearchParams(window.location.search).get("pageproduct") || "1";
                formData.set("pageproduct", currentPage);
                
                const queryString = formDataToQueryString(formData);
                fetchProducts(queryString);
                
                const queryParams = new URLSearchParams(queryString);
                queryParams.set("page", "sanpham");
                
                // ✅ Đảm bảo giữ pageproduct hiện tại
                queryParams.set("pageproduct", currentPage);
                
                const newURL = window.location.pathname + "?" + queryParams.toString();
                history.pushState(null, "", newURL);
                
            } else {
                // Chọn sắp xếp mới
                currentSort = sapxep;
    
                document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
                this.classList.add("active");
    
                const formData = new FormData(filterForm);
                formData.append("sapxep", sapxep);
                
                // ✅ Thêm dòng này: lấy page hiện tại từ URL
                const currentPage = new URLSearchParams(window.location.search).get("pageproduct") || "1";
                formData.set("pageproduct", currentPage);
                
                const queryString = formDataToQueryString(formData);
                fetchProducts(queryString);
    
                const queryParams = new URLSearchParams(queryString);
                queryParams.set("page", "sanpham");
                const newURL = window.location.pathname + "?" + queryParams.toString();
                                history.pushState(null, "", newURL);
            }
        });
    });
    

    // Khi load trang (F5), lấy lại filter từ URL
    const currentSearch = window.location.search;
    const params = currentSearch.startsWith("?") ? currentSearch.substring(1) : "";
    const urlParams = new URLSearchParams(params);

    if (urlParams.has("sapxep")) {
        currentSort = urlParams.get("sapxep");
    }

    document.querySelectorAll(".sort-btn").forEach(btn => {
        if (btn.dataset.sort === currentSort) {
            btn.classList.add("active");
        } else {
            btn.classList.remove("active");
        }
    });

    if (params) {
        fetchProducts(params, false);
    } else {
        fetchProducts();
    }
    // document.querySelectorAll('.size-option').forEach(option => {
    //     option.addEventListener('click', () => {
    //         option.classList.toggle('selected');
    //         const sizeId = option.getAttribute('data-size-id');
    //         const checkbox = document.querySelector(`input.size-checkbox[value="${sizeId}"]`);
    //         if (checkbox) {
    //             checkbox.checked = option.classList.contains('selected');
    //         }
    //     });
    // });
    
    
    
    
    
    // ✅ Gán lại các giá trị lọc vào form sau khi F5
    syncFilterWithURL(urlParams, filterForm);


});




function syncFilterWithURL(urlParams, filterForm) {
    for (const [key, value] of urlParams.entries()) {
        if (key === 'colors') {
            const values = value.split(',');
            values.forEach(val => {
                const checkbox = filterForm.querySelector(`input[name="colors[]"][value="${val}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    checkbox.defaultChecked = true;

                    // ✅ Gắn class .selected cho color-option theo data-color-id
                    const colorBox = document.querySelector(`.color-option[data-color-id="${val}"]`);
                    if (colorBox) {
                        colorBox.classList.add('selected');
                    }
                }
            });
        } else if (key === 'sizes') {
            const values = value.split(',');
            values.forEach(val => {
                const checkbox = filterForm.querySelector(`input[name="sizes[]"][value="${val}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                    checkbox.defaultChecked = true;
        
                    const sizeBox = document.querySelector(`.size-option[data-size-id="${val}"]`);
                    if (sizeBox) {
                        sizeBox.classList.add('selected');
                    }
                }
            });
        }
         else {
            const input = filterForm.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = value;
                input.defaultValue = value;
            }
        }
    }
}



function formDataToQueryString(formData) {
    const params = {};

    for (const [key, value] of formData.entries()) {
        if (value.trim() === "") continue;

        const cleanKey = key.endsWith("[]") ? key.slice(0, -2) : key;

        if (!params[cleanKey]) {
            params[cleanKey] = [];
        }

        params[cleanKey].push(value);
    }

    console.log("🧪 Dữ liệu form gửi đi:", params);

    return Object.entries(params)
        .map(([key, values]) => `${encodeURIComponent(key)}=${encodeURIComponent(values.join(","))}`)
        .join("&");
}





