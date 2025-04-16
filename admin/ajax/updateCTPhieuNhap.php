<?php
require_once('../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idctpn = $_POST['txtMaCTPNsua'] ?? null;
    $idpn   = $_POST['txtMaPNsua'] ?? null;
    $idsp   = $_POST['txtMaSPsua'] ?? null;

    // Validate
    if (!$idctpn || !$idpn || !$idsp) {
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu cần thiết!']);
        exit;
    }

    try {
        // 🔍 Lấy số lượng từ bảng product_variants
        $stmtQty = $pdo->prepare("SELECT stock FROM product_variants 
                                  WHERE product_id = ? 
                                  ORDER BY variant_id DESC 
                                  LIMIT 1");
        $stmtQty->execute([$idsp]);
        $stock = $stmtQty->fetchColumn();
        if (!$stock) $stock = 0;

        // 🔍 Lấy đơn giá sản phẩm
        $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmtPrice->execute([$idsp]);
        $price = $stmtPrice->fetchColumn();
        if (!$price) $price = 0;

        $tongtien = $stock * $price;

        // ⚙️ Cập nhật vào chi tiết phiếu nhập
        $stmtUpdate = $pdo->prepare("UPDATE importreceipt_details 
                                     SET ImportReceipt_id = ?, product_id = ?, total_price = ? 
                                     WHERE ImportReceipt_details_id = ?");
        $stmtUpdate->execute([$idpn, $idsp, $tongtien, $idctpn]);

        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
}