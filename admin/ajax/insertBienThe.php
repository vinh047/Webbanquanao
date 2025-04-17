<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();

header('Content-Type: application/json'); // 🔑 Bắt buộc nếu trả về JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idsp = $_POST['txtMa'];
    $size = $_POST['cbSize'];
    $mau = $_POST['cbMau'];
    $soluong = $_POST['txtSl'];

    if (isset($_FILES['fileAnh']) && $_FILES['fileAnh']['error'] == 0) {
        $targetDir = "../../assets/img/sanpham/";
        $filename = basename($_FILES["fileAnh"]["name"]);
        $targetFile = $targetDir . $filename;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $validTypes)) {
            echo json_encode([
                'success' => false,
                'message' => "File ảnh không hợp lệ (chỉ chấp nhận jpg, jpeg, png, gif)"
            ]);
            exit;
        }

        if (move_uploaded_file($_FILES["fileAnh"]["tmp_name"], $targetFile)) {
            $img = $filename;

            $stmt = $pdo->prepare("INSERT INTO product_variants 
                (product_id, image, size, stock, color_id) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$idsp, $img, $size, $soluong, $mau]);

            echo json_encode([
                'success' => true,
                'message' => "Thêm biến thể thành công"
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Lỗi khi upload ảnh!"
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Ảnh không hợp lệ hoặc chưa được chọn."
        ]);
    }
}
?>
