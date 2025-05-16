<?php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => true, 'total_items' => 0, 'from' => 'guest']);
    exit;
}

try {
    $db = DBConnect::getInstance()->getConnection();

    // Đếm số sản phẩm khác nhau trong giỏ hàng
    $stmt = $db->prepare("
        SELECT COUNT(*) AS total_items
        FROM cart c
        JOIN cart_details cd ON c.cart_id = cd.cart_id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $total = (int) $stmt->fetchColumn();

    echo json_encode(['success' => true, 'total_items' => $total, 'from' => 'database']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn']);
}
