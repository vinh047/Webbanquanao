<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id'], $_POST['ten'], $_POST['loai'], $_POST['mota'], $_POST['gia'])) {

    $id = (int)$_POST['id'];
    $ten = trim($_POST['ten']);
    $loai = (int)$_POST['loai'];
    $mota = trim($_POST['mota']);
    $gia = (float)$_POST['gia'];

    try {
        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        $sql = "UPDATE products 
                SET name = ?, category_id = ?, description = ?, price = ? 
                WHERE product_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $loai, $mota, $gia, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu đầu vào hoặc phương thức không hợp lệ.'
    ]);
}
?>