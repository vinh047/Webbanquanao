<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$conn = DBConnect::getInstance()->getConnection();
$product_id = $_GET['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(['unit_price' => null]);
    exit;
}

$stmt = $conn->prepare("SELECT unit_price FROM importreceipt_details WHERE product_id   = ?");
$stmt->execute([$product_id]);
$price = $stmt->fetchColumn();

echo json_encode(['unit_price' => $price]);
?>