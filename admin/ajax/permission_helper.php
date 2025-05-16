<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function hasPermission($permission_name, $action = 'read') {
    $role_id = $_SESSION['role_id'] ?? null;
    $db = DBConnect::getInstance();
    $query = "
        SELECT 1 FROM role_permission_details rpd
        JOIN permissions p ON rpd.permission_id = p.permission_id
        WHERE rpd.role_id = ? AND p.name = ? AND rpd.action = ?
        LIMIT 1
    ";
    $result = $db->select($query, [$role_id, $permission_name, $action]);
    return !empty($result);
}
