<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

// Đáp ứng JSON
header('Content-Type: application/json; charset=utf-8');

// Lấy user_id từ POST
$user_id = $_POST['user_id'] ?? '';
if (empty($user_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu user_id'
    ]);
    exit;
}

// Chuyển trạng thái thành 0 (khóa tài khoản)
$success = $db->execute(
    'UPDATE users SET status = 0 WHERE user_id = ?',
    [$user_id]
);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Xóa khách hàng thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xóa khách hàng!'
    ]);
}
?>