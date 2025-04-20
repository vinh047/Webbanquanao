<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id'], $_POST['ten'], $_POST['loai'], $_POST['mota'], $_POST['gia'])) {

    $id = (int)$_POST['id'];
    $ten = trim($_POST['ten']);
    $loai = (int)$_POST['loai'];
    $mota = trim($_POST['mota']);
    $giaMoi = (float)$_POST['gia'];
    $giaban = isset($_POST['giaban']) ? (float)$_POST['giaban'] : null;
    $pttg = isset($_POST['pttg']) ? floatval($_POST['pttg']) : 0;

    try {
        $db = DBConnect::getInstance();
        $conn = $db->getConnection();

        // ✅ Tự động tính giá bán nếu không có
        if (is_null($giaban) || $giaban <= 0) {
            $giaban = $giaMoi * (1 + $pttg / 100);
        }

        // ✅ 1. Cập nhật sản phẩm
        $sql = "UPDATE products 
                SET name = ?, category_id = ?, description = ?, price = ?, price_sale = ?, pttg = ?
                WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$ten, $loai, $mota, $giaMoi, $giaban, $pttg, $id]);

        // ✅ 2. Cập nhật unit_price và total_price bên chi tiết phiếu nhập
        $stmtUpdate = $conn->prepare("
            UPDATE importreceipt_details 
            SET 
                unit_price = ?, 
                total_price = quantity * ?
            WHERE product_id = ?
        ");
        $stmtUpdate->execute([$giaMoi, $giaMoi, $id]);

        // ✅ 3. Lấy các importreceipt_id bị ảnh hưởng
        $stmtReceipts = $conn->prepare("
            SELECT DISTINCT importreceipt_id 
            FROM importreceipt_details 
            WHERE product_id = ?
        ");
        $stmtReceipts->execute([$id]);
        $receiptIds = $stmtReceipts->fetchAll(PDO::FETCH_COLUMN);

        // ✅ 4. Với mỗi phiếu nhập, tính lại tổng tiền
        $stmtRecalculate = $conn->prepare("
            UPDATE importreceipt
            SET total_price = (
                SELECT SUM(total_price)
                FROM importreceipt_details
                WHERE importreceipt_id = ?
            )
            WHERE ImportReceipt_id = ?
        ");
        foreach ($receiptIds as $receiptId) {
            $stmtRecalculate->execute([$receiptId, $receiptId]);
        }

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu đầu vào hoặc phương thức không hợp lệ.'
    ]);
}
?>
