<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    $variant_id = $input['variant_id'] ?? null;

    if (!$variant_id) {
        throw new Exception('Thiếu variant_id');
    }

    $db = DBConnect::getInstance();
    $row = $db->selectOne("SELECT stock FROM product_variants WHERE variant_id = ?", [$variant_id]);

    if (!$row) {
        throw new Exception('Không tìm thấy sản phẩm');
    }

    echo json_encode(['success' => true, 'stock' => (int) $row['stock']]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
