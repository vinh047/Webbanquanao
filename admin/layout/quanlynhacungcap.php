<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <?php if (hasPermission('Quản lý nhà cung cấp', 'write')): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemNCC">
            <i class="fa-solid fa-plus"></i> Thêm
        </button>
    <?php endif; ?>
    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm nhà cung cấp" id="searchSupplier">
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
                <th>Tên nhà cung cấp</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="supplier-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Nhà Cung Cấp -->
<div class="modal fade" id="modalThemNCC" tabindex="-1" aria-labelledby="modalThemNCCLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemNCC" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemNCCLabel">Thêm Nhà Cung Cấp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tenNCC" class="form-label">Tên nhà cung cấp</label>
                    <input type="text" class="form-control" id="tenNCC" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="emailNCC" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailNCC" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="diachiNCC" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="diachiNCC" name="address" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal xóa nhà cung cấp -->
<div class="modal fade" id="modalXoaNCC" tabindex="-1" aria-labelledby="modalXoaNCCLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaNCC" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalXoaNCCLabel">Xóa Nhà Cung Cấp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="maNCC" class="form-label">ID</label>
                    <input type="text" class="form-control" id="maNCCXoa" name="id" readonly>
                </div>
                <div class="mb-3">
                    <label for="tenNCC" class="form-label">Tên nhà cung cấp</label>
                    <input type="text" class="form-control" id="tenNCCXoa" name="name" readonly>
                </div>
                <div class="mb-3">
                    <label for="emailNCC" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailNCCXoa" name="email" readonly>
                </div>
                <div class="mb-3">
                    <label for="diachiNCC" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="diachiNCCXoa" name="address" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sửa Nhà Cung Cấp -->
<div class="modal fade" id="modalSuaNCC" tabindex="-1" aria-labelledby="modalSuaNCCLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaNCC" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuaNCCLabel">Sửa Nhà Cung Cấp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" id="idSua" name="supplier_id" readonly>
                </div>

                <div class="mb-3">
                    <label for="tenNCCSua" class="form-label">Tên nhà cung cấp</label>
                    <input type="text" class="form-control" id="tenNCCSua" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="emailNCCSua" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailNCCSua" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="diachiNCCSua" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="diachiNCCSua" name="address" required>
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

    function loadSuppliers(page = 1, params = "") {
        const supplierWrap = document.querySelector('.supplier-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_suppliers.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                supplierWrap.innerHTML = data.supplierHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadSuppliers(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadSuppliers(currentPage);
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
                        loadSuppliers(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Thêmm nhà cung cấp
    document.getElementById("formThemNCC").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const address = form.address.value.trim();

        // Kiểm tra rỗng
        if (!name || !email || !address) {
            alert("Vui lòng nhập đầy đủ thông tin nhà cung cấp.");
            return;
        }

        const formData = new FormData(form);

        fetch('ajax/add_supplier.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemNCC'));
                    modal.hide();

                    // Reset form
                    form.reset();

                    alert(data.message);

                    // Reload danh sách nhà cung cấp
                    loadSuppliers(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi thêm nhà cung cấp.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Xóa nhà cung cấp
    document.querySelector('.supplier-wrap').addEventListener('click', (e) => {
        if (e.target.closest('.btn-delete-supplier')) {
            const button = e.target.closest('.btn-delete-supplier');

            const id = button.dataset.supplierId;
            const name = button.dataset.name;
            const email = button.dataset.email;
            const address = button.dataset.address;

            console.log(id + " " + name)
            document.getElementById('maNCCXoa').value = id || '';
            document.getElementById('tenNCCXoa').value = name || '';
            document.getElementById('emailNCCXoa').value = email || '';
            document.getElementById('diachiNCCXoa').value = address || '';

        }
    });

    document.getElementById('formXoaNCC').addEventListener('submit', function(e) {
        e.preventDefault();

        const supplier_id = document.querySelector('#maNCCXoa').value;
        const formData = new FormData();
        formData.append('supplier_id', supplier_id);

        fetch('ajax/delete_supplier.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaNCC'));
                    modal.hide();

                    alert(data.message);

                    loadSuppliers(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            })
    });

    // Sửa nhà cung cấp
    document.querySelector('.supplier-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-supplier');
        if (!btn) return;

        // Lấy dữ liệu từ nút sửa
        const supplierId = btn.dataset.id;
        const name = btn.dataset.name;
        const email = btn.dataset.email;
        const address = btn.dataset.address;

        // Gán vào modal sửa
        document.getElementById('idSua').value = supplierId || '';
        document.getElementById('tenNCCSua').value = name || '';
        document.getElementById('emailNCCSua').value = email || '';
        document.getElementById('diachiNCCSua').value = address || '';
    });

    document.getElementById('formSuaNCC').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('ajax/update_supplier.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaNCC'));
                    modal.hide();

                    alert(data.message);

                    loadSuppliers(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            })
    });

    // Xử lý tìm kiểm tên nhà cung cấp
    document.querySelector('#searchSupplier').addEventListener('input', function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadSuppliers(1, currentFilterParams);
    });
</script>