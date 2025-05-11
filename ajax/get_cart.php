<?php
session_start();
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Trả về mảng rỗng nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Chưa đăng nhập',
        'data' => []
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "
    SELECT 
        d.product_id AS id,
        d.variant_id,
        d.quantity,
        p.name,
        v.color_id,
        s.name AS size,
        p.price,
        v.image 
    FROM cart_details d
    JOIN cart c ON d.cart_id = c.cart_id
    JOIN products p ON d.product_id = p.product_id
    JOIN product_variants v ON d.variant_id = v.variant_id
    JOIN colors cl ON v.color_id = cl.color_id
    JOIN sizes s ON v.size_id = s.size_id
    WHERE c.user_id = ?
";




    $items = $db->select($sql, [$user_id]);

    foreach ($items as &$item) {
        $item['quantity'] = (int)$item['quantity'];
        $item['price'] = (float)$item['price'];
    }

    echo json_encode([
        'success' => true,
        'data' => $items
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi truy vấn: ' . $e->getMessage(),
        'data' => []
    ]);
}
