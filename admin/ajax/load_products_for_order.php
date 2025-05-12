
<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';

$db = DBConnect::getInstance();

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

// Chỉ chọn khách hàng
$whereClauses[] = 'pv.is_deleted = 0';



// Gộp điều kiện WHERE
$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Truy vấn các sản phẩm có ít nhất một product_variant chưa bị xóa
$sql = "
    SELECT SQL_CALC_FOUND_ROWS DISTINCT p.*
    FROM products p
    JOIN product_variants pv ON p.product_id = pv.product_id
    $whereSql
    LIMIT $limit OFFSET $offset
";
$products = $db->select($sql, $params);

// Lấy tổng số dòng
$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalProduct = $totalResult[0]['FOUND_ROWS()'];

// Tính số trang
$totalPages = ceil($totalProduct / $limit);
$pagination = new Pagination($totalProduct, $limit, $page);

// Tạo HTML bảng sản phẩm
ob_start();
foreach ($products as $product): ?>
    <tr>
        <td><?= $product['product_id'] ?></td>
        <td><?= $product['name'] ?></td>
        <td><?= $product['price_sale'] ?></td>
        <td>
            <button
                class="btn btn-success mx-1 btn-choose-product"
                data-product-id="<?= $product['product_id'] ?>"
                data-name="<?= htmlspecialchars($product['name']) ?>"
                data-price="<?= $product['price_sale'] ?>">
                <i class="fa-regular fa-pen-to-square"></i>
                Chọn
            </button>
        </td>
    </tr>
<?php endforeach;
$productHtml = ob_get_clean();

// HTML phân trang
$paginationHtml = null;
if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

header('Content-Type: application/json');
echo json_encode([
    'productHtml' => $productHtml,
    'pagination' => $paginationHtml
]);
