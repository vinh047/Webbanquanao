<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();
$conn = $db->getConnection();

$variant_id = $_GET['variant_id'] ?? null;
$product_id = $_GET['product_id'] ?? null;

if (!$variant_id || !$product_id) {
    echo json_encode(['error' => 'Thiếu dữ liệu']);
    exit;
}

// Lấy thông tin biến thể
$stmt = $conn->prepare("SELECT color_id, size_id FROM product_variants WHERE variant_id = ? AND product_id = ?");
$stmt->execute([$variant_id, $product_id]);
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$variant) {
    echo json_encode(['error' => 'Không tìm thấy biến thể']);
    exit;
}

echo json_encode([
    'color_id' => (int)$variant['color_id'],
    'size_id' => (int)$variant['size_id']
]);
?>