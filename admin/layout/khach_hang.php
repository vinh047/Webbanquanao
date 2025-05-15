<style>
    .nav-tabs .nav-link {
        color: black;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        /* màu xanh Bootstrap */
    }
</style>

<!-- Thanh tìm kiếm -->
<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm khách hàng" id="searchCustomer">
            <button class="btn btn-secondary" style="pointer-events: none;">
                <i class="fa-solid fa-search"></i>
            </button>
        </div>
    </div>
</div>

<!-- Bảng khách hàng -->
<div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên khách hàng</th>
                <th>Email</th>
                <th>Mật khẩu</th>
                <th>Số điện thoại</th>
                <th>Trạng thái</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="customer-wrap"></tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal XÓA khách hàng -->
<div class="modal fade" id="modalXoaKH" tabindex="-1" aria-labelledby="modalXoaKHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaKH" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xóa khách hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idKhachXoa" name="user_id">
                <div class="mb-3"><label class="form-label">Tên khách hàng</label>
                    <input type="text" class="form-control" id="tenKhachXoa" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailKhachXoa" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Mật khẩu</label>
                    <input type="text" class="form-control" id="matkhauKhachXoa" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="sdtKhachXoa" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Trạng thái</label>
                    <input type="text" class="form-control" id="trangthaiKhachXoa" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal SỬA khách hàng -->
<div class="modal fade" id="modalSuaKH" tabindex="-1" aria-labelledby="modalSuaKHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaKH" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Sửa thông tin khách hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idKhachSua" name="user_id">
                <div class="mb-3"><label class="form-label">Tên khách hàng</label>
                    <input type="text" class="form-control" id="tenKhachSua" name="name" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailKhachSua" name="email" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Mật khẩu</label>
                    <input type="text" class="form-control" id="matkhauKhachSua" name="password" readonly>
                </div>
                <div class="mb-3"><label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="sdtKhachSua" name="phone" readonly>
                </div>
                <!-- Trạng thái vẫn cho sửa -->
                <div class="mb-3"><label class="form-label">Trạng thái</label>
                    <select class="form-control" id="trangthaiKhachSua" name="status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Khóa</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal CHI TIẾT khách hàng -->
<div class="modal fade" id="modalChiTietKH" tabindex="-1" aria-labelledby="modalChiTietKHLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Chi tiết khách hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Thông tin cơ bản -->
                <h6>Thông tin cơ bản</h6>
                <p><strong>Tên:</strong> <span id="ct-name"></span></p>
                <p><strong>Email:</strong> <span id="ct-email"></span></p>
                <p><strong>SĐT:</strong> <span id="ct-phone"></span></p>
                <p><strong>Trạng thái:</strong> <span id="ct-status"></span></p>
                <p><strong>Mật khẩu:</strong> <span id="ct-password"></span></p>

                <hr>

                <!-- Tabs -->
                <ul class="nav nav-tabs" id="khachHangTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">Đơn hàng</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Lịch sử mua hàng</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab">Danh sách địa chỉ</button>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="orders" role="tabpanel">
                        <div id="ct-orders">Đang tải đơn hàng...</div>
                    </div>
                    <div class="tab-pane fade" id="history" role="tabpanel">
                        <div id="ct-history">Đang tải lịch sử mua hàng...</div>
                    </div>
                    <div class="tab-pane fade" id="addresses" role="tabpanel">
                        <div id="ct-addresses">Đang tải địa chỉ...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    let currentFilterParams = "";

    function loadCustomers(page = 1, params = "") {
        const wrap = document.querySelector('.customer-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_customers.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                wrap.innerHTML = data.customerHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            });
    }

    loadCustomers(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                loadCustomers(page, currentFilterParams);
            });
        });
    }

    document.getElementById("searchCustomer").addEventListener("input", function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadCustomers(1, currentFilterParams);
    });

    // Gán dữ liệu khi nhấn XÓA
    document.querySelector('.customer-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-customer');
        if (!btn) return;

        document.getElementById("idKhachXoa").value = btn.dataset.id || "";
        document.getElementById("tenKhachXoa").value = btn.dataset.name || "";
        document.getElementById("emailKhachXoa").value = btn.dataset.email || "";
        document.getElementById("matkhauKhachXoa").value = btn.dataset.password || "";
        document.getElementById("sdtKhachXoa").value = btn.dataset.phone || "";
        document.getElementById("trangthaiKhachXoa").value = btn.dataset.status == "1" ? "Hoạt động" : "Khóa";
    });

    document.getElementById("formXoaKH").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("ajax/delete_customer.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById("modalXoaKH")).hide();
                    loadCustomers(1, currentFilterParams);
                }
            });
    });

    // Gán dữ liệu khi nhấn SỬA
    document.querySelector('.customer-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-customer');
        if (!btn) return;

        document.getElementById("idKhachSua").value = btn.dataset.id || "";
        document.getElementById("tenKhachSua").value = btn.dataset.name || "";
        document.getElementById("emailKhachSua").value = btn.dataset.email || "";
        document.getElementById("matkhauKhachSua").value = btn.dataset.password || "";
        document.getElementById("sdtKhachSua").value = btn.dataset.phone || "";
        document.getElementById("trangthaiKhachSua").value = btn.dataset.status || "1";
    });

    document.getElementById("formSuaKH").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("ajax/update_customer.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById("modalSuaKH")).hide();
                    loadCustomers(1, currentFilterParams);
                }
            });
    });
    document.querySelector('.customer-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-detail-customer');
        if (!btn) return;

        const userId = btn.dataset.id;
        document.getElementById('ct-name').textContent = btn.dataset.name || '';
        document.getElementById('ct-email').textContent = btn.dataset.email || '';
        document.getElementById('ct-phone').textContent = btn.dataset.phone || '';
        document.getElementById('ct-status').textContent = btn.dataset.status == "1" ? "Hoạt động" : "Khóa";
        document.getElementById('ct-password').textContent = btn.dataset.password || '';

        // Gọi AJAX để load đơn hàng và địa chỉ
        fetch(`ajax/chi_tiet_khach_hang.php?user_id=${userId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('ct-orders').innerHTML = data.ordersHtml || 'Không có đơn hàng.';
                document.getElementById('ct-history').innerHTML = data.historyHtml || 'Không có lịch sử.';
                document.getElementById('ct-addresses').innerHTML = data.addressesHtml || 'Không có địa chỉ.';
            });

    });

    document.querySelector('.customer-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-toggle-status-customer');
        if (!btn) return;

        const userId = btn.dataset.id;
        const currentStatus = btn.dataset.status;
        const newStatus = currentStatus == "1" ? 0 : 1;

        if (!confirm(`Bạn có chắc muốn ${newStatus == 1 ? 'mở' : 'khóa'} tài khoản này không?`)) {
            return;
        }

        fetch('ajax/toggle_status_customer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    loadCustomers(1, currentFilterParams);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi kết nối máy chủ.');
            });
    });
</script>