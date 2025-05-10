<?php
require_once '../../database/DBConnection.php';
header('Content-Type: application/json');

$db = DBConnect::getInstance();
$role_id = $_POST['role_id'] ?? null;
$permissions = $_POST['permissions'] ?? [];

if (!$role_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu role_id']);
    exit;
}

// Xóa toàn bộ quyền cũ
$db->execute("DELETE FROM role_permission_details WHERE role_id = ?", [$role_id]);

// Lưu mới
foreach ($permissions as $permission_id => $actions) {
    foreach ($actions as $action) {
        $db->execute("INSERT INTO role_permission_details (role_id, permission_id, action) VALUES (?, ?, ?)", [
            $role_id, $permission_id, $action
        ]);
    }
}

echo json_encode(['success' => true, 'message' => 'Lưu phân quyền thành công!']);
