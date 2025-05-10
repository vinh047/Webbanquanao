<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

$product_id = trim($_GET['product_id'] ?? '');
$size_id = trim($_GET['size_id'] ?? '');
$color_id = trim($_GET['color_id'] ?? '');
$current_variant_id = trim($_GET['current_id'] ?? '');

if (!$product_id || !$size_id || !$color_id) {
    echo json_encode(['exists' => false]);
    exit;
}

$sql = "
    SELECT variant_id 
    FROM product_variants 
    WHERE product_id = ? AND size_id = ? AND color_id = ?
";
$params = [$product_id, $size_id, $color_id];

// Nếu đang sửa, loại trừ chính biến thể đang sửa ra
if ($current_variant_id !== '') {
    $sql .= " AND variant_id != ?";
    $params[] = $current_variant_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$variant_id = $stmt->fetchColumn();

echo json_encode(['exists' => (bool)$variant_id]);
?>