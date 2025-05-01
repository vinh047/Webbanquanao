<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$pdo = DBConnect::getInstance()->getConnection();

$product_id = $_GET['product_id'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

// Đếm tổng số biến thể
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM product_variants WHERE product_id = ? AND is_deleted = 0");
$totalStmt->execute([$product_id]);
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Truy vấn danh sách có phân trang
$sql = "
    SELECT 
        pv.variant_id,
        pv.image,
        pv.stock,
        pv.size_id,
        pv.color_id,
        sz.name AS size_name,
        cl.name AS color_name
    FROM product_variants pv
    LEFT JOIN sizes sz ON pv.size_id = sz.size_id
    LEFT JOIN colors cl ON pv.color_id = cl.color_id
    WHERE pv.product_id = ? AND pv.is_deleted = 0
    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);

echo json_encode([
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    'pagination' => [
        'current' => $page,
        'total' => $totalPages
    ]
]);
?>