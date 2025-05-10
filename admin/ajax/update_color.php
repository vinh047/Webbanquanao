<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Lấy data
$color_id = $_POST['color_id'];
$name = $_POST['color_name'] ?? '';
$hex_code = $_POST['hex_code'] ?? '';

$success = $db->execute('UPDATE colors SET name = ?, hex_code = ? WHERE color_id = ?', [$name, $hex_code, $color_id]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin màu sắc thành công.']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin màu sắc không thành công!']);
}
?>