<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
header('Content-Type: application/json');
ob_start();

$db = DBConnect::getInstance();
$id = $_POST['id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !in_array($status, ['0', '1'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}
ob_end_clean();

try {
    $conn = $db->getConnection();
    $conn->beginTransaction();

    // 1. Kiểm tra trạng thái hiện tại
    $stmtCheck = $conn->prepare("SELECT status FROM importreceipt WHERE importreceipt_id = ?");
    $stmtCheck->execute([$id]);
    $currentStatus = $stmtCheck->fetchColumn();

if ($currentStatus == 0 && $status == 0) {
    $conn->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Phiếu nhập đã được xác nhận trước đó (không cần thực hiện lại).'
    ]);
    exit;
}




    // 2. Cập nhật trạng thái mới
    $stmt = $conn->prepare("UPDATE importreceipt SET status = ? WHERE importreceipt_id = ?");
    $stmt->execute([$status, $id]);

    if ($status == '1') {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Đã chuyển sang trạng thái Mở']);
        exit;
    }

    // 3. Lấy danh sách chi tiết phiếu nhập kèm thông tin biến thể
$stmtCT = $conn->prepare("
    SELECT 
        d.variant_id,
        SUM(d.quantity) AS quantity
    FROM importreceipt_details d
    WHERE d.importreceipt_id = ? AND d.is_deleted = 0
    GROUP BY d.variant_id
");
$stmtCT->execute([$id]);
$details = $stmtCT->fetchAll(PDO::FETCH_ASSOC);


    if (empty($details)) {
        throw new Exception("Không có chi tiết phiếu nhập để xác nhận.");
    }

    foreach ($details as $ct) {
        $variant_id = $ct['variant_id'];
        $quantity = $ct['quantity'];

        // Kiểm tra tồn tại biến thể
        $stmtCheckVariant = $conn->prepare("SELECT stock FROM product_variants WHERE variant_id = ?");
        $stmtCheckVariant->execute([$variant_id]);
        $exists = $stmtCheckVariant->fetch();

        if ($exists) {
            // ➕ Cộng stock vào biến thể đã có
            $stmtUp = $conn->prepare("UPDATE product_variants SET stock = stock + ? WHERE variant_id = ?");
            $stmtUp->execute([$quantity, $variant_id]);
        } else {
            throw new Exception("Biến thể sản phẩm không tồn tại: variant_id = $variant_id");
        }
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => '✅ Đã xác nhận phiếu nhập và cập nhật tồn kho.']);
} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['success' => false, 'message' => '❌ Lỗi: ' . $e->getMessage()]);
}
?>
