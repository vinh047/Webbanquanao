<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$statuses = $db->getEnumValues('orders', 'status');

$payment_methods = $db->select('SELECT * FROM payment_method WHERE is_deleted = 0', []);

$current_staff = $db->selectOne('SELECT * FROM users WHERE status = 1 AND user_id = ?', [$_SESSION['admin_id']]);
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
                                        <input type="text" id="user_id" class="form-control" placeholder="" name="user_id" readonly>
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
                                <select id="statusThem" name="status" class="form-select">
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
                            <input type="text" id="product_id" class="form-control pe-5" placeholder="Sản phẩm" name="product_id" readonly>
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
                            <input type="text" id="price" class="form-control" placeholder="Đơn giá" readonly>
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
                    <!-- Tổng tiền đơn hàng -->
                    <div class="d-flex justify-content-end mt-3 me-2">
                        <h5 class="fw-bold">
                            Tổng tiền: <span id="tongTienDonHang">0 ₫</span>
                        </h5>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="btnLuuDonHang" class="btn btn-primary">
                    <i class="fa fa-save"></i> Lưu đơn hàng
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

<!-- Modal Chọn Biến Thể -->
<div class="modal fade" id="modalChonVariant" tabindex="-1" aria-labelledby="modalChonVariantLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalChonVariantLabel">Chọn biến thể sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Bảng danh sách biến thể -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Size</th>
                                <th>Màu sắc</th>
                                <th>Tồn kho</th>
                                <th>Chức năng</th>
                            </tr>
                        </thead>
                        <tbody id="variantTableBody">
                            <!-- Dữ liệu biến thể sẽ được load bằng AJAX -->
                        </tbody>
                    </table>
                    <div class="variant-pagination-wrap"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Hủy Đơn Hàng -->
<div class="modal fade" id="modalXoaDonHang" tabindex="-1" aria-labelledby="modalXoaDonHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hủy Đơn Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body">
                <form id="formXoaDonHang">
                    <div class="row g-3">
                        <!-- Khách hàng -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="delete_user_name" class="form-control" placeholder="Khách hàng" readonly>
                                <label for="delete_user_name">Khách hàng</label>
                            </div>
                        </div>

                        <!-- Nhân viên -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="delete_staff_name" class="form-control" placeholder="Nhân viên tạo đơn" readonly>
                                <label for="delete_staff_name">Nhân viên tạo đơn</label>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="delete_status" class="form-control" placeholder="Trạng thái" readonly>
                                <label for="delete_status">Trạng thái</label>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="delete_payment_method" class="form-control" placeholder="Phương thức thanh toán" readonly>
                                <label for="delete_payment_method">Phương thức thanh toán</label>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea id="delete_note" class="form-control" placeholder="Ghi chú" style="height: 100px" readonly></textarea>
                                <label for="delete_note">Ghi chú</label>
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng -->
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea id="delete_shipping_address" class="form-control" placeholder="Địa chỉ giao hàng" style="height: 80px" readonly></textarea>
                                <label for="delete_shipping_address">Địa chỉ giao hàng</label>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Chi tiết đơn hàng -->
                <div class="mt-3">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="delete_orderDetailQueue">
                            <!-- Dữ liệu chi tiết đơn hàng sẽ được load bằng JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Tổng tiền đơn hàng -->
                <div class="d-flex justify-content-end mt-3 me-2">
                    <h5 class="fw-bold">
                        Tổng tiền: <span id="delete_tongTienDonHang">0 ₫</span>
                    </h5>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="btnConfirmDeleteOrder" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Xác nhận hủy đơn hàng
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Xem Chi Tiết Đơn Hàng -->
<div class="modal fade" id="modalXemChiTietDonHang" tabindex="-1" aria-labelledby="modalXemChiTietDonHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Chi Tiết Đơn Hàng</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body">
                <form id="formXemChiTietDonHang">
                    <div class="row g-3">
                        <!-- Khách hàng -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="ct_user_name" class="form-control" placeholder="Khách hàng" readonly>
                                <label for="ct_user_name">Khách hàng</label>
                            </div>
                        </div>

                        <!-- Nhân viên -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="ct_staff_name" class="form-control" placeholder="Nhân viên tạo đơn" readonly>
                                <label for="ct_staff_name">Nhân viên tạo đơn</label>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="ct_status" class="form-control" placeholder="Trạng thái" readonly>
                                <label for="ct_status">Trạng thái</label>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="ct_payment_method" class="form-control" placeholder="Phương thức thanh toán" readonly>
                                <label for="ct_payment_method">Phương thức thanh toán</label>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea id="ct_note" class="form-control" placeholder="Ghi chú" style="height: 100px" readonly></textarea>
                                <label for="ct_note">Ghi chú</label>
                            </div>
                        </div>

                        <!-- Địa chỉ giao hàng -->
                        <div class="col-md-12">
                            <div class="form-floating">
                                <textarea id="ct_shipping_address" class="form-control" placeholder="Địa chỉ giao hàng" style="height: 80px" readonly></textarea>
                                <label for="ct_shipping_address">Địa chỉ giao hàng</label>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Chi tiết đơn hàng -->
                <div class="mt-3">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="ct_orderDetailQueue">
                            <!-- Dữ liệu chi tiết đơn hàng sẽ được load bằng JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Tổng tiền đơn hàng -->
                <div class="d-flex justify-content-end mt-3 me-2">
                    <h5 class="fw-bold">
                        Tổng tiền: <span id="ct_tongTienDonHang">0 ₫</span>
                    </h5>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sửa Đơn Hàng -->
