<?php
include_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Lấy tham số lọc
$thang = isset($_GET['thang']) && $_GET['thang'] !== '' ? (int)$_GET['thang'] : '';
$nam = $_GET['nam'] ?? date('Y');
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$status = $_GET['status'] ?? '';

// $status = $_GET['status'] ?? ''; // Khai báo trước khi dùng
// $status = ''; 
$whereClausesDoanhThu = [];
$paramsDoanhThu = [];

// if ($status !== '') {
//     $whereClausesDoanhThu[] = "o.status = ?";
//     $paramsDoanhThu[] = $status;
// }

$whereClausesDoanhThu = [];
$paramsDoanhThu = [];

// if ($status !== '') {
//     $whereClausesDoanhThu[] = "o.status = ?";
//     $paramsDoanhThu[] = $status;
// }
if ($thang !== '') {
    $whereClausesDoanhThu[] = "MONTH(o.created_at) = ?";
    $paramsDoanhThu[] = $thang;
}
if ($nam !== '') {
    $whereClausesDoanhThu[] = "YEAR(o.created_at) = ?";
    $paramsDoanhThu[] = $nam;
}
if ($from_date !== '') {
    $whereClausesDoanhThu[] = "DATE(o.created_at) >= ?";
    $paramsDoanhThu[] = $from_date;
}
if ($to_date !== '') {
    $whereClausesDoanhThu[] = "DATE(o.created_at) <= ?";
    $paramsDoanhThu[] = $to_date;
}
if ($category_id !== '') {
    $whereClausesDoanhThu[] = "p.category_id = ?";
    $paramsDoanhThu[] = $category_id;
}

$whereSqlDoanhThu = count($whereClausesDoanhThu) > 0
    ? "WHERE " . implode(' AND ', $whereClausesDoanhThu)
    : '';

$category_id = $_GET['category_id'] ?? '';

// Build điều kiện lọc chung (áp dụng cho đa số truy vấn)
$whereClauses = [];
$params = [];
if ($thang !== '') {
    $whereClauses[] = "MONTH(o.created_at) = ?";
    $params[] = $thang;
}
if ($nam !== '') {
    $whereClauses[] = "YEAR(o.created_at) = ?";
    $params[] = $nam;
}
if ($from_date !== '') {
    $whereClauses[] = "DATE(o.created_at) >= ?";
    $params[] = $from_date;
}
if ($to_date !== '') {
    $whereClauses[] = "DATE(o.created_at) <= ?";
    $params[] = $to_date;
}
if ($category_id !== '') {
    $whereClauses[] = "p.category_id = ?";
    $params[] = $category_id;
}

$whereSql = count($whereClauses) > 0 ? "WHERE " . implode(' AND ', $whereClauses) : '';

