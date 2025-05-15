<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$variant_id = $input['variant_id'] ?? null;
$quantity = $input['quantity'] ?? 0;

if (!$variant_id || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$db = DBConnect::getInstance();
$variant = $db->selectOne("SELECT stock FROM product_variants WHERE variant_id = ?", [$variant_id]);

if (!$variant) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể']);
    exit;
}

if ($variant['stock'] >= $quantity) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $variant['stock']]);
}
