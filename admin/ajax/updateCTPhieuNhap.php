<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['txtMaPNsua'] ?? '';
        $supplier_id = $_POST['supplier_idSuaPN'] ?? '';
        $user_id = $_POST['user_idSuaPN'] ?? '';

        $detail_ids = $_POST['detail_ids'] ?? []; // ✅ Thêm dòng này
        $product_ids = $_POST['product_ids'] ?? [];
        $variant_ids = $_POST['variant_ids'] ?? [];
        $quantities = $_POST['quantities'] ?? [];

        if (!$id || !$supplier_id || !$user_id || empty($detail_ids) || empty($product_ids) || empty($variant_ids) || empty($quantities)) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu!']);
            exit;
        }

        $pdo->beginTransaction();

        // 1. Update bảng importreceipt (bảng cha)
        $stmt = $pdo->prepare("UPDATE importreceipt SET supplier_id = ?, user_id = ? WHERE ImportReceipt_id = ?");
        $stmt->execute([$supplier_id, $user_id, $id]);

        // 2. Update bảng importreceipt_details + tồn kho
        for ($i = 0; $i < count($detail_ids); $i++) {
            $detail_id = intval($detail_ids[$i]); // ✅ Bổ sung đúng
            $product_id = intval($product_ids[$i]);
            $variant_id = intval($variant_ids[$i]);
            $quantity_new = intval($quantities[$i]);

            if ($detail_id && $variant_id && $quantity_new) {
                // Lấy quantity_old theo detail_id
                $stmtOld = $pdo->prepare("SELECT quantity FROM importreceipt_details WHERE importreceipt_details_id = ?");
                $stmtOld->execute([$detail_id]);
                $quantity_old = intval($stmtOld->fetchColumn());

                // Lấy đơn giá gốc
                $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
                $stmtPrice->execute([$product_id]);
                $unit_price = floatval($stmtPrice->fetchColumn());
                if (!$unit_price) $unit_price = 0;
                $total_price = $unit_price * $quantity_new;

                // Update tồn kho
                $stmtUpdateStock = $pdo->prepare("
                    UPDATE product_variants 
                    SET stock = stock - ? + ? 
                    WHERE variant_id = ?
                ");
                $stmtUpdateStock->execute([$quantity_old, $quantity_new, $variant_id]);

                // Update dòng chi tiết theo detail_id
                $stmtDetail = $pdo->prepare("
                    UPDATE importreceipt_details 
                    SET quantity = ?, unit_price = ?, total_price = ?
                    WHERE importreceipt_details_id = ?
                ");
                $stmtDetail->execute([$quantity_new, $unit_price, $total_price, $detail_id]);
            }
        }

        // 3. Update tổng tiền
        $stmtUpdateTotal = $pdo->prepare("
            UPDATE importreceipt 
            SET total_price = (
                SELECT SUM(total_price) 
                FROM importreceipt_details 
                WHERE importreceipt_id = ?
            )
            WHERE ImportReceipt_id = ?
        ");
        $stmtUpdateTotal->execute([$id, $id]);

        $pdo->commit();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>

