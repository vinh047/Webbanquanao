<?php
require_once '../database/DBConnection.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$code = strtoupper(trim($input['code'] ?? ''));

if (!$code) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã voucher']);
    exit;
}

$db = DBConnect::getInstance();
$sql = "SELECT * FROM vouchers WHERE code = ? AND status = 'active' ORDER BY voucher_id ASC LIMIT 1";
$voucher = $db->selectOne($sql, [$code]);

if ($voucher) {
    echo json_encode([
        'success' => true,
        'discount' => floatval($voucher['discount']),
        'code' => $voucher['code']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn']);
}
