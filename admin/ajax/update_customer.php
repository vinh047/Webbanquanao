<?php
require_once '../../database/DBConnection.php';

header('Content-Type: application/json; charset=utf-8');

$db = DBConnect::getInstance();

// Lấy dữ liệu từ POST
$user_id = $_POST['user_id'] ?? '';
$status  = isset($_POST['status']) ? (int)$_POST['status'] : 1;

// Kiểm tra bắt buộc
if ($user_id === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu user_id để cập nhật.'
    ]);
    exit;
}

// Cập nhật chỉ trường trạng thái
try {
    $success = $db->execute(
        'UPDATE users SET status = ? WHERE user_id = ?',
        [$status, $user_id]
    );

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật trạng thái khách hàng thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cập nhật không thành công.'
        ]);
    }
} catch (PDOException $e) {
    error_log('Lỗi update_customer: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống, vui lòng thử lại.'
    ]);
}
