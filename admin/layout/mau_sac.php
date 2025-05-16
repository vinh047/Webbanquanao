<div class="d-flex align-items-center justify-content-between mt-4 mb-4 position-relative">
    <!-- Nút quay lại bên trái -->
    <div class="position-absolute start-0">
        <a href="ajax/clear_subpage.php" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <!-- Tiêu đề căn giữa -->
    <h4 class="mx-auto mb-0 text-center text-primary fw-semibold text-uppercase fs-4 border-bottom border-2 pb-2" style="max-width: fit-content;">
        <i class="fas fa-palette me-2"></i> Quản lý màu sắc
    </h4>
</div>

<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <?php if (hasPermission('Quản lý thuộc tính', 'write')): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemMauSac">
            <i class="fa-solid fa-plus"></i> Thêm
        </button>
    <?php endif; ?>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm màu" id="searchColor">
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
                <th>Tên màu</th>
                <th>Hexcode (mã màu)</th>
                <?php if (hasPermission('Quản lý thuộc tính', 'write') || hasPermission('Quản lý thuộc tính', 'delete')): ?>
                    <th>Chức năng</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="color-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Màu Sắc -->
<div class="modal fade" id="modalThemMauSac" tabindex="-1" aria-labelledby="modalThemMauSacLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemMauSac" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemMauSacLabel">Thêm màu sắc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tenMau" class="form-label">Tên màu</label>
                    <input type="text" class="form-control" id="tenMau" name="color_name" required>
                </div>
                <div class="mb-3">
                    <label for="hexMau" class="form-label">Hexcode (mã màu)</label>
                    <input type="text" class="form-control" id="hexMau" name="hex_code" placeholder="#FFFFFF" required pattern="^#([A-Fa-f0-9]{6})$" title="Ví dụ hợp lệ: #FFFFFF">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sửa Màu Sắc -->
<div class="modal fade" id="modalSuaMauSac" tabindex="-1" aria-labelledby="modalSuaMauSacLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaMauSac" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuaMauSacLabel">Sửa màu sắc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" id="idMauSua" name="color_id" readonly>
                </div>

                <div class="mb-3">
                    <label for="tenMauSua" class="form-label">Tên màu</label>
                    <input type="text" class="form-control" id="tenMauSua" name="color_name" required>
                </div>

                <div class="mb-3">
                    <label for="hexMauSua" class="form-label">Hexcode (mã màu)</label>
                    <input type="text" class="form-control" id="hexMauSua" name="hex_code" placeholder="#FFFFFF" required pattern="^#([A-Fa-f0-9]{6})$" title="Ví dụ hợp lệ: #FFFFFF">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Xóa Màu Sắc -->
<div class="modal fade" id="modalXoaMauSac" tabindex="-1" aria-labelledby="modalXoaMauSacLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaMauSac" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalXoaMauSacLabel">Xóa màu sắc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="maMauXoa" class="form-label">ID</label>
                    <input type="text" class="form-control" id="maMauXoa" name="color_id" readonly>
                </div>
                <div class="mb-3">
                    <label for="tenMauXoa" class="form-label">Tên màu</label>
                    <input type="text" class="form-control" id="tenMauXoa" name="color_name" readonly>
                </div>
                <div class="mb-3">
                    <label for="hexMauXoa" class="form-label">Hexcode (mã màu)</label>
                    <input type="text" class="form-control" id="hexMauXoa" name="hex_code" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<script>
    let currentFilterParams = '';

    function loadColors(page = 1, params = "") {
        const colorWrap = document.querySelector('.color-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_colors.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                colorWrap.innerHTML = data.colorHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadColors(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadColors(currentPage);
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
                        loadColors(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Xử lý tìm kiểm tên 
    document.querySelector('#searchColor').addEventListener('input', function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadColors(1, currentFilterParams);
    });

    // Thêmm màu
    document.getElementById("formThemMauSac").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const name = form.color_name.value.trim();
        const hexCode = form.hex_code.value.trim();

        // Kiểm tra rỗng
        if (!name || !hexCode) {
            alert("Vui lòng nhập đầy đủ thông tin.");
            return;
        }

        const formData = new FormData(form);

        fetch('ajax/add_color.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemMauSac'));
                    modal.hide();

                    // Reset form
                    form.reset();

                    alert(data.message);

                    // Reload danh sách nhà cung cấp
                    loadColors(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi thêm màu.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Sửa màu
    document.querySelector('.color-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-color');
        if (!btn) return;

        // Lấy dữ liệu từ thuộc tính data-*
        const colorId = btn.dataset.id;
        const colorName = btn.dataset.name;
        const hexCode = btn.dataset.hexCode;

        // Gán vào modal sửa
        document.getElementById('idMauSua').value = colorId || '';
        document.getElementById('tenMauSua').value = colorName || '';
        document.getElementById('hexMauSua').value = hexCode || '';
    });

    // Xử lý submit form sửa
    document.getElementById('formSuaMauSac').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('ajax/update_color.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaMauSac'));
                    modal.hide();

                    alert(data.message);

                    // Reload lại danh sách
                    loadColors(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi cập nhật.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi kết nối máy chủ.");
            });
    });


    // Xóa màu
    document.querySelector('.color-wrap').addEventListener('click', (e) => {
        if (e.target.closest('.btn-delete-color')) {
            const button = e.target.closest('.btn-delete-color');

            const id = button.dataset.id;
            const name = button.dataset.name;
            const hex_code = button.dataset.hexCode;

            document.getElementById('maMauXoa').value = id || '';
            document.getElementById('tenMauXoa').value = name || '';
            document.getElementById('hexMauXoa').value = hex_code || '';

        }
    });

    document.getElementById('formXoaMauSac').addEventListener('submit', function(e) {
        e.preventDefault();

        const color_id = document.querySelector('#maMauXoa').value;
        const formData = new FormData();
        formData.append('color_id', color_id);

        fetch('ajax/delete_color.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaMauSac'));
                    modal.hide();

                    alert(data.message);

                    loadColors(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            })
    });
</script>