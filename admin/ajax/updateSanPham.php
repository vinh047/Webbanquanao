<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id'], $_POST['ten'], $_POST['loai'], $_POST['mota'], $_POST['gia'])) {

    $id = (int)$_POST['id'];
    $ten = trim($_POST['ten']);
    $loai = (int)$_POST['loai'];
    $mota = trim($_POST['mota']);
    $giaMoi = (float)$_POST['gia'];
    $giaban = isset($_POST['giaban']) ? (float)$_POST['giaban'] : 0;
    $pttg = isset($_POST['pttg']) ? floatval($_POST['pttg']) : 0; // ✅ thêm dòng này

    try {
        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        // Nếu giá thay đổi, tự tính lại giaban
        $giaban = $giaMoi * (1 + $pttg / 100);


        // ✅ Cập nhật thêm cột pttg
        $sql = "UPDATE products 
                SET name = ?, category_id = ?, description = ?, price = ?, price_sale = ?, pttg = ?
                WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $loai, $mota, $giaMoi, $giaban, $pttg, $id]);

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