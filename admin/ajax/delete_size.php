<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

$size_id = $_POST['size_id'];

$sucess = $db->execute('UPDATE sizes SET is_deleted = 1 WHERE size_id = ?', [$size_id]);
if($sucess) {
    echo json_encode(['success' => true, 'message' => 'Xóa size thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa size!']);
}

?>