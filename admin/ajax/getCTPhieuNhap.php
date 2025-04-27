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
        "SELECT importreceipt_details_id, product_id, variant_id, quantity
         FROM importreceipt_details
         WHERE importreceipt_id = ?
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
