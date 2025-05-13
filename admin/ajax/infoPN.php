<?php
require_once('../../database/DBConnection.php');
header('Content-Type: application/json');

$pdo = DBConnect::getInstance()->getConnection();

$idpn = isset($_GET['idpn']) ? (int)$_GET['idpn'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8;
$offset = ($page - 1) * $limit;

if (!$idpn) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã phiếu nhập']);
    exit;
}

try {
    // Đếm tổng chi tiết để phân trang
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM importreceipt_details WHERE importreceipt_id = ? AND is_deleted = 0");
    $stmtCount->execute([$idpn]);
    $totalItems = (int)$stmtCount->fetchColumn();
    $totalPages = ceil($totalItems / $limit);

    // Lấy dữ liệu chi tiết có phân trang
    $stmt = $pdo->prepare("
SELECT 
    p.name AS product_name,
    p.product_id AS product_id,
    v.variant_id,
    c.name AS color_name,
    s.name AS size_name,
    d.quantity,
    d.unit_price,
    v.stock
FROM importreceipt_details d
JOIN product_variants v ON d.variant_id = v.variant_id
JOIN products p ON v.product_id = p.product_id  -- ✅ sửa ở đây
JOIN colors c ON v.color_id = c.color_id
JOIN sizes s ON v.size_id = s.size_id
WHERE d.importreceipt_id = ? AND d.is_deleted = 0
LIMIT $limit OFFSET $offset

    ");
    $stmt->execute([$idpn]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Thêm đoạn lấy thông tin phiếu nhập:
$stmtInfo = $pdo->prepare("
    SELECT i.total_price AS tong_giatri, s.name AS supplier_name, u.name AS user_name,
           (SELECT SUM(quantity) FROM importreceipt_details WHERE importreceipt_id = ?) AS tong_soluong
    FROM importreceipt i
    JOIN supplier s ON i.supplier_id = s.supplier_id
    JOIN users u ON i.user_id = u.user_id
    WHERE i.ImportReceipt_id = ?
");
$stmtInfo->execute([$idpn, $idpn]);
$info = $stmtInfo->fetch(PDO::FETCH_ASSOC);


echo json_encode([
    'success' => true,
    'data' => $data,
'pagination' => $totalPages > 1 ? [
    'current' => $page,
    'total' => $totalPages
] : null,

    'info' => $info
]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi SQL: ' . $e->getMessage()]);
}
?>