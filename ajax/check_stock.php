<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['variant_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu variant_id']);
        exit;
    }

    $db = DBConnect::getInstance();
    $variant_id = $data['variant_id'];

    $stockResult = $db->select("SELECT stock FROM product_variants WHERE variant_id = ?", [$variant_id]);

    if (!$stockResult || !isset($stockResult[0]['stock'])) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'stock' => (int) $stockResult[0]['stock']
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
