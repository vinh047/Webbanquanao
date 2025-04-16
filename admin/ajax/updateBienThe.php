<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['txtMaBt'], $_POST['txtMaSua'], $_POST['cbSizeSua'], $_POST['txtSlSua'], $_POST['cbMauSua'])) {

    $variant_id = (int)$_POST['txtMaBt'];
    $new_product_id = (int)$_POST['txtMaSua'];
    $size_id = (int)$_POST['cbSizeSua'];
    $stock = (int)$_POST['txtSlSua'];
    $color_id = (int)$_POST['cbMauSua'];

    $db = DBConnect::getInstance();
    $conn = $db->getConnection();

    // 🔍 Kiểm tra ảnh
    $image = '';
    if (isset($_FILES['fileAnhSua']) && $_FILES['fileAnhSua']['size'] > 0) {
        $image = basename($_FILES['fileAnhSua']['name']);
        move_uploaded_file($_FILES['fileAnhSua']['tmp_name'], "../../assets/img/sanpham/$image");
    } else {
        $stmtImg = $conn->prepare("SELECT image FROM product_variants WHERE variant_id = ?");
        $stmtImg->execute([$variant_id]);
        $image = $stmtImg->fetchColumn();
    }

    // 🔁 Lấy giá gốc từ products
    $stmtPrice = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmtPrice->execute([$new_product_id]);
    $import_price = $stmtPrice->fetchColumn() ?: 0;

    $total_price = $stock * $import_price;

    // ✅ Cập nhật bảng product_variants
    $stmt = $conn->prepare("UPDATE product_variants 
                            SET product_id = ?, image = ?, size_id = ?, stock = ?, color_id = ?
                            WHERE variant_id = ?");
    $stmt->execute([$new_product_id, $image, $size_id, $stock, $color_id, $variant_id]);

    // ✅ Đồng bộ bảng importreceipt_details (nhiều dòng)
    $stmtUpdate = $conn->prepare("UPDATE importreceipt_details 
                                  SET product_id = ?, total_price = ?, import_price = ?
                                  WHERE variant_id = ?");
    $stmtUpdate->execute([$new_product_id, $total_price, $import_price, $variant_id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu hoặc sai phương thức.'
    ]);
}
?>