<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

$data = json_decode(file_get_contents("php://input"), true);
$cart = $data['cart']; // ✅ Giỏ hàng đầy đủ (có variant_id)
// Kiểm tra dữ liệu hợp lệ
if (!$data || !isset($data['cart']) || !is_array($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    $fullname = $data['ho'] . ' ' . $data['ten'];
    $phone = $data['sdt'];
    $email = $data['email'];
    $address = $data['province'] . ', ' . $data['district'] . ', ' . $data['ward'] . ', ' . $data['address'];
    $note = "Họ tên: $fullname | SĐT: $phone | Email: $email";
    $created_at = date('Y-m-d H:i:s');

    // Lưu đơn hàng
    $sql = "INSERT INTO orders (user_id, status, total_price, shipping_address, note, created_at, payment_method_id, staff_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        null,           // user_id (khách vãng lai)
        0,              // status = mới
        $data['total'],
        $address,
        $note,
        $created_at,
        $data['payment_method'],
        null            // staff_id
    ];
    $db->execute($sql, $params);
    $order_id = $db->lastInsertId();
    // Lưu chi tiết từng sản phẩm
    foreach ($data['cart'] as $item) {
        $total_price = $item['price'] * $item['quantity'];
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, total_price, variant_id)
        VALUES (?, ?, ?, ?, ?)";
        $db->execute($sql, [
            $order_id,
            $item['id'],
            $item['quantity'],
            $item['price'] * $item['quantity'], // total_price
            $item['variant_id'] ?? null
        ]);

    }    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
