<?php
        require_once(__DIR__ . '/../../database/DBConnection.php');
        header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

$product_id = $_GET['product_id'] ?? null;
$variant_id = $_GET['variant_id'] ?? null;

if (!$product_id || !$variant_id) {
    echo json_encode(['match' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = ? AND variant_id = ?");
$stmt->execute([$product_id, $variant_id]);
$exists = $stmt->fetchColumn();

echo json_encode(['match' => $exists > 0]);
?>