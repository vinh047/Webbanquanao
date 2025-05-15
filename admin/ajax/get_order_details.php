<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu order_id']);
    exit;
}

$order_id = $_GET['order_id'];

try {
    $db = DBConnect::getInstance();

    // Lấy chi tiết đơn hàng gồm sản phẩm, biến thể, số lượng, giá
    $sql = "
        SELECT od.*,
            p.name AS product_name,
            CONCAT(s.name, ' - ', c.name) AS variant_name
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        JOIN product_variants v ON od.variant_id = v.variant_id
        JOIN sizes s ON s.size_id = v.size_id
        JOIN colors c ON c.color_id = v.color_id
        WHERE od.order_id = ?
    ";

    $orderDetails = $db->select($sql, [$order_id]);

    echo json_encode([
        'success' => true,
        'order_details' => $orderDetails
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi lấy chi tiết đơn hàng: ' . $e->getMessage()
    ]);
}
