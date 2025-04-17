<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();

$id = $_POST['id'] ?? '';

if ($id) {
    // Có thể kiểm tra tồn tại nếu muốn an toàn hơn
    $sql = "DELETE FROM importreceipt WHERE ImportReceipt_id = ?";
    $result = $db->execute($sql, [$id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể xóa phiếu nhập']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
}
?>