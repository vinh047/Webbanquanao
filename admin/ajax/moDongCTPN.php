<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');
ob_start();

$db = DBConnect::getInstance();

$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !in_array($status, ['0', '1'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}
ob_end_clean();

// Cập nhật status của chi tiết phiếu nhập
try {
    $stmt = $db->getConnection()->prepare("UPDATE importreceipt_details SET status = ? WHERE importreceipt_details_id = ?");
    $success = $stmt->execute([$status, $id]);

    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Đã cập nhật trạng thái' : 'Không có dòng nào được cập nhật'
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
?>