<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

// Lấy dữ liệu từ frontend
$input = json_decode(file_get_contents('php://input'), true);

// Kiểm tra dữ liệu
if (
    !$input ||
    empty($input['user_id']) ||
    empty($input['staff_id']) ||
    empty($input['order_details']) ||
    !is_array($input['order_details'])
) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

try {
    $db = DBConnect::getInstance();
    $pdo = $db->getConnection();

    // Bắt đầu transaction
    $pdo->beginTransaction();

    // 1. Thêm đơn hàng vào bảng `orders`
    $orderData = [
        'user_id'           => $input['user_id'],
        'staff_id'          => $input['staff_id'],
        'status'            => $input['status'] ?? 'Chờ xác nhận',
        'total_price'       => $input['total_price'] ?? 0,
        'note'              => $input['note'] ?? '',
        'shipping_address'  => $input['shipping_address'] ?? '',
        'payment_method_id' => $input['payment_method_id'] ?? null,
        'created_at'        => date('Y-m-d H:i:s')
    ];

    $db->insert('orders', $orderData);
    $orderId = $db->lastInsertId();

    // 2. Thêm các dòng vào bảng `order_details`
    foreach ($input['order_details'] as $item) {
        $detailData = [
            'order_id'    => $orderId,
            'product_id'  => $item['product_id'],
            'variant_id'  => $item['variant_id'],
            'quantity'    => $item['quantity'],
            'price'       => $item['price'],
            'total_price' => $item['total_price'],
        ];
        $db->insert('order_details', $detailData);

        // Trừ tồn kho của variant
        $db->execute(
            "UPDATE product_variants SET stock = stock - ? WHERE variant_id = ? AND is_deleted = 0",
            [$item['quantity'], $item['variant_id']]
        );
    }

    // Commit dữ liệu
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Thêm đơn hàng thành công.',
        'order_id' => $orderId
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi thêm đơn hàng: ' . $e->getMessage()
    ]);
}
