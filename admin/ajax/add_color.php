<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Láy data
$name = $_POST['color_name'] ?? '';
$hex_code = $_POST['hex_code'] ?? '';

$success = $db->execute('INSERT INTO colors (name, hex_code) VALUES (?, ?)', [$name, $hex_code]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Thêm màu thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm màu.']);
}


?>