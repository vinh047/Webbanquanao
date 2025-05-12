<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$statuses = $db->getEnumValues('orders', 'status');

$payment_methods = $db->select('SELECT * FROM payment_method WHERE is_deleted = 0', []);

$current_staff = $db->selectOne('SELECT * FROM users WHERE status = 1 AND user_id = ?', [$_SESSION['user_id']]);
?>
<style>
    .modal.fade .modal-dialog {
        transition: transform 0.2s ease-out, opacity 0.2s ease-out;
    }
</style>
<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemDonHang">
        <i class="fa-solid fa-plus"></i> Thêm
    </button>

    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm mã đơn hàng" id="searchOrder">
            <button class="btn btn-secondary" style="pointer-events: none;">
                <i class="fa-solid fa-search"></i>
            </button>
            <button class="btn btn-outline-secondary ms-2 rounded" id="btnToggleFilter">
                <i class="fa fa-filter"></i>
            </button>

            <button type="button" class="btn btn-outline-secondary ms-2" id="btnClearFilter">
                <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
            </button>
        </div>

    </div>



</div>

<!-- Tìm kiếm nâng cao -->
<form method="GET" action="" class="form-search d-none container my-3">
    <div class="row g-3 justify-content-center">

        <div class="col-md-2">
            <div class="form-floating">
                <input type="number" class="form-control" id="price_min" name="price_min" placeholder="Tổng giá từ">
                <label for="price_min">Tổng giá từ</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="number" class="form-control" id="price_max" name="price_max" placeholder="Tổng giá đến">
                <label for="price_max">Tổng giá đến</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="status" name="status">
                    <option value="" selected>Tất cả</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $status ?>"><?= $status ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="status">Trạng thái</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="from_date" name="from_date"
                    value="<?= $_GET['from_date'] ?? '' ?>" placeholder="Từ ngày">
                <label for="from_date">Từ ngày</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="to_date" name="to_date"
                    value="<?= $_GET['to_date'] ?? '' ?>" placeholder="Đến ngày">
                <label for="to_date">Đến ngày</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="payment_method" name="payment_method">
                    <option value="" selected>Tất cả</option>
                    <?php foreach ($payment_methods as $pm): ?>
                        <option value="<?= $pm['payment_method_id'] ?>"><?= $pm['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="payment_method">Phương thức</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="text" class="form-control" id="user" name="user" placeholder="ID khách hàng">
                <label for="user">Khách hàng</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="text" class="form-control" id="staff" name="staff" placeholder="ID nhân viên">
                <label for="staff">Nhân viên</label>
            </div>
        </div>

    </div>
</form>



<div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Khách hàng</th>
                <th>Tình trạng</th>
                <th>Tổng tiền</th>
                <th>Địa chỉ giao hàng</th>
                <th>Ghi chú</th>
                <th>Ngày tạo</th>
                <th>Phương thức thanh toán</th>
                <th>Nhân viên tạo đơn</th>
                <th>Chức năng</th>

            </tr>
        </thead>
        <tbody class="order-wrap">
        </tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Đơn Hàng -->
<div class="modal fade" id="modalThemDonHang" tabindex="-1" aria-labelledby="modalThemDonHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tạo Đơn Hàng Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formThemDonHang">
                    <div class="row g-3">
                        <!-- Khách hàng -->
                        <div class="col-md-6">
                            <div class="row g-0">
                                <!-- Ô input có floating label -->
                                <div class="col-10">
                                    <div class="form-floating">
                                        <input type="hidden" name="user_id">
                                        <input type="text" id="user_id" class="form-control" placeholder="" readonly>
                                        <label for="user_id">Khách hàng</label>
                                    </div>
                                </div>

                                <!-- Nút chọn khách hàng -->
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-secondary w-100 h-100" id="btnChonKhachHang">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>


                        <!-- Nhân viên -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="staff_id" name="staff_id" class="form-control" placeholder="Nhân viên" value="<?= $current_staff['name'] ?>" data-staff-id="<?= $current_staff['user_id'] ?>" readonly>
                                <label for="staff_id">Nhân viên tạo đơn</label>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="status" name="status" class="form-select">
                                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                                    <option value="Đã thanh toán, chờ giao hàng">Đã thanh toán, chờ giao hàng</option>
                                    <option value="Đang giao hàng">Đang giao hàng</option>
                                    <option value="Giao thành công">Giao thành công</option>
                                    <option value="Đã huỷ">Đã huỷ</option>
                                </select>
                                <label for="status">Tình trạng</label>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="payment_method_id" name="payment_method_id" class="form-select">
                                    <?php foreach ($payment_methods as $pm): ?>
                                        <option value="<?= $pm['payment_method_id'] ?>"><?= $pm['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="payment_method_id">Phương thức thanh toán</label>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea id="note" name="note" class="form-control" placeholder="Ghi chú" style="height: 100px"></textarea>
                                <label for="note">Ghi chú</label>
                            </div>
                        </div>

                        <!-- Loại địa chỉ -->
                        <div class="col-md-6">
                            <label class="form-label d-block">Loại địa chỉ</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="address_option" id="addr_saved" value="saved" checked>
                                    <label class="form-check-label" for="addr_saved">Địa chỉ đã lưu</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="address_option" id="addr_new" value="new">
                                    <label class="form-check-label" for="addr_new">Nhập địa chỉ mới</label>
                                </div>
                            </div>

                            <!-- Địa chỉ đã lưu -->
                            <div id="saved-container" class="form-floating mt-3">
                                <select id="saved-address" class="form-select">
                                    <option selected disabled>Chọn địa chỉ</option>
                                    <?php foreach ($user_addresses as $addr): ?>
                                        <?php
                                        $full = $addr['address_detail'] . ', ' . $addr['ward'] . ', ' . $addr['district'] . ', ' . $addr['province'];
                                        ?>
                                        <option value="<?= $addr['address_id'] ?>" <?= $addr['is_default'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($full) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="saved-address">Địa chỉ đã lưu</label>
                            </div>

                            <!-- Nhập địa chỉ mới -->
                            <div id="new-container" class="mt-3" style="display: none;">
                                <label class="form-label fw-semibold mb-2">Địa chỉ mới</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4 form-floating">
                                        <select id="province" class="form-select">
                                            <option selected disabled>Tỉnh/TP</option>
                                        </select>
                                        <label for="province">Tỉnh/TP</label>
                                    </div>
                                    <div class="col-md-4 form-floating">
                                        <select id="district" class="form-select">
                                            <option selected disabled>Quận/Huyện</option>
                                        </select>
                                        <label for="district">Quận/Huyện</label>
                                    </div>
                                    <div class="col-md-4 form-floating">
                                        <select id="ward" class="form-select">
                                            <option selected disabled>Phường/Xã</option>
                                        </select>
                                        <label for="ward">Phường/Xã</label>
                                    </div>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="specific-address" placeholder="Số nhà, đường...">
                                    <label for="specific-address">Địa chỉ cụ thể</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <hr>

                <!-- Chi tiết đơn hàng -->
                <div class="row g-3 align-items-end mt-3">
                    <!-- Sản phẩm -->
                    <div class="col-md-3">
                        <div class="form-floating position-relative">
                            <input type="text" id="product_id" class="form-control pe-5" placeholder="Sản phẩm" readonly>
                            <label for="product_id">Sản phẩm</label>
                            <button type="button" class="btn btn-outline-secondary position-absolute end-0 top-0 mt-2 me-2" id="btnChonSanPham">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Biến thể -->
                    <div class="col-md-3">
                        <div class="form-floating position-relative">
                            <input type="text" id="variant_id" class="form-control pe-5" placeholder="Biến thể" readonly>
                            <label for="variant_id">Biến thể (variant)</label>
                            <button type="button" class="btn btn-outline-secondary position-absolute end-0 top-0 mt-2 me-2" id="btnChonBienThe">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Số lượng -->
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" id="quantity" class="form-control" placeholder="Số lượng" min="1">
                            <label for="quantity">Số lượng</label>
                        </div>
                    </div>

                    <!-- Đơn giá -->
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" id="price" class="form-control" placeholder="Đơn giá" readonly>
                            <label for="price">Đơn giá</label>
                        </div>
                    </div>

                    <!-- Nút thêm -->
                    <div class="col-md-2">
                        <button class="btn btn-success w-100" id="btnThemChiTiet">
                            <i class="fa fa-plus"></i> Thêm sản phẩm
                        </button>
                    </div>
                </div>


                <!-- Danh sách sản phẩm đã thêm -->
                <div class="mt-4">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                                <th>Xoá</th>
                            </tr>
                        </thead>
                        <tbody id="orderDetailQueue">
                            <!-- Sẽ được JS thêm vào -->
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="btnLuuDonHang" class="btn btn-primary">
                    <i class="fa fa-save"></i> Lưu vào đơn hàng tạm thời
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chọn Khách Hàng -->
<div class="modal fade" id="modalChonUser" tabindex="-1" aria-labelledby="modalChonUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalChonUserLabel">Chọn khách hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Tìm kiếm -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tên</label>
                        <input type="text" class="form-control" id="searchName" placeholder="Nhập tên khách hàng">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" id="searchEmail" placeholder="Nhập email">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="searchPhone" placeholder="Nhập số điện thoại">
                    </div>
                </div>

                <!-- Bảng danh sách người dùng -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Dữ liệu user sẽ được load bằng PHP hoặc AJAX -->
                        </tbody>
                    </table>
                    <div class="user-pagination-wrap"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chọn Sản Phẩm -->
<div class="modal fade" id="modalChonSanPham" tabindex="-1" aria-labelledby="modalChonSanPhamLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalChonSanPhamLabel">Chọn sản phẩm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Tìm kiếm -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" class="form-control" id="searchProductName" placeholder="Nhập tên sản phẩm">
                    </div>
                </div>

                <!-- Bảng danh sách sản phẩm -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá bán</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <!-- Dữ liệu sản phẩm sẽ được load bằng AJAX -->
                        </tbody>
                    </table>
                    <div class="product-pagination-wrap"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>



<script>
    let currentFilterParams = '';

    function loadOrders(page = 1, params = "") {
        const orderWrap = document.querySelector('.order-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_orders.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                orderWrap.innerHTML = data.orderHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }
    loadOrders(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadOrders(currentPage);
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
                        loadOrders(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // ẩn hiện form tìm kiếm nâng cao
    document.getElementById('btnToggleFilter').addEventListener('click', () => {
        const form = document.querySelector('.form-search');
        form.classList.toggle('d-none');
    });

    // kiểm tra ngày từ ngày đến
    // Khi nhập xong from_date
    document.getElementById('from_date').addEventListener('change', function() {
        const from = new Date(this.value);
        const to = new Date(document.getElementById('to_date').value);
        if (document.getElementById('to_date').value && from > to) {
            alert("Ngày bắt đầu không được sau ngày kết thúc.");
            this.value = "";
        }
    });

    // Khi nhập xong to_date
    document.getElementById('to_date').addEventListener('change', function() {
        const to = new Date(this.value);
        const from = new Date(document.getElementById('from_date').value);
        if (document.getElementById('from_date').value && to < from) {
            alert("Ngày kết thúc không được trước ngày bắt đầu.");
            this.value = "";
        }
    });

    function handleFilterChange() {
        // id đơn hàng lưu name do lười sửa bên load
        const search_name = document.getElementById('searchOrder').value.trim();
        const priceMin = document.getElementById('price_min').value.trim();
        const priceMax = document.getElementById('price_max').value.trim();
        const status = document.getElementById('status').value.trim();
        const fromDate = document.getElementById('from_date').value.trim();
        const toDate = document.getElementById('to_date').value.trim();
        const paymentMethod = document.getElementById('payment_method').value.trim();
        const user = document.getElementById('user').value.trim();
        const staff = document.getElementById('staff').value.trim();

        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');

        currentFilterParams = "";

        if (search_name) currentFilterParams += `&search_name=${encodeURIComponent(search_name)}`
        if (priceMin) currentFilterParams += `&price_min=${encodeURIComponent(priceMin)}`;
        if (priceMax) currentFilterParams += `&price_max=${encodeURIComponent(priceMax)}`;
        if (status) currentFilterParams += `&status=${encodeURIComponent(status)}`;
        if (fromDate) currentFilterParams += `&from_date=${encodeURIComponent(fromDate)}`;
        if (toDate) currentFilterParams += `&to_date=${encodeURIComponent(toDate)}`;
        if (paymentMethod) currentFilterParams += `&payment_method=${encodeURIComponent(paymentMethod)}`;
        if (user) currentFilterParams += `&user=${encodeURIComponent(user)}`;
        if (staff) currentFilterParams += `&staff=${encodeURIComponent(staff)}`;

        loadOrders(1, currentFilterParams);
    }

    // Xử lý tìm kiểm mã đơn
    document.querySelector('#searchOrder').addEventListener('input', function() {
        handleFilterChange();
    });

    // Gọi handleFilterChange khi người dùng nhập dữ liệu (input/keypress)
    document.querySelectorAll('.form-search input').forEach(input => {
        input.addEventListener('input', handleFilterChange);
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // ngăn submit
                handleFilterChange();
            }
        });
    });

    document.querySelectorAll('.form-search select').forEach(select => {
        select.addEventListener('change', handleFilterChange);
    });

    // Sự kiện nút xóa lọc:
    document.getElementById('btnClearFilter').addEventListener('click', () => {
        // Reset tất cả input trong form
        document.querySelector('.form-search').reset();

        // Xóa thủ công các select (vì reset không luôn hiệu quả với select)
        ['status', 'payment_method'].forEach(id => {
            const select = document.getElementById(id);
            if (select) select.value = '';
        });

        // Reset cả ô tìm kiếm đơn hàng chính
        document.getElementById('searchOrder').value = '';

        // Gọi lại load dữ liệu
        handleFilterChange();
    });

    // load vào modal chọn khách hàng/ nhân viên
    let currentFilterParamsUser = '';

    function loadUsers(page = 1, params = "") {
        const userWrap = document.querySelector('#userTableBody');
        const paginationWrap = document.querySelector('.user-pagination-wrap');
        fetch('ajax/load_users_for_order.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                userWrap.innerHTML = data.userHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            })
    }

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadUsers(currentPage);
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
                        loadUsers(page, currentFilterParams);
                    }
                }
            });
        }
    }

    function handleFilterUserChange(page = 1) {
        const name = document.getElementById('searchName').value.trim();
        const email = document.getElementById('searchEmail').value.trim();
        const phone = document.getElementById('searchPhone').value.trim();

        currentFilterParamsUser = '';

        if (name) currentFilterParamsUser += `&name=${encodeURIComponent(name)}`;
        if (email) currentFilterParamsUser += `&email=${encodeURIComponent(email)}`;
        if (phone) currentFilterParamsUser += `&phone=${encodeURIComponent(phone)}`;

        loadUsers(1, currentFilterParamsUser);

    }

    document.querySelectorAll('#searchName, #searchEmail, #searchPhone').forEach(input => {
        input.addEventListener('input', () => handleFilterUserChange(1));
    });


    // Đóng mở modal chọn khách hàng
    let previousModalId = null; // Biến lưu ID modal trước đó

    // Khi bấm nút mở modal chọn user từ một modal nào đó
    function openModalChonUser(fromModalId) {
        previousModalId = fromModalId;

        // Ẩn modal hiện tại
        const current = bootstrap.Modal.getInstance(document.getElementById(fromModalId));
        if (current) current.hide();

        loadUsers(1);

        // Hiện modal chọn user
        const modalUser = new bootstrap.Modal(document.getElementById('modalChonUser'));
        modalUser.show();
    }

    // Khi modal chọn user bị đóng → mở lại modal trước đó
    document.getElementById('modalChonUser').addEventListener('hidden.bs.modal', function() {
        if (previousModalId) {
            const backModal = new bootstrap.Modal(document.getElementById(previousModalId));
            backModal.show();
            previousModalId = null; // reset lại
        }
    });

    // Gọi từ nút chọn khách hàng trong modal Thêm đơn hàng
    document.getElementById('btnChonKhachHang').addEventListener('click', function() {
        openModalChonUser('modalThemDonHang');
    });

    // Gọi từ modal Sửa đơn hàng
    const btnChonKH_Sua = document.getElementById('btnChonKhachHang_Sua');
    if (btnChonKH_Sua) {
        btnChonKH_Sua.addEventListener('click', function() {
            openModalChonUser('modalSuaDonHang');
        });
    }

    const savedRadio = document.getElementById('addr_saved');
    const newRadio = document.getElementById('addr_new');
    const savedContainer = document.getElementById('saved-container');
    const newContainer = document.getElementById('new-container');

    function toggleAddress() {
        if (savedRadio.checked) {
            savedContainer.style.display = 'block';
            newContainer.style.display = 'none';
        } else {
            savedContainer.style.display = 'none';
            newContainer.style.display = 'block';
        }
    }
    savedRadio.addEventListener('change', toggleAddress);
    newRadio.addEventListener('change', toggleAddress);
    toggleAddress();

    // --- Province/District/Ward for new address ---
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    fetch('https://provinces.open-api.vn/api/p/')
        .then(res => res.json())
        .then(data => {
            provinceSelect.innerHTML = '<option selected disabled>Tỉnh/TP</option>';
            data.forEach(p => {
                const name = p.name.replace(/^Tỉnh |^Thành phố /, '');
                provinceSelect.add(new Option(name, p.code));
            });
        });

    provinceSelect.addEventListener('change', () => {
        districtSelect.innerHTML = '<option selected disabled>Quận/Huyện</option>';
        wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
        fetch(`https://provinces.open-api.vn/api/p/${provinceSelect.value}?depth=2`)
            .then(res => res.json())
            .then(obj => {
                obj.districts.forEach(d => districtSelect.add(new Option(d.name, d.code)));
            });
    });

    districtSelect.addEventListener('change', () => {
        wardSelect.innerHTML = '<option selected disabled>Phường/Xã</option>';
        fetch(`https://provinces.open-api.vn/api/d/${districtSelect.value}?depth=2`)
            .then(res => res.json())
            .then(obj => {
                obj.wards.forEach(w => wardSelect.add(new Option(w.name, w.code)));
            });
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-choose-user');
        if (!btn) return;

        const userId = btn.getAttribute('data-user-id');
        const userName = btn.getAttribute('data-name');

        // Gán giá trị vào ô input user_id (hiển thị tên thay vì ID)
        const inputUser = document.getElementById('user_id');
        if (inputUser) {
            inputUser.value = userName;
            inputUser.setAttribute('data-user-id', userId); // nếu cần lưu ID ngầm
        }

        document.querySelector('input[name="user_id"]').value = userId;


        // Đóng modal chọn user
        const modalUser = bootstrap.Modal.getInstance(document.getElementById('modalChonUser'));
        if (modalUser) {
            modalUser.hide();
        }
    });

    document.getElementById('btnChonSanPham').addEventListener('click', function() {
        openModalChonSanPham('modalThemDonHang');
    });

    let previousModalSanPhamId = null; // Biến riêng cho modal sản phẩm

    function openModalChonSanPham(fromModalId) {
        previousModalSanPhamId = fromModalId;

        // Ẩn modal hiện tại
        const current = bootstrap.Modal.getInstance(document.getElementById(fromModalId));
        if (current) current.hide();

        // loadProducts(1); // Hàm này bạn cần định nghĩa riêng nếu chưa có

        const modalProduct = new bootstrap.Modal(document.getElementById('modalChonSanPham'));
        modalProduct.show();
    }

    // Khi modal chọn sản phẩm đóng lại → mở lại modal trước đó
    document.getElementById('modalChonSanPham').addEventListener('hidden.bs.modal', function() {
        if (previousModalSanPhamId) {
            const backModal = new bootstrap.Modal(document.getElementById(previousModalSanPhamId));
            backModal.show();
            previousModalSanPhamId = null;
        }
    });

    let currentFilterParamsProduct = '';

    function loadProducts(page = 1, params = "") {
        const productWrap = document.querySelector('#productTableBody');
        const paginationWrap = document.querySelector('.product-pagination-wrap');
        fetch('ajax/load_products_for_order.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                productWrap.innerHTML = data.productHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phanTrangProduct(); // gọi hàm phân trang riêng cho product
            });
    }
    loadProducts(1);

    function phanTrangProduct() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const currentPage = parseInt(this.dataset.page);
                loadProducts(currentPage, currentFilterParamsProduct);
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
                        loadProducts(page, currentFilterParamsProduct);
                    }
                }
            });
        }
    }
</script>