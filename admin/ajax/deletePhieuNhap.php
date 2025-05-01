<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();
$pdo = $db->getConnection();

$id = $_POST['id'] ?? '';

if ($id) {
    try {
        $pdo->beginTransaction();

        // Lấy toàn bộ chi tiết để cập nhật tồn kho
        $stmtDetails = $pdo->prepare("SELECT variant_id, quantity FROM importreceipt_details WHERE importreceipt_id = ?");
        $stmtDetails->execute([$id]);
        $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        // Cập nhật tồn kho: trừ đi số lượng nhập trước đó
        foreach ($details as $row) {
            $variantId = $row['variant_id'];
            $quantity = $row['quantity'];

            $stmtUpdateStock = $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?");
            $stmtUpdateStock->execute([$quantity, $variantId]);
        }

        // Xoá chi tiết phiếu nhập
        $stmtDeleteDetails = $pdo->prepare("DELETE FROM importreceipt_details WHERE importreceipt_id = ?");
        $stmtDeleteDetails->execute([$id]);

        // Xoá phiếu nhập
        $stmtDeleteReceipt = $pdo->prepare("DELETE FROM importreceipt WHERE importreceipt_id = ?");
        $stmtDeleteReceipt->execute([$id]);

        $pdo->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi máy chủ: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
}
?>
