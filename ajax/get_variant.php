<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();
$data = json_decode(file_get_contents("php://input"), true);

$product_id = $data['product_id'] ?? '';
$size_id = $data['size_id'] ?? '';
$color_id = $data['color_id'] ?? '';

$variant = $db->select(
        'SELECT pv.*, p.name as product_name, p.price
        FROM product_variants pv
        JOIN products p ON p.product_id = pv.product_id
        WHERE pv.product_id = ? AND pv.color_id = ? AND pv.size_id = ? AND pv.is_deleted = 0',
        [$product_id, $color_id, $size_id]
);
// var_dump($variant); exit;
$response = [
        'success' => !empty($variant),
        'data' => $variant,
        'message' => !empty($variant) ? 'Lấy thông tin sản phẩm thành công!' : 'Lấy thông tin sản phẩm thất bại!'
];

header('Content-Type: application/json');
echo json_encode($response);
