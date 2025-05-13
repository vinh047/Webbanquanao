<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = (int)$_POST['id'];
    $db = DBConnect::getInstance();
    $conn = $db->getConnection();

    try {
        // Kiểm tra có biến thể hay không
        $variantStmt = $conn->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = ?");
        $variantStmt->execute([$productId]);
        $variantCount = (int)$variantStmt->fetchColumn();

        // Kiểm tra đã từng có trong hóa đơn (order_details)
        $orderStmt = $conn->prepare("
            SELECT COUNT(*) FROM order_details
            WHERE product_id = ? AND variant_id IN (
                SELECT variant_id FROM product_variants WHERE product_id = ?
            )
        ");
        $orderStmt->execute([$productId, $productId]);
        $orderCount = (int)$orderStmt->fetchColumn();

        // Kiểm tra đã từng có trong phiếu nhập (importreceipt_details)
        $importStmt = $conn->prepare("
            SELECT COUNT(*) FROM importreceipt_details
            WHERE product_id = ?
        ");
        $importStmt->execute([$productId]);
        $importCount = (int)$importStmt->fetchColumn();

        // Nếu có biến thể hoặc từng xuất hiện trong đơn hàng hoặc phiếu nhập
        if ($variantCount > 0 || $orderCount > 0 || $importCount > 0) {
            $conn->beginTransaction();

            // Cập nhật bảng products
            $conn->prepare("UPDATE products SET is_deleted = 1 WHERE product_id = ?")
                ->execute([$productId]);

            // Cập nhật bảng product_variants
            $conn->prepare("UPDATE product_variants SET is_deleted = 1 WHERE product_id = ?")
                ->execute([$productId]);

            // Cập nhật bảng importreceipt_details
            $conn->prepare("UPDATE importreceipt_details SET is_deleted = 1 WHERE product_id = ?")
                ->execute([$productId]);

            $conn->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Sản phẩm có liên kết dữ liệu đã được ẩn (is_deleted = 1).'
            ]);
        } else {
            // Nếu hoàn toàn chưa dùng → xóa thật
            $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$productId]);

            echo json_encode([
                'success' => true,
                'message' => 'Sản phẩm không có dữ liệu liên kết và đã bị xóa vĩnh viễn.'
            ]);
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
}
