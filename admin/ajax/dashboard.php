<?php
include_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Nhận tham số lọc
$thang = isset($_GET['thang']) && $_GET['thang'] !== '' ? (int)$_GET['thang'] : '';
$nam = $_GET['nam'] ?? date('Y');
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';
$status = $_GET['status'] ?? 'Giao thành công'; // hoặc bạn có thể bỏ mặc định nếu muốn lấy tất cả
$category_id = $_GET['category_id'] ?? '';

// Build điều kiện lọc
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

// Truy vấn doanh thu theo ngày
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

// Top 5 sản phẩm bán chạy
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

// Danh sách đơn hàng mới (không lọc trạng thái)
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

// 5 khách hàng mua nhiều nhất
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

// Thống kê trạng thái đơn hàng theo lọc
$statusCounts = $db->select("
    SELECT status, COUNT(*) AS count
    FROM orders
    WHERE 1=1
    " . ($whereSql ? "AND " . implode(' AND ', $whereClauses) : '') . "
    GROUP BY status
");

// Chuẩn bị data để trả về JSON
header('Content-Type: application/json');
echo json_encode([
    'doanhThu' => $doanhThuTheoNgay,
    'statusCounts' => $statusCounts,
    'topKhachHang' => array_values($topKhachHangGroup), // reset keys
    'donHangMoi' => $donHangMoi,
    'topSanPham' => $topSanPham,
]);
exit;
?>
 