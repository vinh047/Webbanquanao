<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();
$pdo = $db->getConnection();

$id = $_POST['id'] ?? '';

if ($id) {
    try {
        // Kiểm tra xem phiếu có chi tiết không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM importreceipt_details WHERE importreceipt_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể xoá vì phiếu nhập đã có chi tiết!'
            ]);
            exit;
        }

        // Nếu không có chi tiết thì xoá
        $stmtDelete = $pdo->prepare("DELETE FROM importreceipt WHERE ImportReceipt_id = ?");
        $success = $stmtDelete->execute([$id]);

        echo json_encode(['success' => $success]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi máy chủ: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
}
?>