<div class="modal fade" id="modalSuaDonHang" tabindex="-1" aria-labelledby="modalSuaDonHangLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Sửa Đơn Hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="formSuaDonHang">
                    <input type="hidden" id="edit_order_id" name="order_id">
                    <div class="row g-3">
                        <!-- Khách hàng -->
                        <div class="col-md-6">
                            <div class="row g-0">
                                <div class="col-10">
                                    <div class="form-floating">
                                        <input type="text" id="edit_user_id" class="form-control" placeholder="" name="user_id" readonly>
                                        <label for="edit_user_id">Khách hàng</label>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <button type="button" class="btn btn-outline-secondary w-100 h-100" id="btnChonKhachHang_Sua">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Nhân viên -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="edit_staff_id" name="staff_id" class="form-control" placeholder="Nhân viên" readonly>
                                <label for="edit_staff_id">Nhân viên tạo đơn</label>
                            </div>
                        </div>

                        <!-- Trạng thái -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="statusSua" name="status" class="form-select">
                                    <option value="Chờ xác nhận">Chờ xác nhận</option>
                                    <option value="Đã thanh toán, chờ giao hàng">Đã thanh toán, chờ giao hàng</option>
                                    <option value="Đang giao hàng">Đang giao hàng</option>
                                    <option value="Giao thành công">Giao thành công</option>
                                    <option value="Đã huỷ">Đã huỷ</option>
                                </select>
                                <label for="statusSua">Tình trạng</label>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select id="payment_method_id_sua" name="payment_method_id" class="form-select">
                                    <?php foreach ($payment_methods as $pm): ?>
                                        <option value="<?= $pm['payment_method_id'] ?>"><?= $pm['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label for="payment_method_id_sua">Phương thức thanh toán</label>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea id="note_sua" name="note" class="form-control" placeholder="Ghi chú" style="height: 100px"></textarea>
                                <label for="note_sua">Ghi chú</label>
                            </div>
                        </div>

                        <!-- Loại địa chỉ -->
                        <div class="col-md-6">
                            <div class="col-md-12 mt-3">
                                <label class="form-label fw-semibold mb-2">Địa chỉ giao hàng</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-4 form-floating">
                                        <select id="province-sua" class="form-select" name="province" required>
                                            <option selected disabled>Chọn Tỉnh/TP</option>
                                            <!-- Option tỉnh/thành sẽ load JS -->
                                        </select>
                                        <label for="province-sua">Tỉnh/TP</label>
                                    </div>
                                    <div class="col-md-4 form-floating">
                                        <select id="district-sua" class="form-select" name="district" required>
                                            <option selected disabled>Chọn Quận/Huyện</option>
                                            <!-- Option quận/huyện load JS -->
                                        </select>
                                        <label for="district-sua">Quận/Huyện</label>
                                    </div>
                                    <div class="col-md-4 form-floating">
                                        <select id="ward-sua" class="form-select" name="ward" required>
                                            <option selected disabled>Chọn Phường/Xã</option>
                                            <!-- Option phường/xã load JS -->
                                        </select>
                                        <label for="ward-sua">Phường/Xã</label>
                                    </div>
                                </div>
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="specific-address-sua" name="address_detail" placeholder="Số nhà, đường..." required>
                                    <label for="specific-address-sua">Địa chỉ cụ thể</label>
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
                            <input type="text" id="product_id_sua" class="form-control pe-5" placeholder="Sản phẩm" name="product_id_sua" readonly>
                            <label for="product_id_sua">Sản phẩm</label>
                            <button type="button" class="btn btn-outline-secondary position-absolute end-0 top-0 mt-2 me-2" id="btnChonSanPham_Sua">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Biến thể -->
                    <div class="col-md-3">
                        <div class="form-floating position-relative">
                            <input type="text" id="variant_id_sua" class="form-control pe-5" placeholder="Biến thể" readonly>
                            <label for="variant_id_sua">Biến thể (variant)</label>
                            <button type="button" class="btn btn-outline-secondary position-absolute end-0 top-0 mt-2 me-2" id="btnChonBienThe_Sua">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Số lượng -->
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="number" id="quantity_sua" class="form-control" placeholder="Số lượng" min="1">
                            <label for="quantity_sua">Số lượng</label>
                        </div>
                    </div>

                    <!-- Đơn giá -->
                    <div class="col-md-2">
                        <div class="form-floating">
                            <input type="text" id="price_sua" class="form-control" placeholder="Đơn giá" readonly>
                            <label for="price_sua">Đơn giá</label>
                        </div>
                    </div>

                    <!-- Nút thêm -->
                    <div class="col-md-2">
                        <button class="btn btn-success w-100" id="btnThemChiTiet_Sua">
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
                        <tbody id="orderDetailQueue_sua">
                            <!-- Sẽ được JS thêm vào -->
                        </tbody>
                    </table>
                    <!-- Tổng tiền đơn hàng -->
                    <div class="d-flex justify-content-end mt-3 me-2">
                        <h5 class="fw-bold">
                            Tổng tiền: <span id="tongTienDonHang_sua">0 ₫</span>
                        </h5>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="btnLuuDonHang_Sua" class="btn btn-primary">
                    <i class="fa fa-save"></i> Lưu thay đổi
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<script>
    function formatVND(amount) {
        if (isNaN(amount)) return '0 ₫';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }


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

    // Hàm toggle địa chỉ
    function setupAddressToggle(savedRadioId, newRadioId, savedContainerId, newContainerId) {
        const savedRadio = document.getElementById(savedRadioId);
        const newRadio = document.getElementById(newRadioId);
        const savedContainer = document.getElementById(savedContainerId);
        const newContainer = document.getElementById(newContainerId);

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
    }

    // Hàm load tỉnh/quận/phường cho selects
    function setupProvinceDistrictWard(provinceId, districtId, wardId) {
        const provinceSelect = document.getElementById(provinceId);
        const districtSelect = document.getElementById(districtId);
        const wardSelect = document.getElementById(wardId);

        if (!provinceSelect || !districtSelect || !wardSelect) return;

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
    }

    // Gọi cho modal Thêm đơn hàng
    setupAddressToggle('addr_saved', 'addr_new', 'saved-container', 'new-container');
    setupProvinceDistrictWard('province', 'district', 'ward');

    // Gọi cho modal Sửa đơn hàng (nếu có)
    // setupAddressToggle('addr_saved_sua', 'addr_new_sua', 'saved-container-sua', 'new-container-sua');
    setupProvinceDistrictWard('province_sua', 'district_sua', 'ward_sua');


    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-choose-user');
        if (!btn) return;

        const userId = btn.getAttribute('data-user-id');
        const userName = btn.getAttribute('data-name');
        if (!previousModalId) return;
        const parentModal = document.getElementById(previousModalId);
        if (!parentModal) return;

        // Lấy input duy nhất name="user_id"
        const userInput = parentModal.querySelector('input[name="user_id"]');
        if (userInput) {
            // Hiển thị tên khách hàng trong input
            userInput.value = userName;

            // Lưu user_id trong data attribute (data-user-id) để submit hoặc xử lý JS
            userInput.setAttribute('data-user-id', userId);
        }

        // Gọi loadUserAddresses với modalId truyền vào để cập nhật đúng select trong modal
        loadUserAddresses(userId, previousModalId);

        // Đóng modal chọn user
        const modalUser = bootstrap.Modal.getInstance(document.getElementById('modalChonUser'));
        if (modalUser) modalUser.hide();
    });



    function loadUserAddresses(userId, modalId = 'modalThemDonHang') {
        fetch('ajax/get_user_address.php?user_id=' + userId)
            .then(res => res.json())
            .then(data => {
                if (!data.success || !Array.isArray(data.data)) {
                    console.warn("Không có địa chỉ nào.");
                    return;
                }

                // Tìm select địa chỉ đã lưu trong modal tương ứng
                const modal = document.getElementById(modalId);
                if (!modal) {
                    console.warn(`Không tìm thấy modal với id=${modalId}`);
                    return;
                }

                // Địa chỉ đã lưu trong modal thêm đơn hàng có id = saved-address
                // Địa chỉ đã lưu trong modal sửa đơn hàng giả sử có id = saved-address-sua
                // Tự động chọn đúng select trong modal
                let selectId = 'saved-address';
                if (modalId === 'modalSuaDonHang') {
                    selectId = 'saved-address-sua';
                }

                const select = modal.querySelector(`#${selectId}`);
                if (!select) {
                    console.warn(`Không tìm thấy select #${selectId} trong modal ${modalId}`);
                    return;
                }

                select.innerHTML = '';

                data.data.forEach(addr => {
                    const option = document.createElement('option');
                    option.value = addr.address_id;
                    option.textContent = `${addr.address_detail}, ${addr.ward}, ${addr.district}, ${addr.province}`;
                    if (addr.is_default == 1) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Lỗi khi lấy địa chỉ:', err);
            });
    }


    document.getElementById('btnChonSanPham').addEventListener('click', function() {
        // Xóa dữ liệu biến thể nếu chọn lại sản phẩm
        const variantInput = document.getElementById('variant_id');
        variantInput.value = "";
        variantInput.dataset.variantId = "";

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

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-choose-product');
        if (!btn) return;

        const productId = btn.getAttribute('data-product-id');
        const productName = btn.getAttribute('data-name');
        const price = btn.getAttribute('data-price');

        if (!previousModalSanPhamId) return;

        const modalElement = document.getElementById(previousModalSanPhamId);
        if (!modalElement) return;

        // Tìm input product_id hoặc product_id_sua
        let input = modalElement.querySelector('#product_id');
        if (!input) input = modalElement.querySelector('#product_id_sua');

        if (input) {
            input.value = productName;
            input.setAttribute('data-product-id', productId);
        }

        // Tìm input price hoặc price_sua
        let priceInput = modalElement.querySelector('#price');
        if (!priceInput) priceInput = modalElement.querySelector('#price_sua');

        if (priceInput) {
            priceInput.value = formatVND(price);
            priceInput.setAttribute('data-price', price);
        }

        // Đóng modal chọn sản phẩm
        const modalSP = bootstrap.Modal.getInstance(document.getElementById('modalChonSanPham'));
        if (modalSP) modalSP.hide();
    });









    // Sự kiện mở modal chọn biến thể từ modal thêm đơn hàng
    document.getElementById('btnChonBienThe').addEventListener('click', function() {
        openModalChonVariant('modalThemDonHang');
    });

    let previousModalVariantId = null;

    function openModalChonVariant(fromModalId) {
        previousModalVariantId = fromModalId;

        const parentModal = document.getElementById(fromModalId);
        // Cố gắng lấy input #product_id hoặc #product_id_sua tùy modal
        let inputEl = parentModal.querySelector('#product_id');
        if (!inputEl) {
            inputEl = parentModal.querySelector('#product_id_sua');
        }

        if (!inputEl) {
            alert("Không tìm thấy ô nhập sản phẩm.");
            return;
        }

        const productId = inputEl.dataset.productId?.trim();

        if (!productId) {
            alert("Vui lòng chọn sản phẩm trước khi chọn biến thể.");
            return;
        }

        // Ẩn modal hiện tại
        const current = bootstrap.Modal.getInstance(parentModal);
        if (current) current.hide();

        // Gọi loadVariants với đúng product_id
        loadVariants(productId);

        // Hiện modal chọn biến thể
        const modal = new bootstrap.Modal(document.getElementById('modalChonVariant'));
        modal.show();
    }



    // Khi modal chọn biến thể bị đóng → mở lại modal trước đó
    document.getElementById('modalChonVariant').addEventListener('hidden.bs.modal', function() {
        if (previousModalVariantId) {
            const backModal = new bootstrap.Modal(document.getElementById(previousModalVariantId));
            backModal.show();
            previousModalVariantId = null;
        }
    });

    let currentFilterParamsVariant = '';

    function loadVariants(productId) {
        if (!productId) {
            alert("Vui lòng chọn sản phẩm trước khi chọn biến thể.");
            return;
        }

        const variantWrap = document.querySelector('#variantTableBody');
        fetch('ajax/load_variants_for_order.php?product_id=' + productId)
            .then(res => res.json())
            .then(data => {
                variantWrap.innerHTML = data.variantHtml || '';
            });
    }


    function updateTongTien() {
        let total = 0;
        document.querySelectorAll('#orderDetailQueue tr').forEach(row => {
            const quantity = parseInt(row.querySelector('input[name="quantities[]"]').value);
            const price = parseFloat(row.querySelector('input[name="prices[]"]').value);
            total += quantity * price;
        });

        document.getElementById('tongTienDonHang').textContent = total.toLocaleString('vi-VN') + ' ₫';
    }


    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-choose-variant');
        if (!btn) return;

        const variantId = btn.getAttribute('data-variant-id');
        const color = btn.getAttribute('data-color');
        const size = btn.getAttribute('data-size');

        if (!previousModalVariantId) return;

        const modalElement = document.getElementById(previousModalVariantId);
        if (!modalElement) return;

        // Thử lấy input variant_id theo 2 kiểu: modal thêm và sửa
        let visibleInput = modalElement.querySelector('#variant_id');
        if (!visibleInput) {
            visibleInput = modalElement.querySelector('#variant_id_sua');
        }
        if (visibleInput) {
            visibleInput.value = `${size} - ${color}`;
            visibleInput.setAttribute('data-variant-id', variantId);
        }

        let hiddenInput = modalElement.querySelector('input[name="variant_id"]');
        if (!hiddenInput) {
            hiddenInput = modalElement.querySelector('input[name="variant_id_sua"]');
        }
        if (hiddenInput) {
            hiddenInput.value = variantId;
        }

        // Đóng modal chọn biến thể
        const modalVariant = bootstrap.Modal.getInstance(document.getElementById('modalChonVariant'));
        if (modalVariant) modalVariant.hide();
    });






    document.getElementById('btnThemChiTiet').addEventListener('click', function(e) {
        e.preventDefault();

        const productInput = document.querySelector('#product_id');
        const variantInput = document.querySelector('#variant_id');
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');

        const productId = productInput?.dataset.productId?.trim();
        const productName = productInput?.value.trim();
        const variantId = variantInput?.dataset.variantId?.trim();
        const variantName = variantInput?.value.trim();
        const quantity = parseInt(quantityInput?.value.trim());
        const price = parseInt(priceInput?.dataset.price?.trim());

        if (!productId || !variantId || !quantity || quantity <= 0) {
            alert("Vui lòng nhập đầy đủ thông tin: Sản phẩm, Biến thể và Số lượng hợp lệ.");
            return;
        }

        const tableBody = document.getElementById('orderDetailQueue');
        let existingRow = null;
        let existingQuantity = 0;

        // Tìm dòng trùng variant_id
        tableBody.querySelectorAll('tr').forEach(row => {
            const hiddenVariantIdInput = row.querySelector('input[name="variant_ids[]"]');
            if (hiddenVariantIdInput && hiddenVariantIdInput.value === variantId) {
                existingRow = row;
                existingQuantity = parseInt(row.querySelector('input[name="quantities[]"]').value);
            }
        });

        const totalQuantity = existingQuantity + quantity;

        // Gọi kiểm tra tồn kho tổng
        fetch('ajax/check_stock_variant.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    variant_id: variantId,
                    quantity: totalQuantity
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert("Sản phẩm không đủ tồn kho, chỉ còn: " + data.message + " sản phẩm!");
                    return;
                }

                // Nếu đã tồn tại dòng → cập nhật
                if (existingRow) {
                    const newTotal = totalQuantity * price;

                    existingRow.querySelector('input[name="quantities[]"]').value = totalQuantity;
                    existingRow.querySelector('td:nth-child(3)').innerText = totalQuantity;

                    existingRow.querySelector('input[name="prices[]"]').value = price;
                    existingRow.querySelector('td:nth-child(4)').innerHTML = `${price.toLocaleString('vi-VN')} ₫`;

                    existingRow.querySelector('td:nth-child(5)').innerText = newTotal.toLocaleString('vi-VN') + " ₫";

                    existingRow.dataset.price = price;
                    existingRow.dataset.total = newTotal;
                } else {
                    // Thêm dòng mới nếu chưa có
                    const totalPrice = quantity * price;
                    const row = document.createElement('tr');
                    row.dataset.price = price;
                    row.dataset.total = totalPrice;

                    row.innerHTML = `
                        <td>
                            <input type="hidden" name="product_ids[]" value="${productId}">
                            ${productName}
                        </td>
                        <td>
                            <input type="hidden" name="variant_ids[]" value="${variantId}">
                            ${variantName}
                        </td>
                        <td>
                            <input type="hidden" name="quantities[]" value="${quantity}">
                            ${quantity}
                        </td>
                        <td>
                            <input type="hidden" name="prices[]" value="${price}">
                            ${price.toLocaleString('vi-VN')} ₫
                        </td>
                        <td>${totalPrice.toLocaleString('vi-VN')} ₫</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(row);
                    updateTongTien();
                }

                // Reset các ô nhập
                productInput.value = "";
                productInput.dataset.productId = "";
                variantInput.value = "";
                variantInput.dataset.variantId = "";
                quantityInput.value = "";
                priceInput.value = "";
                priceInput.dataset.price = "";
            })
            .catch(error => {
                console.error('Lỗi khi kiểm tra tồn kho:', error);
                alert("Đã xảy ra lỗi khi kiểm tra tồn kho.");
            });
    });



    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-row');
        if (!btn) return;

        const row = btn.closest('tr');
        if (row) row.remove();
        updateTongTien();
    });





    document.getElementById('btnLuuDonHang').addEventListener('click', function() {
        const userId = document.querySelector('#user_id')?.dataset.userId;
        const staffId = document.querySelector('#staff_id').dataset.staffId;
        const status = document.querySelector('#statusThem').value;
        const paymentMethodId = document.querySelector('#payment_method_id').value;
        const note = document.querySelector('#note').value.trim();
        console.log(status)

        if (!userId) {
            alert("Vui lòng chọn khách hàng.");
            return;
        }

        // Xử lý địa chỉ
        let shippingAddress = '';
        const savedAddr = document.getElementById('saved-address');
        if (document.getElementById('addr_saved').checked) {
            // Kiểm tra xem có chọn địa chỉ không (khác chuỗi rỗng)
            if (!savedAddr?.value || savedAddr.value.trim() === '') {
                alert("Vui lòng chọn địa chỉ đã lưu hợp lệ.");
                return;
            }
            shippingAddress = savedAddr.selectedOptions[0]?.textContent || '';
            if (!shippingAddress || shippingAddress.trim() === '') {
                alert("Địa chỉ đã lưu không hợp lệ.");
                return;
            }
        } else {
            const specific = document.getElementById('specific-address').value.trim();
            const wardSelect = document.getElementById('ward');
            const districtSelect = document.getElementById('district');
            const provinceSelect = document.getElementById('province');

            if (!specific || !wardSelect.value || !districtSelect.value || !provinceSelect.value) {
                alert("Vui lòng nhập đầy đủ địa chỉ mới.");
                return;
            }

            const ward = wardSelect.selectedOptions[0].textContent;
            const district = districtSelect.selectedOptions[0].textContent;
            const province = provinceSelect.selectedOptions[0].textContent;

            shippingAddress = `${specific}, ${ward}, ${district}, ${province}`;
        }

        // Chi tiết đơn hàng
        const rows = document.querySelectorAll('#orderDetailQueue tr');
        if (rows.length === 0) {
            alert("Vui lòng thêm ít nhất 1 sản phẩm vào đơn hàng.");
            return;
        }

        const orderDetails = [];
        let totalPrice = 0;

        rows.forEach(row => {
            const product_id = row.querySelector('input[name="product_ids[]"]').value;
            const variant_id = row.querySelector('input[name="variant_ids[]"]').value;
            const quantity = parseInt(row.querySelector('input[name="quantities[]"]').value);
            const price = parseFloat(row.querySelector('input[name="prices[]"]').value);
            const lineTotal = quantity * price;

            totalPrice += lineTotal;

            orderDetails.push({
                product_id,
                variant_id,
                quantity,
                price,
                total_price: lineTotal
            });
        });

        // Gửi dữ liệu lên add_order.php
        fetch('ajax/add_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    staff_id: staffId,
                    status,
                    payment_method_id: paymentMethodId,
                    note,
                    shipping_address: shippingAddress,
                    total_price: totalPrice,
                    order_details: orderDetails
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Thêm đơn hàng thành công!");

                    // Đóng modal thêm đơn hàng
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemDonHang'));
                    if (modal) modal.hide();

                    // Reset form inputs trong modal
                    const form = document.getElementById('formThemDonHang');
                    if (form) form.reset();

                    // Reset các input ẩn hoặc các trường dữ liệu động
                    // Ví dụ reset input ẩn user_id, staff_id, data-user-id ...
                    const userInput = document.querySelector('#user_id');
                    if (userInput) {
                        userInput.value = '';
                        userInput.removeAttribute('data-user-id');
                    }
                    const variantInput = document.querySelector('#variant_id');
                    if (variantInput) {
                        variantInput.value = '';
                        variantInput.removeAttribute('data-variant-id');
                    }
                    const productInput = document.querySelector('#product_id');
                    if (productInput) {
                        productInput.value = '';
                        productInput.removeAttribute('data-product-id');
                    }
                    const priceInput = document.querySelector('#price');
                    if (priceInput) {
                        priceInput.value = '';
                        priceInput.removeAttribute('data-price');
                    }

                    // Xóa hết dòng trong bảng chi tiết đơn hàng
                    const tbody = document.getElementById('orderDetailQueue');
                    if (tbody) tbody.innerHTML = '';
                    // Reset form hoặc reload lại bảng
                    loadOrders(1, currentFilterParams);
                } else {
                    alert("Thêm đơn hàng thất bại: " + data.message);
                }
            })
            .catch(err => {
                console.error("Lỗi gửi đơn hàng:", err);
                alert("Đã xảy ra lỗi khi gửi đơn hàng.");
            });
    });





    function showDeleteOrderDetails(data) {
        document.getElementById('delete_user_name').value = data.user_name;
        document.getElementById('delete_staff_name').value = data.staff_name;
        document.getElementById('delete_status').value = data.status;
        document.getElementById('delete_payment_method').value = data.payment_method_name;
        document.getElementById('delete_note').value = data.note;
        document.getElementById('delete_shipping_address').value = data.shipping_address;

        const tbody = document.getElementById('delete_orderDetailQueue');
        tbody.innerHTML = ''; // Xóa dữ liệu cũ

        let totalPrice = 0;
        data.order_details.forEach(item => {
            const row = document.createElement('tr');

            const lineTotal = item.quantity * item.price;
            totalPrice += lineTotal;

            row.innerHTML = `
            <td>${item.product_name}</td>
            <td>${item.variant_name}</td>
            <td>${item.quantity}</td>
            <td>${item.price.toLocaleString('vi-VN')} ₫</td>
            <td>${lineTotal.toLocaleString('vi-VN')} ₫</td>
        `;
            tbody.appendChild(row);
        });

        document.getElementById('delete_tongTienDonHang').textContent = totalPrice.toLocaleString('vi-VN') + ' ₫';

        // Hiện modal xóa
        const modal = new bootstrap.Modal(document.getElementById('modalXoaDonHang'));
        modal.show();
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-order');
        if (!btn) return;

        // Lấy dữ liệu từ data attributes trên nút
        const orderId = btn.dataset.orderId;
        const userName = btn.dataset.userName;
        const staffName = btn.dataset.staffName;
        const status = btn.dataset.status;
        const paymentMethodName = btn.dataset.paymentMethodName;
        const note = btn.dataset.note;
        const shippingAddress = btn.dataset.shippingAddress;

        // Gửi AJAX lấy chi tiết order
        fetch(`ajax/get_order_details.php?order_id=${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modalData = {
                        user_name: userName,
                        staff_name: staffName,
                        status,
                        payment_method_name: paymentMethodName,
                        note,
                        shipping_address: shippingAddress,
                        order_details: data.order_details || []
                    };
                    showDeleteOrderDetails(modalData);

                    // Lưu orderId vào nút xác nhận xóa để dùng sau
                    const btnConfirm = document.getElementById('btnConfirmDeleteOrder');
                    btnConfirm.dataset.orderId = orderId;
                } else {
                    alert('Không lấy được chi tiết đơn hàng: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Lỗi lấy chi tiết đơn hàng:', err);
                alert('Lỗi khi lấy chi tiết đơn hàng.');
            });
    });

    // Xử lý nút xác nhận xóa
    document.getElementById('btnConfirmDeleteOrder').addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        if (!orderId) return;

        fetch('ajax/delete_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Hủy đơn hàng thành công!');
                    const modalEl = document.getElementById('modalXoaDonHang');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    // Reload lại danh sách đơn hàng (nếu bạn có hàm loadOrders)
                    loadOrders(1, currentFilterParams);
                } else {
                    alert('Hủy đơn hàng thất bại: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Lỗi khi hủy đơn hàng:', err);
                alert('Đã xảy ra lỗi khi hủy đơn hàng.');
            });
    });







    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-view-order');
        if (!btn) return;

        // Lấy dữ liệu từ data attributes trên nút
        const orderId = btn.dataset.orderId;
        const userId = btn.dataset.userId;
        const userName = btn.dataset.userName;
        const status = btn.dataset.status;
        const totalPrice = btn.dataset.totalPrice;
        const shippingAddress = btn.dataset.shippingAddress;
        const note = btn.dataset.note;
        const createdAt = btn.dataset.createdAt;
        const paymentMethodId = btn.dataset.paymentMethodId;
        const paymentMethodName = btn.dataset.paymentMethodName;
        const staffId = btn.dataset.staffId;
        const staffName = btn.dataset.staffName;

        // Hàm đổ dữ liệu vào modal
        function fillModal(data) {
            document.getElementById('ct_user_name').value = data.user_name || '';
            document.getElementById('ct_staff_name').value = data.staff_name || '';
            document.getElementById('ct_status').value = data.status || '';
            document.getElementById('ct_payment_method').value = data.payment_method_name || '';
            document.getElementById('ct_note').value = data.note || '';
            document.getElementById('ct_shipping_address').value = data.shipping_address || '';

            const tbody = document.getElementById('ct_orderDetailQueue');
            tbody.innerHTML = '';

            let total = 0;
            data.order_details.forEach(item => {
                const tr = document.createElement('tr');
                const lineTotal = item.quantity * item.price;
                total += lineTotal;

                tr.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.variant_name}</td>
                <td>${item.quantity}</td>
                <td>${Number(item.price).toLocaleString('vi-VN')} ₫</td>
                <td>${lineTotal.toLocaleString('vi-VN')} ₫</td>
            `;
                tbody.appendChild(tr);
            });

            document.getElementById('ct_tongTienDonHang').textContent = total.toLocaleString('vi-VN') + ' ₫';
        }

        // Gửi AJAX lấy chi tiết đơn hàng theo orderId
        fetch(`ajax/get_order_details.php?order_id=${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Gộp dữ liệu chung từ nút với chi tiết order trả về từ server
                    const modalData = {
                        user_name: userName,
                        staff_name: staffName,
                        status,
                        payment_method_name: paymentMethodName,
                        note,
                        shipping_address: shippingAddress,
                        order_details: data.order_details || []
                    };

                    fillModal(modalData);

                    // Hiện modal chi tiết
                    const modal = new bootstrap.Modal(document.getElementById('modalXemChiTietDonHang'));
                    modal.show();
                } else {
                    alert('Không lấy được chi tiết đơn hàng: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Lỗi lấy chi tiết đơn hàng:', err);
                alert('Lỗi khi lấy chi tiết đơn hàng.');
            });
    });











    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-order');
        if (!btn) return;

        // Lấy data attributes từ nút bấm
        const orderId = btn.getAttribute('data-order-id');
        const userId = btn.getAttribute('data-user-id');
        const userName = btn.getAttribute('data-user-name');
        const staffId = btn.getAttribute('data-staff-id');
        const staffName = btn.getAttribute('data-staff-name');
        const status = btn.getAttribute('data-status');
        const paymentMethodId = btn.getAttribute('data-payment-method-id');
        const note = btn.getAttribute('data-note');
        const shippingAddress = btn.getAttribute('data-shipping-address');
        // Có thể thêm created_at nếu cần


        // Đặt dữ liệu vào modal sửa
        document.getElementById('edit_order_id').value = orderId;

        const userInput = document.getElementById('edit_user_id');
        userInput.value = userName;
        userInput.setAttribute('data-user-id', userId);

        const staffInput = document.getElementById('edit_staff_id');
        staffInput.value = staffName;
        staffInput.setAttribute('data-staff-id', staffId);

        const statusSelect = document.getElementById('statusSua');
        statusSelect.value = status;

        const paymentMethodSelect = document.getElementById('payment_method_id_sua');
        paymentMethodSelect.value = paymentMethodId;

        document.getElementById('note_sua').value = note;

        // Xử lý địa chỉ giao hàng
        if (shippingAddress) {
            // Tách địa chỉ thành các phần: địa chỉ cụ thể, xã, huyện, tỉnh
            const parts = shippingAddress.split(',').map(p => p.trim());
            if (parts.length >= 4) {
                const specificAddress = parts[0];
                const wardText = parts[1];
                const districtText = parts[2];
                const provinceText = parts[3];

                // Điền địa chỉ cụ thể
                document.getElementById('specific-address-sua').value = specificAddress;

                // Chọn tỉnh, quận, xã
                selectLocation(provinceText, districtText, wardText);
            } else {
                console.warn('Định dạng địa chỉ không hợp lệ:', shippingAddress);
            }
        }

        // Xóa sạch các chi tiết đơn hàng cũ trong modal sửa trước khi load lại
        const tbody = document.getElementById('orderDetailQueue_sua');
        tbody.innerHTML = '';

        // Sau đó bạn nên fetch chi tiết đơn hàng qua AJAX để đổ chi tiết từng dòng sản phẩm (product, variant, qty, price) vào tbody
        fetch('ajax/get_order_details.php?order_id=' + orderId)
            .then(res => res.json())
            .then(data => {
                if (data.success && Array.isArray(data.order_details)) {
                    let totalPrice = 0;
                    data.order_details.forEach(item => {
                        const lineTotal = item.quantity * item.price;
                        totalPrice += lineTotal;

                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <input type="hidden" name="product_ids_sua[]" value="${item.product_id}">
                                ${item.product_name}
                            </td>
                            <td>
                                <input type="hidden" name="variant_ids_sua[]" value="${item.variant_id}">
                                ${item.variant_name}
                            </td>
                            <td>
                                <input type="hidden" name="quantities_sua[]" value="${item.quantity}">
                                ${item.quantity}
                            </td>
                            <td>
                                <input type="hidden" name="prices_sua[]" value="${item.price}">
                                ${Number(item.price).toLocaleString('vi-VN')} ₫
                            </td>
                            <td>${lineTotal.toLocaleString('vi-VN')} ₫</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-remove-row-sua">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                    document.getElementById('tongTienDonHang_sua').textContent = totalPrice.toLocaleString('vi-VN') + ' ₫';
                } else {
                    console.error('Lỗi lấy chi tiết đơn hàng:', data.message);
                }
            })
            .catch(err => {
                console.error('Lỗi fetch chi tiết đơn hàng:', err);
            });

        // Hiển thị modal sửa
        previousModalId = 'modalSuaDonHang';
        const modalSua = new bootstrap.Modal(document.getElementById('modalSuaDonHang'));
        modalSua.show();
    });

    // Hàm chọn tỉnh, quận, xã từ text
    function selectLocation(provinceText, districtText, wardText) {
        const provinceSelect = document.getElementById('province-sua');
        const districtSelect = document.getElementById('district-sua');
        const wardSelect = document.getElementById('ward-sua');

        // Load tỉnh
        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(data => {
                provinceSelect.innerHTML = '<option selected disabled>Chọn Tỉnh/TP</option>';
                let provinceCode = null;
                data.forEach(p => {
                    const name = p.name.replace(/^Tỉnh |^Thành phố /, '');
                    provinceSelect.add(new Option(name, p.code));
                    if (name === provinceText) {
                        provinceCode = p.code;
                        provinceSelect.value = p.code;
                    }
                });

                if (provinceCode) {
                    // Load quận/huyện
                    fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
                        .then(res => res.json())
                        .then(obj => {
                            districtSelect.innerHTML = '<option selected disabled>Chọn Quận/Huyện</option>';
                            let districtCode = null;
                            obj.districts.forEach(d => {
                                districtSelect.add(new Option(d.name, d.code));
                                if (d.name === districtText) {
                                    districtCode = d.code;
                                    districtSelect.value = d.code;
                                }
                            });

                            if (districtCode) {
                                // Load phường/xã
                                fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
                                    .then(res => res.json())
                                    .then(obj => {
                                        wardSelect.innerHTML = '<option selected disabled>Chọn Phường/Xã</option>';
                                        obj.wards.forEach(w => {
                                            wardSelect.add(new Option(w.name, w.code));
                                            if (w.name === wardText) {
                                                wardSelect.value = w.code;
                                            }
                                        });
                                    });
                            }
                        });
                }
            })
            .catch(err => {
                console.error('Lỗi khi load địa chỉ:', err);
            });
    }

    document.getElementById('btnChonSanPham_Sua').addEventListener('click', function() {
        // Xóa dữ liệu biến thể trong modal sửa nếu chọn lại sản phẩm
        const variantInputSua = document.getElementById('variant_id_sua');
        if (variantInputSua) {
            variantInputSua.value = "";
            variantInputSua.dataset.variantId = "";
        }

        // Mở modal chọn sản phẩm, truyền id modal sửa để xử lý đúng
        openModalChonSanPham('modalSuaDonHang');
    });

    document.getElementById('btnChonBienThe_Sua').addEventListener('click', function() {
        openModalChonVariant('modalSuaDonHang');
    });

    function updateTongTien_Sua() {
        let total = 0;
        document.querySelectorAll('#orderDetailQueue_sua tr').forEach(row => {
            const quantity = parseInt(row.querySelector('input[name="quantities_sua[]"]').value);
            const price = parseFloat(row.querySelector('input[name="prices_sua[]"]').value);
            total += quantity * price;
        });

        document.getElementById('tongTienDonHang_sua').textContent = total.toLocaleString('vi-VN') + ' ₫';
    }


    document.getElementById('btnThemChiTiet_Sua').addEventListener('click', function(e) {
        e.preventDefault();

        const productInput = document.querySelector('#product_id_sua');
        const variantInput = document.querySelector('#variant_id_sua');
        const quantityInput = document.getElementById('quantity_sua');
        const priceInput = document.getElementById('price_sua');

        const productId = productInput?.dataset.productId?.trim();
        const productName = productInput?.value.trim();
        const variantId = variantInput?.dataset.variantId?.trim();
        const variantName = variantInput?.value.trim();
        const quantity = parseInt(quantityInput?.value.trim());
        const price = parseInt(priceInput?.dataset.price?.trim());

        if (!productId || !variantId || !quantity || quantity <= 0) {
            alert("Vui lòng nhập đầy đủ thông tin: Sản phẩm, Biến thể và Số lượng hợp lệ.");
            return;
        }

        const tableBody = document.getElementById('orderDetailQueue_sua');
        let existingRow = null;
        let existingQuantity = 0;

        // Tìm dòng trùng variant_id
        tableBody.querySelectorAll('tr').forEach(row => {
            const hiddenVariantIdInput = row.querySelector('input[name="variant_ids_sua[]"]');
            if (hiddenVariantIdInput && hiddenVariantIdInput.value === variantId) {
                existingRow = row;
                existingQuantity = parseInt(row.querySelector('input[name="quantities_sua[]"]').value);
            }
        });

        const totalQuantity = existingQuantity + quantity;

        // Gọi kiểm tra tồn kho tổng
        fetch('ajax/check_stock_variant.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    variant_id: variantId,
                    quantity: totalQuantity
                })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert("Sản phẩm không đủ tồn kho, chỉ còn: " + data.message + " sản phẩm!");
                    return;
                }

                // Nếu đã tồn tại dòng → cập nhật
                if (existingRow) {
                    const newTotal = totalQuantity * price;

                    existingRow.querySelector('input[name="quantities_sua[]"]').value = totalQuantity;
                    existingRow.querySelector('td:nth-child(3)').innerText = totalQuantity;

                    existingRow.querySelector('input[name="prices_sua[]"]').value = price;
                    existingRow.querySelector('td:nth-child(4)').innerHTML = `${price.toLocaleString('vi-VN')} ₫`;

                    existingRow.querySelector('td:nth-child(5)').innerText = newTotal.toLocaleString('vi-VN') + " ₫";

                    existingRow.dataset.price = price;
                    existingRow.dataset.total = newTotal;
                } else {
                    // Thêm dòng mới nếu chưa có
                    const totalPrice = quantity * price;
                    const row = document.createElement('tr');
                    row.dataset.price = price;
                    row.dataset.total = totalPrice;

                    row.innerHTML = `
                    <td>
                        <input type="hidden" name="product_ids_sua[]" value="${productId}">
                        ${productName}
                    </td>
                    <td>
                        <input type="hidden" name="variant_ids_sua[]" value="${variantId}">
                        ${variantName}
                    </td>
                    <td>
                        <input type="hidden" name="quantities_sua[]" value="${quantity}">
                        ${quantity}
                    </td>
                    <td>
                        <input type="hidden" name="prices_sua[]" value="${price}">
                        ${price.toLocaleString('vi-VN')} ₫
                    </td>
                    <td>${totalPrice.toLocaleString('vi-VN')} ₫</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-remove-row-sua">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                    tableBody.appendChild(row);
                    updateTongTien_Sua();
                }

                // Reset các ô nhập
                productInput.value = "";
                productInput.removeAttribute('data-product-id');
                variantInput.value = "";
                variantInput.removeAttribute('data-variant-id');
                quantityInput.value = "";
                priceInput.value = "";
                priceInput.removeAttribute('data-price');
            })
            .catch(error => {
                console.error('Lỗi khi kiểm tra tồn kho:', error);
                alert("Đã xảy ra lỗi khi kiểm tra tồn kho.");
            });
    });


    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-remove-row-sua');
        if (!btn) return;

        const row = btn.closest('tr');
        if (row) row.remove();
        updateTongTien_Sua();
    });


    document.getElementById('btnLuuDonHang_Sua').addEventListener('click', function() {
        const userInput = document.querySelector('#edit_user_id');
        const userId = userInput?.dataset.userId || userInput?.value.trim();
        const staffInput = document.querySelector('#edit_staff_id');
        const staffId = staffInput?.dataset.staffId || staffInput?.value.trim();
        const status = document.querySelector('#statusSua').value;
        const paymentMethodId = document.querySelector('#payment_method_id_sua').value;
        const note = document.querySelector('#note_sua').value.trim();

        if (!userId) {
            alert("Vui lòng chọn khách hàng.");
            return;
        }

        // Địa chỉ: xử lý theo radio trong modal sửa
        let shippingAddress = '';
        const specific = document.getElementById('specific-address-sua').value.trim();
        const wardSelect = document.getElementById('ward-sua');
        const districtSelect = document.getElementById('district-sua');
        const provinceSelect = document.getElementById('province-sua');

        if (!specific || !wardSelect.value || !districtSelect.value || !provinceSelect.value) {
            alert("Vui lòng nhập đầy đủ địa chỉ mới.");
            return;
        }

        const ward = wardSelect.selectedOptions[0].textContent;
        const district = districtSelect.selectedOptions[0].textContent;
        const province = provinceSelect.selectedOptions[0].textContent;

        shippingAddress = `${specific}, ${ward}, ${district}, ${province}`;


        // Chi tiết đơn hàng trong modal sửa
        const rows = document.querySelectorAll('#orderDetailQueue_sua tr');
        if (rows.length === 0) {
            alert("Vui lòng thêm ít nhất 1 sản phẩm vào đơn hàng.");
            return;
        }

        const orderDetails = [];
        let totalPrice = 0;

        rows.forEach(row => {
            const product_id = row.querySelector('input[name="product_ids_sua[]"]').value;
            const variant_id = row.querySelector('input[name="variant_ids_sua[]"]').value;
            const quantity = parseInt(row.querySelector('input[name="quantities_sua[]"]').value);
            const price = parseFloat(row.querySelector('input[name="prices_sua[]"]').value);
            const lineTotal = quantity * price;

            totalPrice += lineTotal;

            orderDetails.push({
                product_id,
                variant_id,
                quantity,
                price,
                total_price: lineTotal
            });
        });

        // Gửi dữ liệu lên ajax xử lý cập nhật đơn hàng
        fetch('ajax/update_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: userId,
                    staff_id: staffId,
                    status,
                    payment_method_id: paymentMethodId,
                    note,
                    shipping_address: shippingAddress,
                    total_price: totalPrice,
                    order_details: orderDetails,
                    order_id: document.getElementById('edit_order_id').value
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert("Cập nhật đơn hàng thành công!");

                    // Đóng modal sửa đơn hàng
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaDonHang'));
                    if (modal) modal.hide();

                    // Reset hoặc làm sạch dữ liệu modal sửa (nếu cần)
                    const tbody = document.getElementById('orderDetailQueue_sua');
                    if (tbody) tbody.innerHTML = '';

                    const formSua = document.getElementById('formSuaDonHang');
                    if (formSua) formSua.reset();

                    // Reload lại danh sách đơn hàng
                    loadOrders(1, currentFilterParams);
                } else {
                    alert("Cập nhật đơn hàng thất bại: " + data.message);
                }
            })
            .catch(err => {
                console.error("Lỗi gửi cập nhật đơn hàng:", err);
                alert("Đã xảy ra lỗi khi cập nhật đơn hàng.");
            });
    });
</script>