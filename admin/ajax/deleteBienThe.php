<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();
$variant_id = $_POST['variant_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID biến thể']);
    exit;
}

try {
    // Kiểm tra nếu biến thể đã từng có trong đơn hàng
    $stmt1 = $pdo->prepare("SELECT COUNT(*) FROM order_details WHERE variant_id = ?");
    $stmt1->execute([$variant_id]);
    $count_orders = $stmt1->fetchColumn();

if ($count_orders > 0) {
    // Đã từng bán → chỉ ẩn cả biến thể và chi tiết phiếu nhập
    $stmtHideVariant = $pdo->prepare("UPDATE product_variants SET is_deleted = 1 WHERE variant_id = ?");
    $stmtHideVariant->execute([$variant_id]);

    $stmtHideDetails = $pdo->prepare("UPDATE importreceipt_details SET is_deleted = 1 WHERE variant_id = ?");
    $stmtHideDetails->execute([$variant_id]);

    echo json_encode([
        'success' => true,
        'action' => 'hidden',
        'message' => 'Biến thể đã được sử dụng trong đơn hàng → đã ẩn cả biến thể và chi tiết phiếu nhập.'
    ]);
}
 else {
        // Chưa từng bán → xoá phiếu nhập chi tiết trước
        $pdo->beginTransaction();

        // 1. Lấy danh sách các phiếu nhập bị ảnh hưởng
        $stmtGetReceipts = $pdo->prepare("
            SELECT DISTINCT importreceipt_id 
            FROM importreceipt_details 
            WHERE variant_id = ?
        ");
        $stmtGetReceipts->execute([$variant_id]);
        $receiptIds = $stmtGetReceipts->fetchAll(PDO::FETCH_COLUMN);

        // 2. Xoá chi tiết phiếu nhập liên quan
        $stmtDeleteDetails = $pdo->prepare("DELETE FROM importreceipt_details WHERE variant_id = ?");
        $stmtDeleteDetails->execute([$variant_id]);

        // 3. Xoá chính biến thể
        $stmtDeleteVariant = $pdo->prepare("DELETE FROM product_variants WHERE variant_id = ?");
        $stmtDeleteVariant->execute([$variant_id]);

        // 4. Cập nhật lại tổng giá trị cho các phiếu nhập liên quan
        $stmtUpdateTotal = $pdo->prepare("
            UPDATE importreceipt 
            SET total_price = (
                SELECT COALESCE(SUM(quantity * unit_price), 0)
                FROM importreceipt_details
                WHERE importreceipt_id = ?
            )
            WHERE ImportReceipt_id = ?
        ");
        foreach ($receiptIds as $receiptId) {
            $stmtUpdateTotal->execute([$receiptId, $receiptId]);
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'action' => 'deleted',
            'message' => 'Biến thể không có đơn hàng → đã xoá cùng các chi tiết phiếu nhập và cập nhật lại giá trị.'
        ]);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>