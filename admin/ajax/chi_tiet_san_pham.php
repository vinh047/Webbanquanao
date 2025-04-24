<?php
require_once(__DIR__ . '/../../database/DBConnection.php');
$db = DBConnect::getInstance();

$product_id = $_POST['product_id'] ?? 0;
$page = $_POST['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Lấy info sản phẩm chính
$info = $db->select("SELECT p.product_id, p.name, p.description, p.price, p.price_sale, p.pttg,
                            c.name AS category
                     FROM products p
                     JOIN categories c ON p.category_id = c.category_id
                     WHERE p.product_id = ?", [$product_id]);

// Lấy tổng số biến thể để tính phân trang
$totalVariants = $db->select("SELECT COUNT(*) AS total FROM product_variants WHERE product_id = ? AND is_deleted = 0", [$product_id]);
$total = $totalVariants[0]['total'];
$totalPages = ceil($total / $limit);

// Lấy danh sách biến thể theo trang
$variants = $db->select("
    SELECT 
        pv.variant_id,
        p.name AS product_name,
        s.name AS size,
        c.name AS color,
        pv.image,
        pv.stock,
        pv.product_id
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.product_id
    JOIN sizes s ON pv.size_id = s.size_id
    JOIN colors c ON pv.color_id = c.color_id
    WHERE pv.product_id = ? AND pv.is_deleted = 0
    LIMIT $limit OFFSET $offset
", [$product_id]);

echo json_encode([
  'info' => $info[0],
  'variants' => $variants,
  'pagination' => [
    'current' => (int)$page,
    'total' => $totalPages
  ]
]);

?>