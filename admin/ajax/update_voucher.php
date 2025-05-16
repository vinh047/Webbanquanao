<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();
header('Content-Type: application/json');

$data = $_POST;
if (empty($data['voucher_id']) || empty($data['code']) || empty($data['discount']) || empty($data['start_date']) || empty($data['end_date']) || empty($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu để cập nhật.']);
    exit;
}

$ok = $db->execute(
    "UPDATE vouchers SET code = ?, discount = ?, start_date = ?, end_date = ?, status = ?
     WHERE voucher_id = ?",
    [$data['code'], $data['discount'], $data['start_date'], $data['end_date'], $data['status'], $data['voucher_id']]
);

echo json_encode(['success' => $ok, 'message' => $ok ? 'Cập nhật thành công' : 'Cập nhật thất bại']);