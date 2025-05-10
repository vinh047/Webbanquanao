<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Lấy data
$category_id = $_POST['category_id'];
$name = $_POST['category_name'] ?? '';

$success = $db->execute('UPDATE categories SET name = ? WHERE category_id = ?', [$name, $category_id]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thể loại thành công.']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Cập nhật thông tin thể loại không thành công!']);
}
?>