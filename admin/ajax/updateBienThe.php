<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ghi log để debug
        file_put_contents("log_update.txt", print_r($_POST, true) . print_r($_FILES, true));

        if (!isset($_POST['txtMaBt'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã biến thể!']);
            exit;
        }

        $idvr = (int)$_POST['txtMaBt'];
        $tenAnhCu = $_POST['tenAnhCu'] ?? null;

        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        $file = $_FILES['fileAnhSua'] ?? null;

        if ($file && $file['size'] > 0) {
            // Có ảnh mới
            $anh = basename($file['name']);
            $uploadDir = __DIR__ . '/../../assets/img/sanpham/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadFilePath = $uploadDir . $anh;

            if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                echo json_encode(['success' => false, 'message' => 'Upload ảnh thất bại']);
                exit;
            }
        } else {
            // Không có ảnh mới → dùng ảnh cũ
            if (!$tenAnhCu) {
                echo json_encode(['success' => false, 'message' => 'Không có ảnh mới hoặc ảnh cũ!']);
                exit;
            }
            $anh = basename($tenAnhCu);
        }

        // Cập nhật ảnh trong DB
        $stmt = $conn->prepare("UPDATE product_variants SET image = ? WHERE variant_id = ?");
        $stmt->execute([$anh, $idvr]);

        echo json_encode(['success' => true, 'message' => 'Cập nhật ảnh thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ!']);
    }
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
