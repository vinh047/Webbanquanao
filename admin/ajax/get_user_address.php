<?php
require_once '../../database/DBConnection.php';
$db = DBConnect::getInstance();

$user_id = $_GET['user_id'] ?? '';

$user_addresses = $db->select(
    "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, updated_at DESC",
    [$user_id]
);

echo json_encode(['success' => $user_addresses, 'data' => $user_addresses]);
