<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ctpn = $_POST['txtMaCTPNsua'] ?? null;
    $id_pn = $_POST['txtMaPNsua'] ?? null;
    $id_sp = $_POST['txtMaSPsua'] ?? null;
    $quantity_new = intval($_POST['txtSlsuaTon'] ?? 0);
    $variant_id_new = $_POST['txtMaBTsua'] ?? null;

    if (!$id_ctpn || !$id_pn || !$id_sp || !$quantity_new || !$variant_id_new) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đầu vào']);
        exit;
    }

    // 1. Lấy dữ liệu cũ từ CTPN
    $stmt = $pdo->prepare("SELECT variant_id, quantity, importreceipt_id FROM importreceipt_details WHERE importreceipt_details_id = ?");
    $stmt->execute([$id_ctpn]);
    $old = $stmt->fetch();

    if (!$old) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy chi tiết phiếu nhập']);
        exit;
    }

    $variant_id_old = $old['variant_id'];
    $quantity_old = intval($old['quantity']);
    $old_pn_id = $old['importreceipt_id'];

    // 2. Kiểm tra biến thể có đúng thuộc sản phẩm không
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM product_variants WHERE variant_id = ? AND product_id = ?");
    $stmtCheck->execute([$variant_id_new, $id_sp]);
    $isMatch = $stmtCheck->fetchColumn();
    if (!$isMatch) {
        echo json_encode(['success' => false, 'message' => 'Mã biến thể không khớp với sản phẩm!']);
        exit;
    }

    // 3. Lấy lại giá sản phẩm mới
    $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmtPrice->execute([$id_sp]);
    $product_price = $stmtPrice->fetchColumn();
    if (!$product_price) $product_price = 0;

    // 4. Cập nhật tồn kho
    if ($variant_id_old != $variant_id_new) {
        $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE variant_id = ?")
            ->execute([$quantity_old, $variant_id_old]);

        $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
            ->execute([$quantity_new, $variant_id_new]);
    } else {
        $delta = $quantity_new - $quantity_old;
        $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?")
            ->execute([$delta, $variant_id_old]);
    }

    // 5. Cập nhật lại chi tiết phiếu nhập
    $stmtUpdate = $pdo->prepare("
        UPDATE importreceipt_details 
        SET importreceipt_id = ?, product_id = ?, variant_id = ?, quantity = ?
        WHERE importreceipt_details_id = ?
    ");
    $stmtUpdate->execute([
        $id_pn,
        $id_sp,
        $variant_id_new,
        $quantity_new,
        $id_ctpn
    ]);

    // 6. Cập nhật lại tổng tiền phiếu nhập mới
    $stmtUpdateTotal = $pdo->prepare("
        UPDATE importreceipt 
        SET total_price = (
            SELECT SUM(d.quantity * p.price)
            FROM importreceipt_details d
            JOIN products p ON d.product_id = p.product_id
            WHERE d.importreceipt_id = ?
        )
        WHERE importreceipt_id = ?
    ");
    $stmtUpdateTotal->execute([$id_pn, $id_pn]);

    // 7. Nếu mã phiếu nhập mới khác mã cũ, cập nhật lại phiếu nhập cũ
    if ($id_pn != $old_pn_id) {
        $stmtUpdateTotalOld = $pdo->prepare("
            UPDATE importreceipt 
            SET total_price = (
                SELECT SUM(d.quantity * p.price)
                FROM importreceipt_details d
                JOIN products p ON d.product_id = p.product_id
                WHERE d.importreceipt_id = ?
            )
            WHERE importreceipt_id = ?
        ");
        $stmtUpdateTotalOld->execute([$old_pn_id, $old_pn_id]);
    }

    echo json_encode(['success' => true]);
}
?>
