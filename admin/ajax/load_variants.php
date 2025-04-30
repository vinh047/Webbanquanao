<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();

$product_id = $_GET['product_id'] ?? '';
$stmt = $pdo->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND is_deleted = 0");
$stmt->execute([$product_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
