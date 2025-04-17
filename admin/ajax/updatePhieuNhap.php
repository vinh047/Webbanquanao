<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();

$id = $_POST['txtMaPNsua'] ?? '';
$supplier_id = $_POST['supplier_idSuaPN'] ?? '';
$user_id = $_POST['user_idSuaPN'] ?? '';
$total_price = $_POST['txtTongGT'] ?? '';

if ($id && $supplier_id && $user_id && $total_price) {
    $sql = "UPDATE importreceipt SET supplier_id = ?, user_id = ?, total_price = ? WHERE ImportReceipt_id = ?";
    $result = $db->execute($sql, [$supplier_id, $user_id, $total_price, $id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
}
?>
