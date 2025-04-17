<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

$id_ct = $_POST['id'] ?? null;

if (!$id_ct) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID chi tiết phiếu nhập']);
    exit;
}

// 1. Lấy lại chi tiết dòng sẽ xoá (cần thêm ImportReceipt_id để cập nhật lại tổng tiền)
$stmt = $pdo->prepare("SELECT importreceipt_id, variant_id, quantity FROM importreceipt_details WHERE ImportReceipt_details_id = ?");
$stmt->execute([$id_ct]);
$ct = $stmt->fetch();

if ($ct) {
    $receipt_id = $ct['importreceipt_id'];
    $variant_id = $ct['variant_id'];
    $quantity = $ct['quantity'];

    // 2. Trừ tồn kho
    $stmt2 = $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?");
    $stmt2->execute([$quantity, $variant_id]);

    // 3. Xoá chi tiết phiếu nhập
    $stmt3 = $pdo->prepare("DELETE FROM importreceipt_details WHERE ImportReceipt_details_id = ?");
    $stmt3->execute([$id_ct]);

    // 4. Kiểm tra tồn kho → nếu về 0 thì xoá mềm
    $stmt4 = $pdo->prepare("SELECT stock FROM product_variants WHERE variant_id = ?");
    $stmt4->execute([$variant_id]);
    $stock = $stmt4->fetchColumn();

    if ($stock <= 0) {
        $stmt5 = $pdo->prepare("UPDATE product_variants SET is_deleted = 1 WHERE variant_id = ?");
        $stmt5->execute([$variant_id]);
    }

    // ✅ 5. Cập nhật lại tổng tiền phiếu nhập
    $stmt6 = $pdo->prepare("
        UPDATE importreceipt 
        SET total_price = (
            SELECT IFNULL(SUM(total_price), 0)
            FROM importreceipt_details
            WHERE importreceipt_id = ?
        )
        WHERE importreceipt_id = ?
    ");
    $stmt6->execute([$receipt_id, $receipt_id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết phiếu nhập']);
}
?>