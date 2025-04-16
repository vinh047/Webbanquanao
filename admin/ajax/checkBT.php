<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

$product_id = $_GET['product_id'] ?? '';
$size_id = $_GET['size_id'] ?? '';
$color_id = $_GET['color_id'] ?? '';
$image = $_GET['image'] ?? '';

if (!$product_id || !$size_id || !$color_id || !$image) {
    echo json_encode(['exists' => false]);
    exit;
}

// checkBT.php
$stmt = $pdo->prepare("
    SELECT variant_id 
    FROM product_variants 
    WHERE product_id = ? AND size_id = ? AND color_id = ? AND image = ?
");
$stmt->execute([$product_id, $size_id, $color_id, $image]);


$variant_id = $stmt->fetchColumn();

if ($variant_id) {
    echo json_encode(['exists' => true, 'variant_id' => $variant_id]);
} else {
    echo json_encode(['exists' => false]);
}
?>