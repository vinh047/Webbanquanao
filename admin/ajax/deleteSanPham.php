<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $db = DBConnect::getInstance();

    try {
        $qry = "DELETE FROM products WHERE product_id = ?";
        $stmt = $db->getConnection()->prepare($qry);
        $stmt->execute([$id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
}
?>