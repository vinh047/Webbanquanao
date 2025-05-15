<?php
include_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Lấy tham số lọc
$thang = isset($_GET['thang']) && $_GET['thang'] !== '' ? (int)$_GET['thang'] : '';
$nam = $_GET['nam'] ?? date('Y');
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$status = $_GET['status'] ?? 'Giao thành công'; // Mặc định lấy đơn đã giao thành công
$category_id = $_GET['category_id'] ?? '';

// Build điều kiện lọc chung (áp dụng cho đa số truy vấn)
$whereClauses = [];
$params = [];

if ($status !== '') {
    $whereClauses[] = "o.status = ?";
    $params[] = $status;
}
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
    $whereSql
    GROUP BY ngay
    ORDER BY ngay ASC
    LIMIT 31
", $params);

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

// --- Top 5 sản phẩm bán chạy ---
$topSanPham = $db->select("
    SELECT p.name, p.price_sale AS price, SUM(od.quantity) AS soluong
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN products p ON p.product_id = od.product_id
    $whereSql
    GROUP BY p.product_id
    ORDER BY soluong DESC
    LIMIT 5
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
    $whereSqlDonHangMoi
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

$topUsers = $db->select("
    SELECT u.user_id, u.name, SUM(o.total_price) AS tong_tien_mua
    FROM users u
    JOIN orders o ON u.user_id = o.user_id
    $whereOrderSql
    GROUP BY u.user_id, u.name
    ORDER BY tong_tien_mua DESC
    LIMIT 5
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

// --- Thống kê số lượng đơn theo trạng thái ---
$statusCounts = $db->select("
    SELECT status, COUNT(*) AS count
    FROM orders
    GROUP BY status
");

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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Thống kê hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* Hiệu ứng hover cho box thống kê */
        .stat-box:hover {
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            cursor: pointer;
        }
        .table tbody tr td {
            vertical-align: middle;
        }
        .row.align-items-stretch {
            align-items: stretch;  
        }
        .card.h-100 {
            height: 100%;
        }
        .col-md-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .card h5 {
        margin-bottom: 0rem;
        }
        .table thead th {
        background-color: #f8f9fa;
        }
        .card.mb-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .card.mb-0 h5 {
            margin-bottom: 0.25rem; /* hoặc 0 */
        }

        .card.mb-0 table {
            margin-top: 0;
        }
        /* Tách style ra đây thay vì inline */
        #filterButton {
        margin-bottom: 20px;
        }

    </style>
</head>
<body>
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
    <form method="GET" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Lọc sản phẩm</h5>
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
    <h5>Top 5 khách hàng mua nhiều nhất</h5>
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
                            <?php foreach ($kh['don_hang'] as $dh): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dh['order_id']) ?></td>
                                    <td><?= number_format($dh['total_price']) ?></td>
                                    <td><?= htmlspecialchars($dh['created_at']) ?></td>
                                    <td>
                                        <a href="order_detail.php?order_id=<?= htmlspecialchars($dh['order_id']) ?>" class="btn btn-sm btn-primary" target="_blank">Xem</a>
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
                                $st = $dh['status'];
                                $class = 'badge bg-secondary';
                                if ($st == 'completed') $class = 'badge bg-success';
                                elseif ($st == 'pending') $class = 'badge bg-warning text-dark';
                                elseif ($st == 'canceled') $class = 'badge bg-danger';
                                ?>
                                <span class="<?= $class ?>"><?= ucfirst($st) ?></span>
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
            <h5>Top 5 sản phẩm bán chạy</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Lượt mua</th>
                            <th>Giá (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topSanPham as $sp): ?>
                        <tr>
                            <td><?= htmlspecialchars($sp['name']) ?></td>
                            <td><?= intval($sp['soluong']) ?></td>
                            <td><?= number_format($sp['price'] ?? 0) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Biểu đồ doanh thu theo ngày
    const revenueLabels = <?= json_encode($labels) ?>;
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
    // Xóa hết giá trị trong form lọc rồi submit để load lại trang ko có filter
    const form = document.querySelector('#filterModal form');
    form.querySelectorAll('input, select').forEach(el => {
      el.value = '';
    });
    form.submit();
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
  e.preventDefault(); // ngăn submit mặc định (reload trang)

  const fromDate = this.from_date.value;
  const toDate = this.to_date.value;

  // Thêm các tham số lọc khác nếu cần
  // Gửi AJAX
  fetch(`dashboard.php?from_date=${encodeURIComponent(fromDate)}&to_date=${encodeURIComponent(toDate)}`)
    .then(res => res.text()) // hoặc res.json() nếu PHP trả JSON
    .then(html => {
      // Cập nhật vùng dữ liệu trên trang (ví dụ phần danh sách đơn hàng)
      document.querySelector('#donHangMoiContainer').innerHTML = html;
      // Có thể gọi lại hàm vẽ biểu đồ hoặc update lại số liệu ở đây nếu cần
    })
    .catch(err => console.error(err));
});

// Cập nhật ngay khi tải trang
updateSummary();

// Cập nhật lại mỗi 10 giây (10000ms)
setInterval(updateSummary, 10000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
