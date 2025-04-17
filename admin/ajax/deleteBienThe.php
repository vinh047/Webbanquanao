<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();
$variant_id = $_POST['variant_id'] ?? null;

if (!$variant_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID biến thể']);
    exit;
}

// Kiểm tra trong order_details
$stmt1 = $pdo->prepare("SELECT COUNT(*) FROM order_details WHERE variant_id = ?");
$stmt1->execute([$variant_id]);
$count_orders = $stmt1->fetchColumn();

// Kiểm tra trong importreceipt_details
$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM importreceipt_details WHERE variant_id = ?");
$stmt2->execute([$variant_id]);
$count_imports = $stmt2->fetchColumn();

if ($count_orders > 0 || $count_imports > 0) {
    // Có liên kết → chỉ ẩn
    $stmtHide = $pdo->prepare("UPDATE product_variants SET is_deleted = 1 WHERE variant_id = ?");
    $stmtHide->execute([$variant_id]);
    echo json_encode([
        'success' => true,
        'action' => 'hidden',
        'message' => 'Biến thể đang được sử dụng → đã ẩn thay vì xoá.'
    ]);
} else {
    // Không có liên kết → xoá thật
    $stmtDelete = $pdo->prepare("DELETE FROM product_variants WHERE variant_id = ?");
    $stmtDelete->execute([$variant_id]);
    echo json_encode([
        'success' => true,
        'action' => 'deleted',
        'message' => 'Biến thể không còn liên kết → đã xoá khỏi hệ thống.'
    ]);
}

?>