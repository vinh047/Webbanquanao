<?php
require_once '../../database/DBConnection.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$order_id = $input['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu order_id']);
    exit;
}

try {
    $db = DBConnect::getInstance();
    $pdo = $db->getConnection();

    $pdo->beginTransaction();

    // Lấy chi tiết đơn hàng (variant_id, quantity)
    $orderDetails = $db->select("SELECT variant_id, quantity FROM order_details WHERE order_id = ?", [$order_id]);

    // Cập nhật lại tồn kho product_variants (cộng lại số lượng)
    $updateStockSql = "UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?";
    $updateStockStmt = $pdo->prepare($updateStockSql);
    foreach ($orderDetails as $detail) {
        $updateStockStmt->execute([$detail['quantity'], $detail['variant_id']]);
    }

    // Cập nhật trạng thái đơn hàng thành 'Đã huỷ'
    $db->execute("UPDATE orders SET status = 'Đã huỷ' WHERE order_id = ?", [$order_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái và tồn kho thành công']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật đơn hàng: ' . $e->getMessage()]);
}
