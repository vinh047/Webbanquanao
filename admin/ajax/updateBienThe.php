<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['txtMaBt'], $_POST['txtMaSua'], $_POST['cbSizeSua'], $_POST['txtSlSua'], $_POST['cbMauSua'])) {

    $idvr = (int)$_POST['txtMaBt'];
    $idsp = (int)$_POST['txtMaSua'];
    $sl = (int)$_POST['txtSlSua'];
    $size = (int)$_POST['cbSizeSua'];
    $mau = (int)$_POST['cbMauSua'];

    $db = DBConnect::getInstance();
    $conn = $db->getConnection();

    $anh = '';

    // Nếu có ảnh mới thì lưu
    if (isset($_FILES['fileAnhSua']) && $_FILES['fileAnhSua']['size'] > 0) {
        $anh = basename($_FILES['fileAnhSua']['name']);
        move_uploaded_file($_FILES['fileAnhSua']['tmp_name'], "../../assets/img/sanpham/$anh");
    } else {
        // Lấy lại ảnh cũ
        $stmt = $conn->prepare("SELECT image FROM product_variants WHERE variant_id = ?");
        $stmt->execute([$idvr]);
        $anh = $stmt->fetchColumn();
    }

    // Lấy importreceipt_details_id liên kết với biến thể này
    $stmt = $conn->prepare("SELECT importreceipt_details_id FROM product_variants WHERE variant_id = ?");
    $stmt->execute([$idvr]);
    $idctpn = $stmt->fetchColumn();

    // Cập nhật biến thể
    $stmt = $conn->prepare("UPDATE product_variants 
                            SET product_id = ?, image = ?, size_id = ?, stock = ?, color_id = ?
                            WHERE variant_id = ?");
    $stmt->execute([$idsp, $anh, $size, $sl, $mau, $idvr]);

    // Lấy giá gốc của sản phẩm
    $stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmt->execute([$idsp]);
    $giaNhap = $stmt->fetchColumn();

    if ($giaNhap === false) $giaNhap = 0;

    $tongGiaTri = $sl * $giaNhap;

    // ✅ Cập nhật lại total_price đúng dòng theo importreceipt_details_id
    $stmt = $conn->prepare("UPDATE importreceipt_details SET total_price = ? WHERE ImportReceipt_details_id = ?");
    $stmt->execute([$tongGiaTri, $idctpn]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu hoặc phương thức không hợp lệ.'
    ]);
}
