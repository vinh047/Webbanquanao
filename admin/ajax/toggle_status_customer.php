<?php
require_once '../../database/DBConnection.php';

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$status = $data['status'] ?? null;

if ($user_id === null || $status === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu đầu vào.'
    ]);
    exit;
}

$db = DBConnect::getInstance();

try {
    $ok = $db->execute(
        "UPDATE users SET status = ? WHERE user_id = ?",
        [$status, $user_id]
    );

    if ($ok) {
        echo json_encode([
            'success' => true,
            'message' => $status == 1 ? 'Mở khóa tài khoản thành công.' : 'Khóa tài khoản thành công.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Cập nhật trạng thái không thành công.'
        ]);
    }
} catch (Exception $e) {
    error_log('Lỗi toggle_status_customer.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống, vui lòng thử lại.'
    ]);
}