// --- Truy vấn doanh thu theo ngày ---
$doanhThuTheoNgay = $db->select("
    SELECT DATE(o.created_at) AS ngay,
           SUM(od.quantity * od.price) AS doanhthu,
           SUM(od.quantity * p.price) AS von
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN products p ON p.product_id = od.product_id
    $whereSqlDoanhThu
    GROUP BY ngay
    ORDER BY ngay ASC
    LIMIT 31
", $paramsDoanhThu);

// Xử lý dữ liệu biểu đồ
$labels = [];
$doanhthuData = [];
$vonData = [];
$loinhuanData = [];

foreach ($doanhThuTheoNgay as $row) {
    $labels[] = $row['ngay'];
    $doanhthuData[] = (float)$row['doanhthu'];
    $vonData[] = (float)$row['von'];
    $loinhuanData[] = (float)$row['doanhthu'] - (float)$row['von'];
}
$limitSanPham = isset($_GET['limit_sanpham']) ? (int)$_GET['limit_sanpham'] : 5;
// --- Top 5 sản phẩm bán chạy ---
$topSanPham = $db->select("
    SELECT p.name, p.price_sale AS price, SUM(od.quantity) AS soluong
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN products p ON p.product_id = od.product_id
    $whereSql
    GROUP BY p.product_id
    ORDER BY soluong DESC
    LIMIT $limitSanPham
", $params);


// --- Lấy danh sách đơn hàng mới nhất (limit 10) ---
// Lọc không dựa trên trạng thái (nên không thêm điều kiện $status)
$whereClausesDonHangMoi = [];
$paramsDonHangMoi = [];

if ($thang !== '') {
    $whereClausesDonHangMoi[] = "MONTH(o.created_at) = ?";
    $paramsDonHangMoi[] = $thang;
}
if ($nam !== '') {
    $whereClausesDonHangMoi[] = "YEAR(o.created_at) = ?";
    $paramsDonHangMoi[] = $nam;
}
if ($from_date !== '') {
    $whereClausesDonHangMoi[] = "DATE(o.created_at) >= ?";
    $paramsDonHangMoi[] = $from_date;
}
if ($to_date !== '') {
    $whereClausesDonHangMoi[] = "DATE(o.created_at) <= ?";
    $paramsDonHangMoi[] = $to_date;
}
if ($category_id !== '') {
    $whereClausesDonHangMoi[] = "p.category_id = ?";
    $paramsDonHangMoi[] = $category_id;
}

$whereSqlDonHangMoi = count($whereClausesDonHangMoi) > 0 ? "WHERE " . implode(' AND ', $whereClausesDonHangMoi) : '';

$donHangMoi = $db->select("
    SELECT o.order_id, u.name as customer_name, o.total_price, o.status, o.created_at
    FROM orders o
    JOIN users u ON u.user_id = o.user_id
    JOIN order_details od ON od.order_id = o.order_id
    JOIN products p ON p.product_id = od.product_id
    $whereSqlDonHangMoi
    GROUP BY o.order_id
    ORDER BY o.created_at DESC
    LIMIT 10
", $paramsDonHangMoi);

// --- Lấy 5 khách hàng mua nhiều nhất ---
$whereOrderClauses = [];
$orderParams = [];

if ($from_date !== '') {
    $whereOrderClauses[] = "DATE(o.created_at) >= ?";
    $orderParams[] = $from_date;
}
if ($to_date !== '') {
    $whereOrderClauses[] = "DATE(o.created_at) <= ?";
    $orderParams[] = $to_date;
}

$whereOrderSql = count($whereOrderClauses) > 0 ? "WHERE " . implode(' AND ', $whereOrderClauses) : '';

$limitKhach = isset($_GET['limit_khach']) ? (int)$_GET['limit_khach'] : 5;
$topUsers = $db->select("
    SELECT u.user_id, u.name, SUM(o.total_price) AS tong_tien_mua
    FROM users u
    JOIN orders o ON u.user_id = o.user_id
    $whereOrderSql
    GROUP BY u.user_id, u.name
    ORDER BY tong_tien_mua DESC
    LIMIT $limitKhach
", $orderParams);

$userIds = array_column($topUsers, 'user_id');
$ordersOfTopUsers = [];

if (count($userIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($userIds), '?'));
    $ordersOfTopUsers = $db->select("
        SELECT o.user_id, o.order_id, o.total_price, o.created_at
        FROM orders o
        WHERE o.user_id IN ($placeholders)
        " . ($whereOrderSql ? "AND " . implode(' AND ', $whereOrderClauses) : '') . "
        ORDER BY o.created_at DESC
    ", array_merge($userIds, $orderParams));
}

// Gom nhóm khách hàng
$topKhachHangGroup = [];
foreach ($topUsers as $user) {
    $topKhachHangGroup[$user['user_id']] = [
        'user_id' => $user['user_id'],
        'name' => $user['name'],
        'tong_tien_mua' => $user['tong_tien_mua'],
        'don_hang' => []
    ];
}
foreach ($ordersOfTopUsers as $order) {
    $topKhachHangGroup[$order['user_id']]['don_hang'][] = [
        'order_id' => $order['order_id'],
        'total_price' => $order['total_price'],
        'created_at' => $order['created_at']
    ];
}
// ✅ Sắp xếp đơn hàng của từng khách theo tổng tiền giảm dần
foreach ($topKhachHangGroup as &$khach) {
    usort($khach['don_hang'], function ($a, $b) {
        if ($b['total_price'] == $a['total_price']) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        }
        return $b['total_price'] - $a['total_price'];
    });
}

unset($khach); // Xóa biến tham chiếu

// --- Thống kê số lượng đơn theo trạng thái ---
$whereStatus = [];
$statusParams = [];

if ($from_date !== '') {
    $whereStatus[] = "DATE(created_at) >= ?";
    $statusParams[] = $from_date;
}
if ($to_date !== '') {
    $whereStatus[] = "DATE(created_at) <= ?";
    $statusParams[] = $to_date;
}
$whereStatusSql = count($whereStatus) ? 'WHERE ' . implode(' AND ', $whereStatus) : '';

if ($category_id !== '') {
    $statusCounts = $db->select("
        SELECT o.status, COUNT(DISTINCT o.order_id) AS count
        FROM orders o
        JOIN order_details od ON od.order_id = o.order_id
        JOIN products p ON p.product_id = od.product_id
        $whereStatusSql
        GROUP BY o.status
    ", $statusParams);
} else {
    $statusCounts = $db->select("
        SELECT o.status, COUNT(*) AS count
        FROM orders o
        $whereStatusSql
        GROUP BY o.status
    ", $statusParams);
}


// Tạo mảng labels và data cho biểu đồ trạng thái
$statusLabels = [];
$statusData = [];
foreach ($statusCounts as $row) {
    $statusLabels[] = $row['status'];
    $statusData[] = (int)$row['count'];
}

// Lấy 5 khách hàng đầu tiên (nếu cần)
$topKhachHangGroup = array_slice($topKhachHangGroup, 0, 5, true);

?>
<div class="container my-4">

    <h1 class="mb-4">Thống kê hệ thống</h1>

   <!-- Nút mở modal -->
   <button type="button" class="btn btn-outline-secondary rounded" data-bs-toggle="modal" data-bs-target="#filterModal"style="margin-bottom:20px;">
  <i class="fa fa-filter"></i> Lọc
</button>
    <button type="button" class="btn btn-outline-secondary"  onclick="resetFilter()" style="margin-bottom:20px;">
      <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
    </button>
<!-- Modal lọc sản phẩm -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="GET" action="index.php" class="modal-content">
    <input type="hidden" name="page" value="thongke">

      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Lọc</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col">
            <label for="from_date" class="form-label">Từ ngày:</label>
            <input type="date" class="form-control" id="from_date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
          </div>
          <div class="col">
            <label for="to_date" class="form-label">Đến ngày:</label>
            <input type="date" class="form-control" id="to_date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Lọc</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </form>
  </div>
</div>

   
    
    <!-- Tổng quan -->
    <div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="p-3 bg-primary text-white rounded stat-box text-center fw-bold">
            <i class="fa-solid fa-box fa-2x mb-2"></i>
            <div>Sản phẩm</div>
            <div class="fs-4" id="stat-sanpham"><?= number_format($tongSanPham) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="p-3 bg-success text-white rounded stat-box text-center fw-bold">
            <i class="fa-solid fa-user-group fa-2x mb-2"></i>
            <div>Khách hàng</div>
            <div class="fs-4" id="stat-khachhang"><?= number_format($tongKhachHang) ?></div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="p-3 bg-info text-white rounded stat-box text-center fw-bold">
            <i class="fa-solid fa-user-tie fa-2x mb-2"></i>
            <div>Nhân viên</div>
            <div class="fs-4" id="stat-nhanvien"><?= number_format($tongNhanVien) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-warning text-dark rounded stat-box text-center fw-bold">
            <i class="fa-solid fa-truck fa-2x mb-2"></i>
            <div>Nhà cung cấp</div>
            <div class="fs-4" id="stat-nhacungcap"><?= number_format($tongNhaCungCap) ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 bg-danger text-white rounded stat-box text-center fw-bold">
            <i class="fa-solid fa-boxes-packing fa-2x mb-2"></i>
            <div>Tồn kho</div>
            <div class="fs-4" id="stat-tonkho"><?= number_format($tongTonKho) ?></div>
        </div>
    </div>
</div>


<!-- Biểu đồ doanh thu theo ngày -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card p-3 shadow-sm h-100" id="chartDoanhThuContainer">
            <h5>Biểu đồ doanh thu theo ngày</h5>
            <canvas id="revenueChart" height="150"></canvas>
        </div>
    </div>
    <!-- Biểu đồ trạng thái đơn hàng (ví dụ tĩnh) -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm h-100" id="chartStatusContainer">
            <h5>Thống kê trạng thái đơn hàng</h5>
            <canvas id="statusPieChart" height="100"></canvas>
        </div>
    </div>
</div>
<div class="card p-3 shadow-sm mb-4" id="topKhachHangContainer">
  <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 mb-0">
      <h5 class="mb-0">Top khách hàng mua nhiều nhất</h5>
      <form method="GET" class="d-flex align-items-center gap-2 mb-0">
        <?php foreach ($_GET as $k => $v): if ($k !== 'limit_khach') : ?>
            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
        <?php endif; endforeach; ?>
        <label for="limit_khach" class="mb-0">Hiển thị:</label>
        <input type="number" name="limit_khach" id="limit_khach"
                class="form-control form-control-sm w-auto"
                value="<?= htmlspecialchars($_GET['limit_khach'] ?? 5) ?>"
                min="1" max="100"
                onchange="this.form.submit()">
        </form>
    </div>
  </div>

    <?php if (count($topKhachHangGroup) > 0): ?>
        <table class="table table-striped table-bordered align-middle mb-0">
    <thead>
        <tr>
            <th>#</th>
            <th>Khách hàng</th>
            <th>Tổng tiền mua (VNĐ)</th>
            <th>Chi tiết đơn hàng</th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach ($topKhachHangGroup as $kh): ?>
            <tr 
                data-bs-toggle="collapse" 
                data-bs-target="#orders-<?= $kh['user_id'] ?>" 
                aria-expanded="false" 
                aria-controls="orders-<?= $kh['user_id'] ?>" 
                style="cursor: pointer;"
                role="button"
                tabindex="0"
                class="collapsed"
            >
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($kh['name']) ?></td>
                <td><?= number_format($kh['tong_tien_mua']) ?></td>
                <td><small><em>Nhấn để xem đơn hàng</em></small></td>
            </tr>
            <tr class="collapse" id="orders-<?= $kh['user_id'] ?>">
                <td colspan="4" class="p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Tổng tiền (VNĐ)</th>
                                <th>Ngày đặt</th>
                                <th>Xem chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($kh['don_hang'] as $dh): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dh['order_id']) ?></td>
                                    <td><?= number_format($dh['total_price']) ?></td>
                                    <td><?= htmlspecialchars($dh['created_at']) ?></td>
                                    <td>
                                    <button type="button" class="btn btn-sm btn-primary btn-view-order"
                                            data-order-id="<?= $dh['order_id'] ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#orderDetailModal">
                                            Xem
                                    </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    <?php else: ?>
        <p>Không có dữ liệu khách hàng mua nhiều nhất trong khoảng thời gian này.</p>
    <?php endif; ?>
</div>
<!-- Bảng danh sách đơn hàng mới và bảng top 5 sản phẩm bán chạy -->
<div class="row mb-5">
    <!-- Danh sách đơn hàng mới -->
    <div class="col-md-8">
        <div class="card p-3 shadow-sm h-100" id="donHangMoiContainer">
            <h5>Danh sách đơn hàng mới</h5>
            <h6 style="color: #888;">(10 sản phẩm)</h6>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền (VNĐ)</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donHangMoi as $dh): ?>
                        <tr>
                            <td><?= htmlspecialchars($dh['order_id']) ?></td>
                            <td><?= htmlspecialchars($dh['customer_name']) ?></td>
                            <td><?= number_format($dh['total_price']) ?></td>
                            <td>
                                <?php
                                // Làm sạch dữ liệu trạng thái
                                $st = mb_strtolower(trim($dh['status']), 'UTF-8'); // dùng mb_strtolower để xử lý tiếng Việt
                                $class = 'badge';
                                $style = '';
                                $displayText = trim($dh['status']); // Mặc định hiển thị nguyên bản

                                switch ($st) {
                                    case 'chờ xác nhận':
                                        $style = 'background-color: #f1c40f; color: #000;';
                                        break;
                                    case 'đã thanh toán, chờ giao hàng':
                                        $style = 'background-color: #3498db; color: #fff;';
                                        break;
                                    case 'đang giao hàng':
                                        $style = 'background-color: #e67e22; color: #fff;';
                                        break;
                                    case 'giao thành công':
                                        $style = 'background-color: #2ecc71; color: #fff;';
                                        break;
                                    case 'đã huỷ':
                                        $style = 'background-color: #e74c3c; color: #fff;';
                                        break;
                                    default:
                                        $style = 'background-color: #ccc; color: #000;';
                                }
                                ?>
                                <span class="<?= $class ?>" style="<?= $style ?>"><?= htmlspecialchars($displayText) ?></span>
                            </td>
                            <td><?= htmlspecialchars($dh['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Top 5 sản phẩm bán chạy -->
    <div class="col-md-4">
    <div class="card p-3 shadow-sm h-100" id="topSanPhamContainer">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h5 class="mb-0">Top sản phẩm bán chạy</h5>
            <form method="GET" class="d-flex align-items-center gap-2 mb-0">
                <?php foreach ($_GET as $k => $v): if ($k !== 'limit_sanpham') : ?>
                    <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                <?php endif; endforeach; ?>
                <label class="mb-0" for="limit_sanpham">Hiển thị:</label>
                <input type="number" name="limit_sanpham" id="limit_sanpham"
                    class="form-control form-control-sm w-auto"
                    value="<?= htmlspecialchars($_GET['limit_sanpham'] ?? 5) ?>"
                    min="1" max="100"
                    onchange="this.form.submit()">
            </form>
        </div>
        <style>
#scrollable-product-table-wrapper {
    max-height: 480px;
    overflow-y: auto;
    display: block;
}

#scrollable-product-table {
    table-layout: fixed;
    width: 100%;
}

#scrollable-product-table th,
#scrollable-product-table td {
    padding: 8px;
    word-wrap: break-word;
}
</style>

<div id="scrollable-product-table-wrapper">
  <table id="scrollable-product-table" class="table table-striped align-middle mb-0">
    <thead class="bg-light sticky-top">
        <tr>
            <th style="width: 50%;">Tên sản phẩm</th>
            <th style="width: 20%;">Lượt mua</th>
            <th style="width: 30%;">Giá (VNĐ)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($topSanPham as $sp): ?>
        <tr>
            <td class="text-center"><?= htmlspecialchars($sp['name']) ?></td>
            <td class="text-center"><?= intval($sp['soluong']) ?></td>
            <td><?= number_format($sp['price'] ?? 0) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>

            </div>
        </div>
    </div>
</div>
<!-- Modal xem chi tiết đơn hàng -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     aria-labelledby="orderDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div id="orderDetailContent">Đang tải...</div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Biểu đồ doanh thu theo ngày
    const revenueLabels = <?= json_encode($labels ?: ["Không có dữ liệu"]) ?>;
    const doanhthuData = <?= json_encode($doanhthuData) ?>;
    const vonData = <?= json_encode($vonData) ?>;
    const loinhuanData = <?= json_encode($loinhuanData) ?>;
    const ctxStatus = document.getElementById('statusPieChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'pie',
        data: {
            labels: <?= json_encode($statusLabels) ?>,
            datasets: [{
                data: <?= json_encode($statusData) ?>,
                backgroundColor: [
                    '#f1c40f', // màu vàng cho chờ xác nhận
                    '#3498db', // màu xanh dương cho đã thanh toán
                    '#e67e22', // cam cho đang giao hàng
                    '#2ecc71', // xanh lá cho giao thành công
                    '#e74c3c'  // đỏ cho đã huỷ
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [
                {
                    label: 'Doanh thu (VNĐ)',
                    data: doanhthuData,
                    fill: true,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: 'Tiền vốn (VNĐ)',
                    data: vonData,
                    fill: true,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    tension: 0.3,
                    pointRadius: 3
                },
                {
                    label: 'Lợi nhuận (VNĐ)',
                    data: loinhuanData,
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.3,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            aspectRatio: 2.5,  // tăng chiều ngang biểu đồ
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => value.toLocaleString('vi-VN') + ' đ'
                    }
                }
            }
        }
    });
    function updateSummary() {
    fetch('ajax/get_summary.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                document.getElementById('stat-sanpham').textContent = data.tongSanPham.toLocaleString('vi-VN');
                document.getElementById('stat-khachhang').textContent = data.tongKhachHang.toLocaleString('vi-VN');
                document.getElementById('stat-nhanvien').textContent = data.tongNhanVien.toLocaleString('vi-VN');
                document.getElementById('stat-nhacungcap').textContent = data.tongNhaCungCap.toLocaleString('vi-VN');
                document.getElementById('stat-tonkho').textContent = data.tongTonKho.toLocaleString('vi-VN');
            } catch (e) {
                console.error('Error parsing JSON:', e, 'Response:', text);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
}
// document.getElementById('filter-icon').addEventListener('click', function() {
//         const form = document.getElementById('filter-form');
//         if (form.style.display === 'none' || form.style.display === '') {
//             form.style.display = 'flex'; // vì form dùng class row Bootstrap nên dùng flex
//         } else {
//             form.style.display = 'none';
//         }
//     });
function resetFilter() {
    // Trở lại trang mặc định mà không có tham số GET
    window.location.href = 'index.php?page=thongke';
}
  document.addEventListener('click', function(e) {
  // Tìm tất cả các collapse đang mở
  const openCollapses = document.querySelectorAll('tr.collapse.show');
  
  openCollapses.forEach(collapse => {
    const toggleBtn = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
    if (toggleBtn && !collapse.contains(e.target) && !toggleBtn.contains(e.target)) {
      // Lấy instance collapse bootstrap và gọi hide ngay
      const bsCollapse = bootstrap.Collapse.getInstance(collapse);
      if (bsCollapse) {
        bsCollapse.hide();
      }
    }
  });
});
document.querySelector('#filterModal form').addEventListener('submit', function(e) {
  const fromDate = this.from_date.value;
  const toDate = this.to_date.value;

  if (fromDate && toDate && new Date(toDate) < new Date(fromDate)) {
    e.preventDefault();
    alert('Ngày kết thúc không được nhỏ hơn ngày bắt đầu.');
    this.to_date.focus();
    return;
  }

  // Cho phép submit nếu hợp lệ
});

let lastOpenedCollapseId = null;

document.querySelectorAll('.btn-view-order').forEach(button => {
  button.addEventListener('click', function () {
    const orderId = this.getAttribute('data-order-id');
    const contentDiv = document.getElementById('orderDetailContent');
    contentDiv.innerHTML = 'Đang tải...';

    // Lưu lại collapse đang mở (parent row có id bắt đầu bằng 'orders-')
    const parentRow = this.closest('tr').closest('tr.collapse');
    if (parentRow && parentRow.id) {
      lastOpenedCollapseId = parentRow.id;
    }

    fetch('ajax/get_order_detail_modal.php?order_id=' + orderId)
      .then(response => response.text())
      .then(html => {
        contentDiv.innerHTML = html;
      })
      .catch(error => {
        contentDiv.innerHTML = '<div class="text-danger">Lỗi tải dữ liệu</div>';
        console.error(error);
      });
  });
});
const orderModal = document.getElementById('orderDetailModal');
orderModal.addEventListener('hidden.bs.modal', function () {
  if (lastOpenedCollapseId) {
    const target = document.getElementById(lastOpenedCollapseId);
    const collapse = new bootstrap.Collapse(target, { toggle: false });
    collapse.show();
  }
});
document.addEventListener('DOMContentLoaded', function () {
  const inputLimitKhach = document.getElementById('limit_khach');

  // Submit khi nhấn Enter
  inputLimitKhach.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault(); // Ngăn form reload nếu cần
      this.form.submit();
    }
  });

  // Submit khi mất focus (blur)
  inputLimitKhach.addEventListener('blur', function () {
    this.form.submit();
  });
});
document.addEventListener('DOMContentLoaded', function () {
  const inputLimitSanPham = document.getElementById('limit_sanpham');

  if (inputLimitSanPham) {
    inputLimitSanPham.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        this.form.submit();
      }
    });

    inputLimitSanPham.addEventListener('blur', function () {
      this.form.submit();
    });
  }
});

// Cập nhật ngay khi tải trang
updateSummary();

// Cập nhật lại mỗi 10 giây (10000ms)
setInterval(updateSummary, 10000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
