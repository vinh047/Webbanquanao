<?php
require_once '../../database/DBConnection.php';

$db = DBConnect::getInstance();

header('Content-Type: application/json');

$supplier_id = $_POST['supplier_id'];

$sucess = $db->execute('UPDATE supplier SET is_deleted = 1 WHERE supplier_id = ?', [$supplier_id]);
if($sucess) {
    echo json_encode(['success' => true, 'message' => 'Xóa nhà cung cấp thành công']);
}
else {
    echo json_encode(['success' => false, 'message' => 'Lỗi xóa nhà cung cấp!']);
}

?>