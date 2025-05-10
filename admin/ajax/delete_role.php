<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

$db = DBConnect::getInstance();

$role_id = $_POST['role_id'] ?? null;

// Không cho xóa nếu không có role_id hoặc cố tình xóa role admin (ID = 1)
if (!$role_id || $role_id == 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Vai trò không hợp lệ hoặc không thể xóa vai trò quản trị viên.'
    ]);
    exit;
}

// Cập nhật is_deleted = 1 (xóa mềm)
$success = $db->execute("UPDATE roles SET is_deleted = 1 WHERE role_id = ?", [$role_id]);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Đã xóa vai trò thành công.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Xóa vai trò thất bại.'
    ]);
}
