<?php
require_once('../../database/DBConnection.php');
$db = DBConnect::getInstance();
$conn = $db->getConnection();

// Cập nhật các phiếu nhập nếu tất cả chi tiết của chúng đã bị xóa
$sql = "
    UPDATE importreceipt im
    SET im.is_deleted = 1
    WHERE im.is_deleted = 0
    AND NOT EXISTS (
        SELECT 1
        FROM importreceipt_details d
        WHERE d.importreceipt_id = im.ImportReceipt_id
          AND d.is_deleted = 0
    )
";

$stmt = $conn->prepare($sql);
$stmt->execute();

echo json_encode(['success' => true]);
?>