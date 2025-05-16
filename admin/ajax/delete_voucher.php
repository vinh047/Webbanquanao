<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();
header('Content-Type: application/json');

$id = $_GET['delete'] ?? 0;
if (!is_numeric($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

$ok = $db->execute("DELETE FROM vouchers WHERE voucher_id = ?", [$id]);
echo json_encode(['success' => $ok, 'message' => $ok ? 'Xoá thành công' : 'Xoá thất bại']);