<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['idctpn'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu idctpn!']);
            exit;
        }

        $idctpn = intval($_POST['idctpn']);

        $pdo->beginTransaction(); // 🔥 Bắt đầu Transaction

        // 1. Lấy chi tiết dòng sẽ xoá
        $stmt = $pdo->prepare("SELECT importreceipt_id, variant_id, quantity FROM importreceipt_details WHERE importreceipt_details_id = ?");
        $stmt->execute([$idctpn]);
        $ct = $stmt->fetch();

        if (!$ct) {
            throw new Exception("Không tìm thấy chi tiết phiếu nhập");
        }

        $receipt_id = $ct['importreceipt_id'];
        $variant_id = $ct['variant_id'];
        $quantity = $ct['quantity'];

        // 2. Trừ tồn kho
        $stmt2 = $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?");
        $stmt2->execute([$quantity, $variant_id]);

        // 3. Xoá chi tiết phiếu nhập
        $stmt3 = $pdo->prepare("DELETE FROM importreceipt_details WHERE importreceipt_details_id = ?");
        $stmt3->execute([$idctpn]);

        // 4. Cập nhật lại tổng tiền của phiếu nhập
        $stmt6 = $pdo->prepare("
            UPDATE importreceipt
            SET total_price = (
                SELECT COALESCE(SUM(total_price), 0)
                FROM importreceipt_details
                WHERE importreceipt_id = ?
            )
            WHERE importreceipt_id = ?
        ");
        $stmt6->execute([$receipt_id, $receipt_id]);

        $pdo->commit(); // ✅ OK, hoàn tất

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // ❌ Có lỗi rollback lại
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
