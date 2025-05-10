<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

$color_id = $_POST['color_id'];

$sucess = $db->execute('UPDATE colors SET is_deleted = 1 WHERE color_id = ?', [$color_id]);
if($sucess) {
    echo json_encode(['success' => true, 'message' => 'Xóa màu thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa màu!']);
}

?>