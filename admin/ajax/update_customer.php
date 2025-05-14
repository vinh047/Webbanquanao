<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();
header('Content-Type: application/json; charset=utf-8');

// Lấy dữ liệu từ POST
$user_id  = $_POST['user_id']   ?? '';
$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

// Kiểm tra bắt buộc
if ($user_id === '' || $name === '' || $email === '' || $phone === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc'
    ]);
    exit;
}

// Cập nhật thông tin khách hàng
$success = $db->execute(
    'UPDATE users SET name = ?, email = ?, password = ?, phone = ?, status = ? WHERE user_id = ?',
    [
        $name,
        $email,
        $password,
        $phone,
        $status,
        $user_id
    ]
);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật khách hàng thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Cập nhật khách hàng không thành công'
    ]);
}
?>