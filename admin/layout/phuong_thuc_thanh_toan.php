<div class="d-flex align-items-center justify-content-between mt-4 mb-4 position-relative">
    <!-- Nút quay lại bên trái -->
    <div class="position-absolute start-0">
        <a href="ajax/clear_subpage.php" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <!-- Tiêu đề căn giữa -->
    <h4 class="mx-auto mb-0 text-center text-primary fw-semibold text-uppercase fs-4 border-bottom border-2 pb-2" style="max-width: fit-content;">
        <i class="fa-solid fa-credit-card me-2"></i> Quản lý phương thức thanh toán
    </h4>
</div>

<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemPTTT">
        <i class="fa-solid fa-plus"></i> Thêm
    </button>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm phương thức thanh toán" id="searchPaymentMethod">
            <button class="btn btn-secondary" style="pointer-events: none;">
                <i class="fa-solid fa-search"></i>
            </button>
        </div>
    </div>


</div>


<div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên phương thức</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="payment-method-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Phương Thức Thanh Toán -->
<div class="modal fade" id="modalThemPTTT" tabindex="-1" aria-labelledby="modalThemPTTTLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemPTTT" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemPTTTLabel">Thêm phương thức thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tenPTTT" class="form-label">Tên phương thức thanh toán</label>
                    <input type="text" class="form-control" id="tenPTTT" name="name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Sửa Phương Thức Thanh Toán -->
<div class="modal fade" id="modalSuaPTTT" tabindex="-1" aria-labelledby="modalSuaPTTTLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaPTTT" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuaPTTTLabel">Sửa phương thức thanh toán</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" id="idPTTTSua" name="payment_method_id" readonly>
                </div>

                <div class="mb-3">
                    <label for="tenPTTTSua" class="form-label">Tên phương thức thanh toán</label>
                    <input type="text" class="form-control" id="tenPTTTSua" name="name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<script>
    let currentFilterParams = '';

    function loadPaymentMethods(page = 1, params = "") {
        const paymentMethodWrap = document.querySelector('.payment-method-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_payment_methods.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                paymentMethodWrap.innerHTML = data.paymentMethodHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadPaymentMethods(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadPaymentMethods(currentPage);
            });
        });
        const input = document.getElementById("pageInput");
        if (input) {
            input.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    let page = parseInt(this.value);
                    const max = parseInt(this.max);

                    if (page < 1) page = 1;
                    if (page > max) page = max;

                    if (page >= 1 && page <= max) {
                        currentPage = page;
                        loadPaymentMethods(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Xử lý tìm kiểm tên 
    document.querySelector('#searchPaymentMethod').addEventListener('input', function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadPaymentMethods(1, currentFilterParams);
    });

    // Sửa phưuong thưucs thanh toán
    document.querySelector('.payment-method-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-payment-method');
        if (btn) {
            // Lấy dữ liệu từ nút sửa
            const paymentMethodId = btn.dataset.id;
            const name = btn.dataset.name;

            // Gán vào modal sửa
            document.getElementById('idPTTTSua').value = paymentMethodId || '';
            document.getElementById('tenPTTTSua').value = name || '';
        }
    });

    document.getElementById('formSuaPTTT').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('ajax/update_payment_method.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaPTTT'));
                    modal.hide();

                    alert(data.message);

                    loadPaymentMethods(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            })
    });
</script>