<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$db = DBConnect::getInstance();

if (!isset($_GET['idpn'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã phiếu nhập!']);
    exit;
}

$idpn = intval($_GET['idpn']);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 6;
$offset = ($page - 1) * $perPage;

try {
    $phieu = $db->selectOne("SELECT * FROM importreceipt WHERE importreceipt_id = ?", [$idpn]);
    if (!$phieu) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy phiếu nhập!']);
        exit;
    }

    $totalResult = $db->selectOne("SELECT COUNT(*) AS total FROM importreceipt_details WHERE importreceipt_id = ?", [$idpn]);
    $totalDetails = intval($totalResult['total'] ?? 0);
    

    $ctpn = $db->select(
        "SELECT 
            d.importreceipt_details_id, 
            d.product_id, 
            p.name AS product_name,
            d.variant_id, 
            d.quantity
         FROM importreceipt_details d
         JOIN products p ON d.product_id = p.product_id
         WHERE d.importreceipt_id = ?
         LIMIT $perPage OFFSET $offset",
        [$idpn]
    );
    
    

    echo json_encode([
        'success' => true,
        'phieunhap' => $phieu,
        'details' => $ctpn,
        'pagination' => [
            'current' => $page,
            'total' => ceil($totalDetails / $perPage)
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>
