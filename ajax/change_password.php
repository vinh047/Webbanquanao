<?php
session_start();
// Tắt hiển thị lỗi ra client
ini_set('display_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

// Đọc dữ liệu từ AJAX
$data   = json_decode(file_get_contents('php://input'), true);
$oldPw  = trim($data['old_password'] ?? '');
$newPw  = trim($data['new_password'] ?? '');
$userId = $_SESSION['user_id'] ?? null;

// Kiểm tra đã đăng nhập chưa
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
    exit;
}

// Lấy hash mật khẩu hiện tại từ DB
$user = $db->selectOne(
    'SELECT password FROM users WHERE user_id = ?',
    [$userId]
);
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
    exit;
}
$currentHash = $user['password'];

// Xác thực mật khẩu cũ
if (!password_verify($oldPw, $currentHash)) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng']);
    exit;
}

// Validate mật khẩu mới: tối thiểu 8 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt
$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/';
if (!preg_match($pattern, $newPw)) {
    echo json_encode([
        'success' => false,
        'message' => 'Mật khẩu mới phải ít nhất 8 ký tự, gồm chữ hoa, chữ thường, số và ký tự đặc biệt'
    ]);
    exit;
}

// Hash và cập nhật mật khẩu mới
$hashed = password_hash($newPw, PASSWORD_DEFAULT);
$success = $db->execute(
    'UPDATE users SET password = ? WHERE user_id = ?',
    [$hashed, $userId]
);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật mật khẩu thất bại']);
}
exit;