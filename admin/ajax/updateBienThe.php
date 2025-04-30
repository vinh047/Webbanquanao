<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['txtMaBt'], $_POST['txtMaSua'], $_POST['cbSizeSua'], $_POST['txtSlSua'], $_POST['cbMauSua'])) {

        $idvr = (int)$_POST['txtMaBt'];     // ID biến thể đang sửa
        $idsp = (int)$_POST['txtMaSua'];    // ID sản phẩm
        $sl   = (int)$_POST['txtSlSua'];    // Số lượng
        $size = (int)$_POST['cbSizeSua'];   // Size mới
        $mau  = (int)$_POST['cbMauSua'];    // Màu mới

        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        // 1. ✅ Lấy ảnh (nếu có chọn file mới)
        $anh = '';
        if (isset($_FILES['fileAnhSua']) && $_FILES['fileAnhSua']['size'] > 0) {
            $anh = basename($_FILES['fileAnhSua']['name']);
            move_uploaded_file($_FILES['fileAnhSua']['tmp_name'], "../../assets/img/sanpham/$anh");
        } else {
            $stmt = $conn->prepare("SELECT image FROM product_variants WHERE variant_id = ?");
            $stmt->execute([$idvr]);
            $anh = $stmt->fetchColumn();
        }

        // 2. ✅ Kiểm tra trùng biến thể (cùng product, size, màu, ảnh) nhưng ID khác
        $stmt = $conn->prepare("SELECT variant_id FROM product_variants 
            WHERE product_id = ? AND size_id = ? AND color_id = ? AND image = ? AND variant_id != ?");
        $stmt->execute([$idsp, $size, $mau, $anh, $idvr]);

        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Đã tồn tại biến thể với size, màu và ảnh này.']);
            exit;
        }

        // 3. ✅ Cập nhật bảng product_variants
        $stmt = $conn->prepare("UPDATE product_variants 
            SET product_id = ?, image = ?, size_id = ?, stock = ?, color_id = ?
            WHERE variant_id = ?");
        $stmt->execute([$idsp, $anh, $size, $sl, $mau, $idvr]);

        // 4. ✅ Cập nhật lại tổng giá trị (nếu có)
        $stmt = $conn->prepare("SELECT importreceipt_details_id FROM importreceipt_details WHERE variant_id = ? LIMIT 1");
        $stmt->execute([$idvr]);
        $idctpn = $stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt->execute([$idsp]);
        $giaNhap = $stmt->fetchColumn();
        if ($giaNhap === false) $giaNhap = 0;

        $tongGiaTri = $sl * $giaNhap;

        // Nếu cần cập nhật luôn total_price:
        // $stmt = $conn->prepare("UPDATE importreceipt_details SET total_price = ? WHERE importreceipt_details_id = ?");
        // $stmt->execute([$tongGiaTri, $idctpn]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu hoặc phương thức không hợp lệ.']);
    }
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
