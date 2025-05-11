<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$product_id = $_POST['product_id'] ?? '';
$size_id = $_POSTơ['size_id'] ?? '';
$color_id = $_POST['color_id'] ?? '';

$variant = $db->selectOne('SELECT pv.*
        FROM product_variants pv
        WHERE pv.product_id = ? AND pv.color_id = ? AND s.size_id = ? AND pv.is_deleted = 0
        LIMIT 1', [$product_id, $color_id, $size_id]);

echo json_encode(['success' => $variant, 'message' => $variant ? $variant : 'Lấy thông tin snả phẩm thất bại!']);
