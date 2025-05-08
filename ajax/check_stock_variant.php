<?php
session_start();
require_once '../database/DBConnection.php';

header('Content-Type: application/json');

$db = DBConnect::getInstance();
$conn = $db->getConnection();

// Bắt dữ liệu JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$product_id = $data['product_id'] ?? null;
$color_id = $data['color_id'] ?? null;
$size_id = $data['size_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

// Lấy user_id từ session
$user_id = $_SESSION['user_id'] ?? null;

$variant_id = $data['variant_id'] ?? null;

if ($variant_id == null) {
    // 2. Tìm variant_id dựa trên product_id, color_id, size_id
    $stmt = $conn->prepare("
        SELECT pv.variant_id
        FROM product_variants pv
        JOIN sizes s ON s.size_id = pv.size_id
        WHERE pv.product_id = ? AND pv.color_id = ? AND s.size_id = ? AND pv.is_deleted = 0
        LIMIT 1
    ");
    $stmt->execute([$product_id, $color_id, $size_id]);
    $variant = $stmt->fetch();

    if (!$variant) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy biến thể sản phẩm.']);
        exit;
    }

    $variant_id = $variant['variant_id'];
}

$variant = $db->selectOne('SELECT * FROM product_variants WHERE variant_id = ?', [$variant_id]);
// san pham khong du
if ($variant['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Không đủ sản phẩm chỉ còn ' . $variant['stock']]);
    exit;
}
// snar pham du so luong
echo json_encode(['success' => true]);
