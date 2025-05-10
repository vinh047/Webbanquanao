<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

// Láy data
$name = $_POST['size_name'] ?? '';

$success = $db->execute('INSERT INTO sizes (name) VALUES (?)', [$name]);

if($success) {
    echo json_encode(['success' => true, 'message' => 'Thêm size thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Không thể thêm size.']);
}


?>