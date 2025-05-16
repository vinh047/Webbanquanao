let currentFilterString = "";
let lastFilterState = "";

document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.querySelector(".filter_loc form");
    const productContainer = document.getElementById("product-list");

    // Helper: build a canonical “state” string of only the real filters
    function getFilterState(form) {
        const raw = new FormData(form);
        const entries = Array.from(raw.entries())
            .filter(([k, v]) =>
                v.toString().trim() !== "" &&
                !["page", "q", "pageproduct"].includes(k)
            )
            .sort(([a], [b]) => a.localeCompare(b));
        return entries
            .map(([k, v]) => `${k}=${encodeURIComponent(v)}`)
            .join("|");
    }

    function fetchProducts(params = "", updateURL = true) {
        fetch("ajax/search_ajax.php?" + params)
            .then(r => r.text())
            .then(html => {
                // 1) render HTML
                productContainer.innerHTML = html;

                // 2) nếu .no-results xuất hiện => không gắn sự kiện, disable nút Lọc
                const noRes = productContainer.querySelector(".no-results");
                const filterBtn = filterForm.querySelector('[type="submit"]');
                if (noRes) {
                    filterBtn.disabled = true;
                    return;
                } else {
                    filterBtn.disabled = false;
                }

                // 3) gắn lại các sự kiện tương tác
                attachColorHoverEvents();
                attachAddToCartEvents();
                attachProductClickEvents();

                // 4) phân trang
                document.querySelectorAll(".page-link-custom").forEach(link => {
                    link.addEventListener("click", function (e) {
                        e.preventDefault();
                        const page = this.dataset.page;
                        const q = new URLSearchParams(window.location.search);
                        q.set("pageproduct", page);
                        fetchProducts(q.toString());
                        history.pushState(null, "", "index.php?page=search&" + q.toString());
                    });
                });

                // 5) enter trên ô pageInput
                const input = document.getElementById("pageInput");
                if (input) {
                    input.addEventListener("keypress", function (e) {
                        if (e.key === "Enter") {
                            e.preventDefault();
                            let p = parseInt(this.value);
                            const max = parseInt(this.max);
                            p = Math.min(Math.max(p, 1), max);
                            const q = new URLSearchParams(window.location.search);
                            q.set("pageproduct", p);
                            fetchProducts(q.toString());
                            history.pushState(null, "", "index.php?page=search&" + q.toString());
                        }
                    });
                }

                // 6) cập nhật tracker
                currentFilterString = params;
                lastFilterState      = getFilterState(filterForm);

                // 7) cập nhật URL nếu cần
                if (updateURL) {
                    history.pushState(null, "", "index.php?page=search&" + params);
                }
            })
            .catch(console.error);
    }

    // submit lọc
    filterForm.addEventListener("submit", function (e) {
        e.preventDefault();

        // ít nhất 1 tiêu chí
        const hasFl = Array.from(new FormData(this).entries())
            .some(([k, v]) => v.toString().trim() && !["page","q","pageproduct"].includes(k));
        if (!hasFl) {
            alert("Vui lòng chọn ít nhất một tiêu chí lọc trước khi nhấn Lọc");
            return;
        }

        // filter state mới
        const newState = getFilterState(this);
        if (newState === lastFilterState) {
            console.log("Không có thay đổi so với lựa chọn trước, không tải lại");
            return;
        }
        lastFilterState = newState;

        // xây queryString
        const fd = new FormData(this);
        fd.set("pageproduct", new URLSearchParams(window.location.search).get("pageproduct")||"1");
        const sq = new URLSearchParams(window.location.search).get("q");
        if (sq) fd.set("q", sq);
        const qs = formDataToQueryString(fd);

        fetchProducts(qs);
    });

    // sort nút
    document.querySelectorAll(".sort-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const sort = this.dataset.sort;
            document.querySelectorAll(".sort-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const fd = new FormData(filterForm);
            fd.set("sapxep", sort);
            fd.set("pageproduct", new URLSearchParams(window.location.search).get("pageproduct")||"1");
            const sq = new URLSearchParams(window.location.search).get("q");
            if (sq) fd.set("q", sq);

            const qs = formDataToQueryString(fd);
            if (qs === currentFilterString) {
                console.log("Sort trùng, không tải lại");
                return;
            }
            currentFilterString = qs;
            fetchProducts(qs);
        });
    });

    // sync form với URL
    const up = new URLSearchParams(window.location.search);
    syncFilterWithURL(up, filterForm);
    if (up.get("sapxep")) {
        document.querySelectorAll(".sort-btn").forEach(btn => {
            if (btn.dataset.sort === up.get("sapxep")) btn.classList.add("active");
        });
    }

    // fetch ban đầu
    const init = window.location.search.startsWith("?")
        ? window.location.search.slice(1)
        : "";
    fetchProducts(init, false);

    // popstate
    window.addEventListener("popstate", () => {
        const p = window.location.search.startsWith("?")
            ? window.location.search.slice(1)
            : "";
        fetchProducts(p, false);
    });

    // nút back tìm kiếm
    const btnBack = document.getElementById("btn-back-home");
    if (btnBack) {
        btnBack.addEventListener("click", e => {
            e.preventDefault();
            if (window.history.length > 1) window.history.back();
            else window.location.href = "index.php?page=search";
        });
    }
});

// chuyển FormData→query string
function formDataToQueryString(formData) {
    const p = {};
    for (const [k, v] of formData.entries()) {
        if (!v.trim()) continue;
        const key = k.endsWith("[]") ? k.slice(0,-2) : k;
        if (!p[key]) p[key] = [];
        p[key].push(v);
    }
    return Object.entries(p)
        .map(([k, vs]) => `${encodeURIComponent(k)}=${encodeURIComponent(vs.join(","))}`)
        .join("&");
}

// sync URL→form
function syncFilterWithURL(urlParams, form) {
    for (const [k,v] of urlParams.entries()) {
        if (["colors","sizes"].includes(k)) {
            v.split(",").forEach(val => {
                const cb = form.querySelector(`input[name="${k}[]"][value="${val}"]`);
                if (cb) {
                    cb.checked = true;
                    const box = document.querySelector(`.${k.slice(0,-1)}-option[data-${k.slice(0,-1)}-id="${val}"]`);
                    if (box) box.classList.add("selected");
                }
            });
        } else {
            const inp = form.querySelector(`[name="${k}"]`);
            if (inp) inp.value = v;
        }
    }
}
// Các hàm attachColorHoverEvents, attachAddToCartEvents, attachProductClickEvents, addToCart
// giữ nguyên như trước...

// Thêm sự kiện hover thay đổi ảnh màu sắc
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

// Thêm sự kiện nút thêm vào giỏ hàng
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

// Thêm sự kiện click ảnh sản phẩm để đi tới chi tiết
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

// Hàm giả định addToCart (bạn cần implement riêng)
function addToCart(id, name, price, image, variant_id, colorName, sizeName) {
    // Thêm sản phẩm vào giỏ hàng logic ở đây
    console.log(`Add to cart: ${name} - ${colorName} - ${sizeName} - ${price} đ`);
}
