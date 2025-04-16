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
    $giaban = isset($_POST['giaban']) ? (float)$_POST['giaban'] : null;
    $pttg = isset($_POST['pttg']) ? floatval($_POST['pttg']) : 0;

    try {
        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        // ✅ Nếu giá bán chưa được nhập hoặc <= 0 thì tự tính theo công thức
        if (is_null($giaban) || $giaban <= 0) {
            $giaban = $giaMoi * (1 + $pttg / 100);
        }

        // ✅ Cập nhật cả giá nhập, giá bán, phần trăm
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
