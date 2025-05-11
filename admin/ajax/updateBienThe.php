<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Debug xem $_POST và $_FILES nhận được gì
        file_put_contents("log_update.txt", print_r($_POST, true).print_r($_FILES, true));

        if (!isset($_POST['txtMaBt'], $_FILES['fileAnhSua'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu gửi lên!']);
            exit;
        }

        $idvr = (int)$_POST['txtMaBt'];
        $file = $_FILES['fileAnhSua'];

        if ($file['size'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'File ảnh rỗng hoặc không hợp lệ!']);
            exit;
        }

        $anh = basename($file['name']);
        $uploadDir = __DIR__ . '/../../assets/img/sanpham/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFilePath = $uploadDir . $anh;

        if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            $db = DBConnect::getInstance();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("UPDATE product_variants SET image = ? WHERE variant_id = ?");
            $stmt->execute([$anh, $idvr]);

            echo json_encode(['success' => true, 'message' => 'Cập nhật ảnh thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Upload ảnh thất bại']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
