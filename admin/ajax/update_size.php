<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Lấy data
$size_id = $_POST['size_id'];
$name = $_POST['size_name'] ?? '';

$success = $db->execute('UPDATE size SET name = ? WHERE size_id = ?', [$name, $size_id]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin size thành công.']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin size không thành công!']);
}
?>