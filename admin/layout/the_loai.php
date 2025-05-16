<div class="d-flex align-items-center justify-content-between mt-4 mb-4 position-relative">
    <!-- Nút quay lại bên trái -->
    <div class="position-absolute start-0">
        <a href="ajax/clear_subpage.php" class="btn btn-outline-dark rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
        </a>
    </div>

    <!-- Tiêu đề căn giữa -->
    <h4 class="mx-auto mb-0 text-center text-primary fw-semibold text-uppercase fs-4 border-bottom border-2 pb-2" style="max-width: fit-content;">
        <i class="fas fa-tags me-2"></i> Quản lý thể loại
    </h4>
</div>

<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <?php if (hasPermission('Quản lý thuộc tính', 'write')): ?>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemTheLoai">
            <i class="fa-solid fa-plus"></i> Thêm
        </button>
    <?php endif; ?>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm thể loại" id="searchCategory">
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
                <th>Tên thể loại</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="category-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Thể Loại -->
<div class="modal fade" id="modalThemTheLoai" tabindex="-1" aria-labelledby="modalThemTheLoaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemTheLoai" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemTheLoaiLabel">Thêm thể loại</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="tenTheLoai" class="form-label">Tên thể loại</label>
                    <input type="text" class="form-control" id="tenTheLoai" name="category_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Sửa Thể Loại -->
<div class="modal fade" id="modalSuaTheLoai" tabindex="-1" aria-labelledby="modalSuaTheLoaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaTheLoai" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSuaTheLoaiLabel">Sửa thể loại</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="idTheLoaiSua" class="form-label">ID</label>
                    <input type="text" class="form-control" id="idTheLoaiSua" name="category_id" readonly>
                </div>

                <div class="mb-3">
                    <label for="tenTheLoaiSua" class="form-label">Tên thể loại</label>
                    <input type="text" class="form-control" id="tenTheLoaiSua" name="category_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xóa Thể Loại -->
<div class="modal fade" id="modalXoaTheLoai" tabindex="-1" aria-labelledby="modalXoaTheLoaiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaTheLoai" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalXoaTheLoaiLabel">Xóa thể loại</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="maTheLoaiXoa" class="form-label">ID</label>
                    <input type="text" class="form-control" id="maTheLoaiXoa" name="category_id" readonly>
                </div>
                <div class="mb-3">
                    <label for="tenTheLoaiXoa" class="form-label">Tên thể loại</label>
                    <input type="text" class="form-control" id="tenTheLoaiXoa" name="category_name" readonly>
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

    function loadCategories(page = 1, params = "") {
        const categoryWrap = document.querySelector('.category-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_categories.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                categoryWrap.innerHTML = data.categoryHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadCategories(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadCategories(currentPage);
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
                        loadCategories(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Xử lý tìm kiểm tên 
    document.querySelector('#searchCategory').addEventListener('input', function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadCategories(1, currentFilterParams);
    });


    // Thêmm thể loại
    document.getElementById("formThemTheLoai").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const name = form.category_name.value.trim();

        // Kiểm tra rỗng
        if (!name) {
            alert("Vui lòng nhập đầy đủ thông tin.");
            return;
        }

        const formData = new FormData(form);

        fetch('ajax/add_category.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemTheLoai'));
                    modal.hide();

                    // Reset form
                    form.reset();

                    alert(data.message);

                    // Reload danh sách nhà cung cấp
                    loadCategories(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi thêm màu.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Sửa thể loại
    document.querySelector('.category-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-category');
        if (!btn) return;

        // Lấy dữ liệu từ thuộc tính data-*
        const categoryId = btn.dataset.id;
        const name = btn.dataset.name;

        // Gán vào modal sửa
        document.getElementById('idTheLoaiSua').value = categoryId || '';
        document.getElementById('tenTheLoaiSua').value = name || '';
    });

    // Xử lý submit form sửa
    document.getElementById('formSuaTheLoai').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('ajax/update_category.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaTheLoai'));
                    modal.hide();

                    alert(data.message);

                    // Reload lại danh sách
                    loadCategories(1, currentFilterParams);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi cập nhật.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Xóa thể loại
    document.querySelector('.category-wrap').addEventListener('click', (e) => {
        if (e.target.closest('.btn-delete-category')) {
            const button = e.target.closest('.btn-delete-category');

            const id = button.dataset.id;
            const name = button.dataset.name;

            document.getElementById('maTheLoaiXoa').value = id || '';
            document.getElementById('tenTheLoaiXoa').value = name || '';

        }
    });

    document.getElementById('formXoaTheLoai').addEventListener('submit', function(e) {
        e.preventDefault();

        const category_id = document.querySelector('#maTheLoaiXoa').value;
        const formData = new FormData();
        formData.append('category_id', category_id);

        fetch('ajax/delete_category.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalXoaTheLoai'));
                    modal.hide();

                    alert(data.message);

                    loadCategories(1, currentFilterParams);
                } else {
                    alert(data.message);
                }
            })
    });
</script>