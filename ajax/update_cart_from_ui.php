<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/json; charset=utf-8');

$db = DBConnect::getInstance();

// Đọc JSON input
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'] ?? '';
$cartItems = $data['cart'] ?? [];

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu cần thiết']);
    exit;
}

try {
    // 1. Tìm cart_id của user
    $cart = $db->select("SELECT cart_id FROM cart WHERE user_id = ?", [$user_id]);
    if (empty($cart)) {
        // Chưa có → tạo mới
        $db->execute("INSERT INTO cart (user_id) VALUES (?)", [$user_id]);
        $cart_id = $db->lastInsertId();
    } else {
        $cart_id = $cart[0]['cart_id'];
    }

    // 2. Xoá tất cả cart_details cũ của user (có thể thay bằng update nếu muốn giữ lại)
    $db->execute("DELETE FROM cart_details WHERE cart_id = ?", [$cart_id]);

    // 3. Thêm lại các item mới
    foreach ($cartItems as $item) {
        $product_id = $item['product_id'];
        $variant_id = $item['variant_id'];
        $quantity = $item['quantity'];

        if (!$product_id || !$variant_id || $quantity <= 0) continue;

        $db->execute("INSERT INTO cart_details (cart_id, product_id, variant_id, quantity) VALUES (?, ?, ?, ?)", [
            $cart_id, $product_id, $variant_id, $quantity
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Đã cập nhật giỏ hàng']);
} catch (Exception $e) {
    error_log("Lỗi khi cập nhật cart: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống']);
}