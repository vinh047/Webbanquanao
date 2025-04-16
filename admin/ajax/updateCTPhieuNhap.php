<?php
require_once('../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ctpn = $_POST['txtMaCTPNsua'] ?? null;
    $id_pn = $_POST['txtMaPNsua'] ?? null;
    $id_sp = $_POST['txtMaSPsua'] ?? null;
    $quantity_new = $_POST['txtSlsuaTon'] ?? null;
    $variant_id_new = $_POST['txtMaBTsua'] ?? null;

    if (!$id_ctpn || !$id_pn || !$id_sp || !$quantity_new || !$variant_id_new) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đầu vào']);
        exit;
    }

    // 1. Lấy dữ liệu cũ từ CTPN
    $stmt = $pdo->prepare("SELECT variant_id, quantity, import_price FROM importreceipt_details WHERE ImportReceipt_details_id = ?");
    $stmt->execute([$id_ctpn]);
    $old = $stmt->fetch();

    if (!$old) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết phiếu nhập']);
        exit;
    }

    $variant_id_old = $old['variant_id'];
    $quantity_old = $old['quantity'];

    // 2. Lấy giá nhập mới
    $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmtPrice->execute([$id_sp]);
    $import_price = $stmtPrice->fetchColumn();
    if (!$import_price) $import_price = 0;

    // 3. Kiểm tra variant_id mới có hợp lệ không
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM product_variants WHERE variant_id = ? AND product_id = ?");
    $stmtCheck->execute([$variant_id_new, $id_sp]);
    $isMatch = $stmtCheck->fetchColumn();
    if (!$isMatch) {
        echo json_encode(['success' => false, 'message' => 'Mã biến thể không khớp với sản phẩm!']);
        exit;
    }

    // 4. Lấy lại product_id từ variant mới
    $stmtPID = $pdo->prepare("SELECT product_id FROM product_variants WHERE variant_id = ?");
    $stmtPID->execute([$variant_id_new]);
    $product_id_new = $stmtPID->fetchColumn();

    // 5. Cập nhật tồn kho
    if ($variant_id_old != $variant_id_new) {
        $stmt1 = $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?");
        $stmt1->execute([$quantity_old, $variant_id_old]);

        $stmt2 = $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
        $stmt2->execute([$quantity_new, $variant_id_new]);
    } else {
        $delta = $quantity_new - $quantity_old;
        $stmt3 = $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
        $stmt3->execute([$delta, $variant_id_old]);
    }

    // 6. Cập nhật lại chi tiết phiếu nhập
    $total_price = $quantity_new * $import_price;
    $stmtUpdate = $pdo->prepare("
        UPDATE importreceipt_details 
        SET product_id = ?, variant_id = ?, quantity = ?, total_price = ?, import_price = ?
        WHERE ImportReceipt_details_id = ?
    ");
    $stmtUpdate->execute([$product_id_new, $variant_id_new, $quantity_new, $total_price, $import_price, $id_ctpn]);

    // 7. ✅ Cập nhật lại tổng tiền trong bảng phiếu nhập
    $stmtUpdateTotal = $pdo->prepare("
        UPDATE importreceipt 
        SET total_price = (
            SELECT SUM(total_price) 
            FROM importreceipt_details 
            WHERE importreceipt_id = ?
        ) 
        WHERE importreceipt_id = ?
    ");
    $stmtUpdateTotal->execute([$id_pn, $id_pn]);

    echo json_encode(['success' => true]);
}
?>
