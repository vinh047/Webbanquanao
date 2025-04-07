<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['txtMaBt'], $_POST['txtMaSua'], $_POST['cbSizeSua'], $_POST['txtSlSua'], $_POST['cbMauSua']) &&
    isset($_FILES['fileAnhSua'])) {

    $idvr = (int)$_POST['txtMaBt'];
    $idsp = (int)$_POST['txtMaSua'];
    $sl = (int)$_POST['txtSlSua'];
    $size = $_POST['cbSizeSua'];
    $mau = (int)$_POST['cbMauSua'];

    $db = DBConnect::getInstance();
    $conn = $db->getConnection();

    $anh = ''; // Tên file ảnh nếu có

// Nếu có upload ảnh mới
if (isset($_FILES['fileAnhSua']) && $_FILES['fileAnhSua']['size'] > 0) {
    $anh = basename($_FILES['fileAnhSua']['name']);
    move_uploaded_file($_FILES['fileAnhSua']['tmp_name'], "../../assets/img/sanpham/$anh");
} else {
    // Nếu không upload ảnh mới thì lấy lại tên ảnh cũ từ DB
    $stmt = $conn->prepare("SELECT image FROM product_variants WHERE variant_id = ?");
    $stmt->execute([$idvr]);
    $anh = $stmt->fetchColumn();
}


    $sql = "UPDATE product_variants 
            SET product_id = ?, image = ?, size = ?, stock = ?, color_id = ?
            WHERE variant_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$idsp, $anh, $size, $sl, $mau, $idvr]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu hoặc phương thức không hợp lệ.'
    ]);
}

?>