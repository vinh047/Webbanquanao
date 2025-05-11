<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$statuses = $db->getEnumValues('orders', 'status');

$payment_methods = $db->select('SELECT * FROM payment_method WHERE is_deleted = 0', []);
?>

<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemNCC">
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
                <input type="number" class="form-control" id="user" name="user" placeholder="ID khách hàng">
                <label for="user">Khách hàng</label>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-floating">
                <input type="number" class="form-control" id="staff" name="staff" placeholder="ID nhân viên">
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


<script>
    let currentFilterParams = '';

    function getFilterParams() {
        const form = document.querySelector('.form-search');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value.trim() !== "") {
                params.append(key, value.trim());
            }
        }

        currentFilterParams = '&' + params.toString(); // cập nhật để phân trang dùng
        return currentFilterParams;
    }

    function loadOrders(page = 1, params = "") {
        const orderWrap = document.querySelector('.order-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');

        fetch('ajax/load_orders.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                orderWrap.innerHTML = data.orderHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            });
    }

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                currentPage = parseInt(this.dataset.page);
                loadOrders(currentPage, currentFilterParams);
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
                        loadOrders(page, currentFilterParams);
                    }
                }
            });
        }
    }

    // Trigger tìm kiếm nâng cao khi có thay đổi
    document.querySelectorAll('.form-search input, .form-search select').forEach(input => {
        input.addEventListener('change', () => {
            const params = getFilterParams();
            loadOrders(1, params);
        });
    });

    // Load lần đầu
    window.addEventListener('DOMContentLoaded', () => {
        const params = getFilterParams();
        loadOrders(1, params);
    });

    // ẩn hiện form tìm kiếm nâng cao
    document.getElementById('btnToggleFilter').addEventListener('click', () => {
        const form = document.querySelector('.form-search');
        form.classList.toggle('d-none');
    });
</script>