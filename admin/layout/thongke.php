<?php
include_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Lấy tháng/năm từ query hoặc mặc định hiện tại
$thang = $_GET['thang'] ?? date('m');
$nam = $_GET['nam'] ?? date('Y');

// Truy vấn doanh thu
if (isset($_GET['thang']) && isset($_GET['nam'])) {
    $doanhThuTheoThang = $db->select("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS thang, SUM(total_price) AS doanhthu
        FROM orders
        WHERE status = 'completed'
        AND MONTH(created_at) = ?
        AND YEAR(created_at) = ?
        GROUP BY thang
        ORDER BY thang DESC
    ", [$thang, $nam]);
} else {
    // Nếu không chọn gì, mặc định lấy 6 tháng gần nhất
    $doanhThuTheoThang = $db->select("
        SELECT DATE_FORMAT(created_at, '%Y-%m') AS thang, SUM(total_price) AS doanhthu
        FROM orders
        WHERE status = 'completed'
        GROUP BY thang
        ORDER BY thang DESC
        LIMIT 6
    ");
}

// Top 5 sản phẩm bán chạy
$topSanPham = $db->select("
    SELECT p.name, SUM(od.quantity) AS soluong
    FROM order_details od
    JOIN products p ON p.product_id = od.product_id
    GROUP BY od.product_id
    ORDER BY soluong DESC
    LIMIT 5
");

// Thống kê số lượng tổng (chỉ giữ WHERE is_deleted ở bảng nào có cột đó)
$tongSanPham = $db->selectOne("SELECT COUNT(*) AS total FROM products")['total'];
// $tongKhachHang = $db->selectOne("SELECT COUNT(*) AS total FROM [ten_bang_dung]")['total'];
// $tongNhanVien = $db->selectOne("SELECT COUNT(*) AS total FROM users WHERE role_id = 3")['total'];
$tongNhaCungCap = $db->selectOne("SELECT COUNT(*) AS total FROM supplier")['total'];
$tongTonKho = $db->selectOne("SELECT SUM(stock) AS total FROM product_variants")['total'];
?>

<form method="GET" class="row g-3 align-items-end mb-4 container">
    <div class="col-auto">
        <label for="thang" class="form-label">Tháng</label>
        <select name="thang" id="thang" class="form-select">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= sprintf('%02d', $i) ?>" <?= $thang == sprintf('%02d', $i) ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <label for="nam" class="form-label">Năm</label>
        <input type="number" name="nam" id="nam" class="form-control" value="<?= $nam ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-dark">Lọc</button>
    </div>
</form>

<div class="container mt-4">
    <h3 class="mb-4">Tổng quan hệ thống</h3>
    <div class="row text-white">
        <div class="col-md-2 mb-3">
            <div class="p-3 bg-primary rounded">Sản phẩm: <?= $tongSanPham ?></div>
        </div>
        <!-- <div class="col-md-2 mb-3">
            <div class="p-3 bg-success rounded">Khách hàng: <?= $tongKhachHang ?></div>
        </div> -->
        <!-- <div class="col-md-2 mb-3">
            <div class="p-3 bg-info rounded">Nhân viên: <?= $tongNhanVien ?></div>
        </div> -->
        <div class="col-md-3 mb-3">
            <div class="p-3 bg-warning text-dark rounded">Nhà cung cấp: <?= $tongNhaCungCap ?></div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="p-3 bg-danger rounded">Tồn kho: <?= number_format($tongTonKho) ?></div>
        </div>
    </div>

    <h4 class="mt-5">Biểu đồ doanh thu theo tháng</h4>
    <canvas id="revenueChart" height="100"></canvas>

    <h4 class="mt-5">Top 5 sản phẩm bán chạy</h4>
    <canvas id="topProductsChart" height="100"></canvas>

    <div class="mt-4">
        <a href="export_thongke_pdf.php?thang=<?= $thang ?>&nam=<?= $nam ?>" class="btn btn-danger me-2">
            <i class="fa-solid fa-file-pdf"></i> Xuất PDF
        </a>
        <a href="export_thongke_excel.php?thang=<?= $thang ?>&nam=<?= $nam ?>" class="btn btn-success">
            <i class="fa-solid fa-file-excel"></i> Xuất Excel
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($doanhThuTheoThang, 'thang')) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode(array_map('floatval', array_column($doanhThuTheoThang, 'doanhthu'))) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: {
        responsive: true,
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

const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($topSanPham, 'name')) ?>,
        datasets: [{
            label: 'Số lượng bán',
            data: <?= json_encode(array_map('intval', array_column($topSanPham, 'soluong'))) ?>,
            backgroundColor: ['#f94144', '#f3722c', '#f9c74f', '#90be6d', '#577590']
        }]
    },
    options: {
        responsive: true
    }
});
</script>
