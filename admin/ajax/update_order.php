<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (
    !$input ||
    empty($input['order_id']) ||
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

    $orderId = $input['order_id'];

    // Bắt đầu transaction
    $pdo->beginTransaction();

    // 1. Lấy các chi tiết đơn hàng cũ để cập nhật lại tồn kho
    $oldDetails = $db->select("SELECT variant_id, quantity FROM order_details WHERE order_id = ?", [$orderId]);

    // Cộng tồn kho lại cho các variant_id trong đơn hàng cũ (đảo ngược lượng đã trừ trước đó)
    foreach ($oldDetails as $old) {
        $variantId = $old['variant_id'];
        $qty = (int)$old['quantity'];
        $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
            ->execute([$qty, $variantId]);
    }

    // 2. Xóa hết chi tiết đơn hàng cũ
    $db->execute("DELETE FROM order_details WHERE order_id = ?", [$orderId]);

    // 3. Cập nhật đơn hàng (bảng orders)
    $orderData = [
        'user_id'           => $input['user_id'],
        'staff_id'          => $input['staff_id'],
        'status'            => $input['status'] ?? 'Chờ xác nhận',
        'total_price'       => $input['total_price'] ?? 0,
        'note'              => $input['note'] ?? '',
        'shipping_address'  => $input['shipping_address'] ?? '',
        'payment_method_id' => $input['payment_method_id'] ?? null,
    ];

    $setPart = [];
    $values = [];
    foreach ($orderData as $key => $value) {
        $setPart[] = "$key = ?";
        $values[] = $value;
    }
    $values[] = $orderId;

    $sqlUpdateOrder = "UPDATE orders SET " . implode(', ', $setPart) . " WHERE order_id = ?";
    $stmt = $pdo->prepare($sqlUpdateOrder);
    $stmt->execute($values);

    // 4. Thêm chi tiết đơn hàng mới và trừ tồn kho tương ứng
    foreach ($input['order_details'] as $item) {
        $detailData = [
            'order_id'   => $orderId,
            'product_id' => $item['product_id'],
            'variant_id' => $item['variant_id'],
            'quantity'   => $item['quantity'],
            'price'      => $item['price'],
            'total_price' => $item['total_price'],
        ];

        $db->insert('order_details', $detailData);

        // Trừ tồn kho
        $qty = (int)$item['quantity'];
        $variantId = $item['variant_id'];
        $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?")
            ->execute([$qty, $variantId]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật đơn hàng thành công.'
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật đơn hàng: ' . $e->getMessage()
    ]);
}
