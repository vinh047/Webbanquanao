<div class="d-flex align-items-center justify-content-between mt-4 mb-4 position-relative">
    <!-- Nút quay lại bên trái -->
    <div class="position-absolute start-0">
        <a href="ajax/clear_subpage.php" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <!-- Tiêu đề căn giữa -->
    <h4 class="mx-auto mb-0 text-center text-primary fw-semibold text-uppercase fs-4 border-bottom border-2 pb-2" style="max-width: fit-content;">
        <i class="fas fa-ruler-combined me-2"></i> Quản lý size
    </h4>
</div>

<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemSize">
        <i class="fa-solid fa-plus"></i> Thêm
    </button>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm size" id="searchSize">
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
                <th style="width: 25%;">ID</th>
                <th style="width: 35%;">Size</th>
                <th style="width: 40%;">Chức năng</th>
            </tr>
        </thead>
        <tbody class="size-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Size -->
<div class="modal fade" id="modalThemSize" tabindex="-1" aria-labelledby="modalThemSizeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemSize" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemSizeLabel">Thêm size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tenSize" class="form-label">Tên size</label>
                    <input type="text" class="form-control" id="tenSize" name="size_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Sửa Size -->
<div class="modal fade" id="modalSuaSize" tabindex="-1" aria-labelledby="modalSuaSizeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaSize" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuaSizeLabel">Sửa size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="idSizeSua" class="form-label">ID</label>
                    <input type="text" class="form-control" id="idSizeSua" name="size_id" readonly>
                </div>

                <div class="mb-3">
                    <label for="tenSizeSua" class="form-label">Tên size</label>
                    <input type="text" class="form-control" id="tenSizeSua" name="size_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Xóa Size -->
<div class="modal fade" id="modalXoaSize" tabindex="-1" aria-labelledby="modalXoaSizeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaSize" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalXoaSizeLabel">Xóa size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="maSizeXoa" class="form-label">ID</label>
                    <input type="text" class="form-control" id="maSizeXoa" name="size_id" readonly>
                </div>
                <div class="mb-3">
                    <label for="tenSizeXoa" class="form-label">Tên size</label>
                    <input type="text" class="form-control" id="tenSizeXoa" name="size_name" readonly>
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

    function loadSizes(page = 1, params = "") {
        const sizeWrap = document.querySelector('.size-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_sizes.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                sizeWrap.innerHTML = data.sizeHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadSizes(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadSizes(currentPage);
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
                        loadSizes(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Xử lý tìm kiểm tên 
    document.querySelector('#searchSize').addEventListener('input', function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadSizes(1, currentFilterParams);
    });

    // Thêm size
    document.getElementById("formThemSize").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const name = form.size_name.value.trim();

        if (!name) {
            alert("Vui lòng nhập tên size.");
            return;
        }

        const formData = new FormData(form);

        fetch('ajax/add_size.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemSize'));
                    modal.hide();
                    form.reset();
                    alert(data.message);
                    loadSizes(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi thêm size.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Mở modal sửa size
    document.querySelector('.size-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-size');
        if (!btn) return;

        const sizeId = btn.dataset.id;
        const name = btn.dataset.name;

        document.getElementById('idSizeSua').value = sizeId || '';
        document.getElementById('tenSizeSua').value = name || '';
    });

    // Submit sửa size
    document.getElementById('formSuaSize').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('ajax/update_size.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaSize'));
                    modal.hide();
                    alert(data.message);
                    loadSizes(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi cập nhật.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Mở modal xóa size
    document.querySelector('.size-wrap').addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-delete-size');
        if (!btn) return;

        const id = btn.dataset.id;
        const name = btn.dataset.name;

        document.getElementById('maSizeXoa').value = id || '';
        document.getElementById('tenSizeXoa').value = name || '';
    });

    // Submit xoá size
    document.getElementById('formXoaSize').addEventListener('submit', function(e) {
        e.preventDefault();

        const size_id = document.getElementById('maSizeXoa').value;
        const formData = new FormData();
        formData.append('size_id', size_id);

        fetch('ajax/delete_size.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaSize'));
                    modal.hide();
                    alert(data.message);
                    loadSizes(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            });
    });
</script>