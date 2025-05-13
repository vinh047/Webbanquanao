<?php
session_start();
require_once '../../database/DBConnection.php'; // ✅ Sửa đường dẫn đúng nếu file nằm trong /ajax
$db = DBConnect::getInstance();
header('Content-Type: application/json');

// Lấy và decode JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Kiểm tra dữ liệu đầu vào
if (!$data || empty($data['cart']) || empty($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu đơn hàng không hợp lệ']);
    exit;
}

try {
    $db->beginTransaction();

    $user_id = $_SESSION['user_id'] ?? null;
    $address = $data['address'] ?? [];

    // Lưu vào bảng orders
    $db->insert('orders', [
        'user_id' => $user_id,
        'name' => $data['name'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'payment_method' => $data['payment_method'],
        'total_price' => $data['total_price'],
        'discount' => $data['discount'],
        'shipping_fee' => $data['shipping_fee'],
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $order_id = $db->lastInsertId();

    // Lưu địa chỉ nếu là địa chỉ mới
    if (empty($address['saved_id']) && !empty($address['province'])) {
        $db->insert('user_addresses', [
            'user_id' => $user_id,
            'address_detail' => $address['detail'] ?? '',
            'ward' => $address['ward'] ?? '',
            'district' => $address['district'] ?? '',
            'province' => $address['province'] ?? '',
            'is_default' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Lưu chi tiết đơn hàng
    foreach ($data['cart'] as $item) {
        if (!isset($item['product_id'], $item['variant_id'], $item['price'], $item['quantity'])) continue;

        $db->insert('order_details', [
            'order_id' => $order_id,
            'product_id' => $item['product_id'],
            'variant_id' => $item['variant_id'],
            'price' => $item['price'],
            'quantity' => $item['quantity']
        ]);
    }

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
