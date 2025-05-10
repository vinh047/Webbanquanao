<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Láy data
$name = $_POST['category_name'] ?? '';

$success = $db->execute('INSERT INTO categories (name) VALUES (?)', [$name]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Thêm thể loại thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm thể loại.']);
}


?>