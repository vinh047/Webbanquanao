<?php
    require_once(__DIR__ . '/../../database/DBConnection.php');
    header('Content-Type: application/json');

try {
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

        if (isset($_FILES['fileAnhSua']) && $_FILES['fileAnhSua']['size'] > 0) {
            $anh = basename($_FILES['fileAnhSua']['name']);
            move_uploaded_file($_FILES['fileAnhSua']['tmp_name'], "../../assets/img/sanpham/$anh");
        } else {
            $stmt = $conn->prepare("SELECT image FROM product_variants WHERE variant_id = ?");
            $stmt->execute([$idvr]);
            $anh = $stmt->fetchColumn();
        }

$stmt = $conn->prepare("SELECT importreceipt_details_id FROM importreceipt_details WHERE variant_id = ? LIMIT 1");
        $stmt->execute([$idvr]);
        $idctpn = $stmt->fetchColumn();

        $stmt = $conn->prepare("UPDATE product_variants 
                                SET product_id = ?, image = ?, size_id = ?, stock = ?, color_id = ?
                                WHERE variant_id = ?");
        $stmt->execute([$idsp, $anh, $size, $sl, $mau, $idvr]);

        $stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
        $stmt->execute([$idsp]);
        $giaNhap = $stmt->fetchColumn();
        if ($giaNhap === false) $giaNhap = 0;

        $tongGiaTri = $sl * $giaNhap;

        $stmt = $conn->prepare("UPDATE importreceipt_details SET total_price = ? WHERE ImportReceipt_details_id = ?");
        $stmt->execute([$tongGiaTri, $idctpn]);

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
