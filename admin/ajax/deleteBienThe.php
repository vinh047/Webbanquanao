<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $db = DBConnect::getInstance();
    $conn = $db->getConnection();

    try {
        // Kiểm tra biến thể có từng được bán không
        $check = $conn->prepare("SELECT COUNT(*) FROM order_details WHERE variant_id = ?");
        $check->execute([$id]);
        $sold = $check->fetchColumn();

        if ($sold > 0) {
            $hide = $conn->prepare("UPDATE product_variants SET stock = 0 WHERE variant_id = ?");
            $hide->execute([$id]);
            $affected = $hide->rowCount(); // 👈 kiểm tra số dòng thực sự bị ảnh hưởng
        
            echo json_encode([
                'success' => $affected > 0,
                'hidden' => $affected > 0,
                'message' => $affected > 0 ? 'Đã ẩn biến thể' : 'Ẩn thất bại'
            ]);
        } else {
            $del = $conn->prepare("DELETE FROM product_variants WHERE variant_id = ?");
            $del->execute([$id]);
            $affected = $del->rowCount();
        
            echo json_encode([
                'success' => $affected > 0,
                'deleted' => $affected > 0,
                'message' => $affected > 0 ? 'Đã xoá biến thể' : 'Xoá thất bại'
            ]);
        }
        
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
}
?>
