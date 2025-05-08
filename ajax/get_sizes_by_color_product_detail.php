<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$color_id = $data['color_id'] ?? null;
$product_id = $data['product_id'] ?? null;

if (!$color_id || !$product_id) {
    echo json_encode([]);
    exit;
}

// Truy vấn lấy size theo color
$sizes = $db->select("
    SELECT DISTINCT s.size_id, s.name AS size_name
    FROM product_variants pv
    JOIN sizes s ON s.size_id = pv.size_id
    WHERE pv.product_id = ? AND pv.color_id = ? AND pv.stock > 0 AND pv.is_deleted = 0
    GROUP BY s.size_id ASC
", [$product_id, $color_id]);

echo json_encode($sizes); 