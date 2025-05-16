<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json; charset=utf-8');

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

if ($user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu hoặc không hợp lệ user_id.'
    ]);
    exit;
}

$success = $db->execute(
    'UPDATE users SET status = 0 WHERE user_id = ? AND status != 0',
    [$user_id]
);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Khóa nhân viên thành công.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi khóa nhân viên hoặc nhân viên đã bị khóa.'
    ]);
}
