<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');
ob_start(); // ⚡

$db = DBConnect::getInstance();

$id = $_GET['id'] ?? null;

if (!$id) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
    exit;
}

$data = $db->select("
SELECT 
    d.importreceipt_details_id,
    d.importreceipt_id,
    p.name AS product_name,
    v.image,
    c.name AS color_name,
    s.name AS size_name,
    d.quantity,
    d.created_at
FROM importreceipt_details d
JOIN product_variants v ON d.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id   -- ✅ sửa ở đây
JOIN colors c ON v.color_id = c.color_id
JOIN sizes s ON v.size_id = s.size_id
WHERE d.importreceipt_details_id = ?

", [$id]);

ob_end_clean(); // ⚡

if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết']);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $data[0]
]);
?>
