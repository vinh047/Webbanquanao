<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();
$conn = $db->getConnection();

$variant_id = $_POST['variant_id'] ?? null;
$page = $_POST['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

if (!$variant_id) {
    echo json_encode(['error' => 'Thiếu variant_id']);
    exit;
}

// Lấy info
$info = $db->select("
    SELECT pv.stock, pv.image, pr.name AS ten_sp, s.name AS ten_size, c.name AS ten_mau, pv.variant_id AS id_bt_sp
    FROM product_variants pv
    JOIN products pr ON pv.product_id = pr.product_id
    JOIN sizes s ON pv.size_id = s.size_id
    JOIN colors c ON pv.color_id = c.color_id
    WHERE pv.variant_id = ?
", [$variant_id])[0];

// Lấy tổng số lần nhập
$total = $db->select("
    SELECT COUNT(*) AS total
    FROM importreceipt_details d
    WHERE d.variant_id = ?
", [$variant_id])[0]['total'];

$totalPages = ceil($total / $limit);

// Chi tiết từng lần nhập
$details = $db->select("
    SELECT d.importreceipt_details_id AS id_ctpn,
           d.importreceipt_id AS id_pn,
           d.product_id AS id_sp,
           d.variant_id AS id_bt,
           d.quantity AS so_luong,
           DATE(r.created_at) AS ngay_nhap
    FROM importreceipt_details d
    JOIN importreceipt r ON d.importreceipt_id = r.importreceipt_id
    WHERE d.variant_id = ?
    ORDER BY d.importreceipt_details_id ASC
    LIMIT $limit OFFSET $offset
", [$variant_id]);

echo json_encode([
    'info' => [
        'ten_sp' => $info['ten_sp'],
        'mau' => $info['ten_mau'],
        'size' => $info['ten_size'],
        'stock' => $info['stock'],
        'anh' => $info['image'],
        'id_bt_sp' => $info['id_bt_sp']
    ],
    'chitiet' => $details,
    'pagination' => [
        'current' => (int)$page,
        'total' => $totalPages
    ]
]);
?>