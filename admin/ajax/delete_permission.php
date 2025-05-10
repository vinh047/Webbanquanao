<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

$db = DBConnect::getInstance();
$permission_id = $_POST['permission_id'] ?? null;

if (!$permission_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID chức năng!']);
    exit;
}

// Xóa mềm: cập nhật cờ is_deleted
$updated = $db->execute("UPDATE permissions SET is_deleted = 1 WHERE permission_id = ?", [$permission_id]);

echo json_encode([
    'success' => $updated,
    'message' => $updated ? 'Đã ẩn chức năng thành công!' : 'Ẩn chức năng thất bại!'
]);
