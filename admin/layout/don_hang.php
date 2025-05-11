<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$statuses = $db->getEnumValues('orders', 'status');

$payment_methods = $db->select('SELECT * FROM payment_method WHERE is_deleted = 0', []);
?>

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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemDonHangLabel">Thêm đơn hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formThemDonHang">
                    <div class="row g-3">

                        <!-- Chọn Khách hàng -->
                        <div class="col-md-6">
                            <label class="form-label">Khách hàng</label>
                            <div class="input-group">
                                <input type="text" id="user_id" name="user_id" class="form-control" placeholder="ID khách hàng" readonly>
                                <button type="button" class="btn btn-outline-secondary" id="btnChonKhachHang">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Chọn Nhân viên -->
                        <div class="col-md-6">
                            <label class="form-label">Nhân viên tạo đơn</label>
                            <div class="input-group">
                                <input type="text" id="staff_id" name="staff_id" class="form-control" placeholder="ID nhân viên" readonly>
                                <button type="button" class="btn btn-outline-secondary" id="btnChonNhanVien">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Trạng thái đơn hàng -->
                        <div class="col-md-6">
                            <label class="form-label">Tình trạng</label>
                            <select id="status" name="status" class="form-select">
                                <option value="Chờ xác nhận">Chờ xác nhận</option>
                                <option value="Đã thanh toán, chờ giao hàng">Đã thanh toán, chờ giao hàng</option>
                                <option value="Đang giao hàng">Đang giao hàng</option>
                                <option value="Giao thành công">Giao thành công</option>
                                <option value="Đã huỷ">Đã huỷ</option>
                            </select>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-6">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select id="payment_method_id" name="payment_method_id" class="form-select">
                                <?php foreach ($payment_methods as $pm): ?>
                                    <option value="<?= $pm['payment_method_id'] ?>"><?= $pm['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-12">
                            <label class="form-label">Ghi chú</label>
                            <textarea id="note" name="note" class="form-control" rows="3" placeholder="Ghi chú nếu có"></textarea>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnThemChiTiet" class="btn btn-success">
                    <i class="fa fa-plus"></i> Lưu đơn hàng vào
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
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
</script>