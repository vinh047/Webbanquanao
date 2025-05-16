<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();
header('Content-Type: application/json');

$data = $_POST;
if (empty($data['code']) || empty($data['discount']) || empty($data['start_date']) || empty($data['end_date']) || empty($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu bắt buộc.']);
    exit;
}

$ok = $db->execute(
    "INSERT INTO vouchers (code, discount, start_date, end_date, status)
     VALUES (?, ?, ?, ?, ?)",
    [$data['code'], $data['discount'], $data['start_date'], $data['end_date'], $data['status']]
);

echo json_encode(['success' => $ok, 'message' => $ok ? 'Thêm thành công' : 'Thêm thất bại']);