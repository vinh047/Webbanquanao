<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

$category_id = $_POST['category_id'];

$sucess = $db->execute('UPDATE categories SET is_deleted = 1 WHERE category_id = ?', [$category_id]);
if($sucess) {
    echo json_encode(['success' => true, 'message' => 'Xóa thể loại thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa thể loại!']);
}

?>