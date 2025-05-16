<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <?php if (hasPermission('Quản lý tài khoản ngân hàng', 'write')): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemTKNH">
            <i class="fa-solid fa-plus"></i> Thêm
        </button>
    <?php endif; ?>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm tài khoản" id="searchAccountBank">
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
                <th>Mã ngân hàng</th>
                <th>Số tài khoản</th>
                <th>Chủ tài khoản</th>
                <th>Trạng thái</th>
                <th>Mặc định</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="accountbank-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Model thêm tài khoản ngân hàng -->
<div class="modal fade" id="modalThemTKNH" tabindex="-1" aria-labelledby="modalThemTKNHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemTKNH" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Thêm Tài Khoản Ngân Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Mã ngân hàng</label>
                    <input type="text" class="form-control" id="bankCode" name="bank_code" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số tài khoản</label>
                    <input type="text" class="form-control" id="accountNumber" name="account_number" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chủ tài khoản</label>
                    <input type="text" class="form-control" id="accountName" name="account_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" id="isActive" name="is_active">
                        <option value="1" selected>Hoạt động</option>
                        <option value="0">Ngừng hoạt động</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="isDefault" name="is_default" value="1">
                    <label class="form-check-label" for="isDefault">
                        Chọn làm mặc định
                    </label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal xóa tài khoản ngân hàng -->
<div class="modal fade" id="modalXoaTKNH" tabindex="-1" aria-labelledby="modalXoaTKNHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaTKNH" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xóa Tài Khoản Ngân Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteAccountId" name="account_id">
                <div class="mb-3">
                    <label class="form-label">Mã ngân hàng</label>
                    <input type="text" class="form-control" id="deleteBankCode" name="bank_code" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số tài khoản</label>
                    <input type="text" class="form-control" id="deleteAccountNumber" name="account_number" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chủ tài khoản</label>
                    <input type="text" class="form-control" id="deleteAccountName" name="account_name" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Sửa tài khoản ngân hàng -->
<div class="modal fade" id="modalSuaTKNH" tabindex="-1" aria-labelledby="modalSuaTKNHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaTKNH" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Sửa Tài Khoản Ngân Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editAccountId" name="account_id">
                <div class="mb-3">
                    <label class="form-label">Mã ngân hàng</label>
                    <input type="text" class="form-control" id="editBankCode" name="bank_code" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số tài khoản</label>
                    <input type="text" class="form-control" id="editAccountNumber" name="account_number" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Chủ tài khoản</label>
                    <input type="text" class="form-control" id="editAccountName" name="account_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" id="editIsActive" name="is_active">
                        <option value="1">Hoạt động</option>
                        <option value="0">Ngừng hoạt động</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="editIsDefault" name="is_default" value="1">
                    <label class="form-check-label" for="editIsDefault">
                        Chọn làm mặc định
                    </label>
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

    function loadBankAccounts(page = 1, params = '') {
        const wrap = document.querySelector('.accountbank-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');

        fetch(`ajax/load_bankaccounts.php?page=${page}${params}`)
            .then(res => res.json())
            .then(data => {
                wrap.innerHTML = data.bankaccountHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                setupPagination();
            })
            .catch(err => console.error('Lỗi khi tải dữ liệu:', err));
    }
    loadBankAccounts(1);

    // Phân trang
    function setupPagination() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                loadBankAccounts(page, currentFilterParams);
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
                    loadBankAccounts(page, currentFilterParams);
                }
            });
        }
    }

    // Thêm tài khoản ngân hàng
    document.getElementById("formThemTKNH").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const bankCode = form.bank_code.value.trim();
        const accNum = form.account_number.value.trim();
        const accName = form.account_name.value.trim();
        const isActive = form.is_active.value;
        const isDefaultCheckbox = form.is_default;

        if (!bankCode || !accNum || !accName) {
            alert("Vui lòng nhập đầy đủ thông tin.");
            return;
        }

        if (isActive === '0' && isDefaultCheckbox.checked) {
            alert("Tài khoản ngừng hoạt động không thể đặt làm mặc định.");
            return;
        }

        const formData = new FormData(form);
        fetch("ajax/add_bankaccount.php", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemTKNH'));
                    modal.hide();
                    form.reset();
                    alert(data.message);
                    loadBankAccounts(1, currentFilterParams);
                } else {
                    alert(data.message || "Có lỗi khi thêm tài khoản.");
                }
            })
            .catch(err => alert("Lỗi kết nối tới máy chủ."));
    });

    // Mở modal XÓA
    document.querySelector('.accountbank-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-bankaccount');
        if (!btn) return;

        document.getElementById("deleteAccountId").value = btn.dataset.id || '';
        document.getElementById("deleteBankCode").value = btn.dataset.bankCode || '';
        document.getElementById("deleteAccountNumber").value = btn.dataset.accountNumber || '';
        document.getElementById("deleteAccountName").value = btn.dataset.accountName || '';

        const modal = new bootstrap.Modal(document.getElementById('modalXoaTKNH'));
        modal.show();
    });

    // Xác nhận xóa
    document.getElementById("formXoaTKNH").addEventListener("submit", function(e) {
        e.preventDefault();
        const id = document.getElementById("deleteAccountId").value;
        const formData = new FormData();
        formData.append("account_id", id);

        fetch("ajax/delete_bankaccount.php", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaTKNH'));
                modal.hide();
                alert(data.message);
                loadBankAccounts(1, currentFilterParams);
            });
    });

    // Mở modal SỬA
    document.querySelector('.accountbank-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-bankaccount');
        if (!btn) return;

        document.getElementById("editAccountId").value = btn.dataset.id || '';
        document.getElementById("editBankCode").value = btn.dataset.bankCode || '';
        document.getElementById("editAccountNumber").value = btn.dataset.accountNumber || '';
        document.getElementById("editAccountName").value = btn.dataset.accountName || '';
        document.getElementById("editIsActive").value = btn.dataset.isActive || '1';
        document.getElementById("editIsDefault").checked = btn.dataset.isDefault === '1';

        // ⚠ Disable "Chọn mặc định" nếu tài khoản đang ngưng hoạt động
        const isActive = btn.dataset.isActive;
        document.getElementById("editIsDefault").disabled = (isActive === '0');

        const modal = new bootstrap.Modal(document.getElementById('modalSuaTKNH'));
        modal.show();
    });

    // Lưu sửa
    document.getElementById("formSuaTKNH").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const isActive = form.is_active.value;
        const isDefaultCheckbox = form.is_default;

        if (isActive === '0' && isDefaultCheckbox.checked) {
            alert("Tài khoản ngừng hoạt động không thể đặt làm mặc định.");
            return;
        }

        const formData = new FormData(form);
        fetch("ajax/update_bankaccount.php", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaTKNH'));
                modal.hide();
                alert(data.message);
                loadBankAccounts(1, currentFilterParams);
            });
    });

    // Tìm kiếm
    document.getElementById("searchAccountBank").addEventListener("input", function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadBankAccounts(1, currentFilterParams);
    });
</script>