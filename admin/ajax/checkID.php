<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if (isset($_GET['product_id'])) {
    $id = (int)$_GET['product_id'];

    $pdo = DBConnect::getInstance()->getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    echo json_encode(['exists' => $row['total'] > 0]);
} else {
    echo json_encode(['exists' => false]);
}
?>
