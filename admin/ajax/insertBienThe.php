<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

$db = DBConnect::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ POST']);
    exit;
}

try {
    $product_id = $_POST['id_sanpham'] ?? null;
    $colors = $_POST['colors'] ?? [];
    $sizes = $_POST['sizes'] ?? [];


    $uploadDir = __DIR__ . '/../../assets/img/sanpham/';
    $successCount = 0;
    foreach ($colors as $index => $color_id) {
        $color_id = intval($color_id);
        $size_id = intval($sizes[$index] ?? 0);
    
        if ($color_id <= 0 || $size_id <= 0) continue;
    
        // ✅ Kiểm tra ảnh tồn tại
        if (!isset($_FILES['images']['tmp_name'][$index])) {
            throw new Exception("Thiếu ảnh cho dòng $index");
        }
    
        $tmpPath = $_FILES['images']['tmp_name'][$index];
        $originalName = basename($_FILES['images']['name'][$index]);
    
        $fileType = mime_content_type($tmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Ảnh không đúng định dạng (jpg, png, webp)");
        }
    
        $targetPath = $uploadDir . $originalName;
    
        // ✅ Kiểm tra biến thể đã tồn tại (sau khi có $originalName)
        $stmtCheck = $conn->prepare("SELECT variant_id FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ?");
        $stmtCheck->execute([$product_id, $color_id, $size_id]);
        if ($stmtCheck->fetch()) {
            throw new Exception("Biến thể (màu + size) đã tồn tại trong hệ thống!");
        }
    
        // ✅ Nếu ảnh chưa có trong thư mục thì mới move vào
        if (!file_exists($targetPath)) {
            if (!move_uploaded_file($tmpPath, $targetPath)) {
                throw new Exception("Không thể lưu ảnh biến thể vào thư mục.");
            }
        }
    
        // ✅ Lưu thông tin biến thể với tên ảnh gốc
        $stmtInsert = $conn->prepare("INSERT INTO product_variants (product_id, color_id, size_id, stock, image) VALUES (?, ?, ?, 0, ?)");
        $stmtInsert->execute([$product_id, $color_id, $size_id, $originalName]);
        $successCount++;
    }
    

    echo json_encode([
        'success' => true,
        'message' => "Đã thêm $successCount biến thể thành công!"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}
