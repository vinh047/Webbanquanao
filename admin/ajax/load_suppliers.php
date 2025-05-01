<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';

$db = DBConnect::getInstance();

$db = DBConnect::getInstance();

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

$whereClauses[] = 'is_deleted = 0';

$search_name = $_GET['search_name'] ?? '';
if (!empty($search_name)) {
    $whereClauses[] = 'name LIKE ?';
    $params[] = '%' . $search_name . '%';
}





// Gộp điều kiện WHERE
$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM supplier $whereSql LIMIT $limit OFFSET $offset";
$suppliers = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalSupplier = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalSupplier / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalSupplier, $limit, $page);

ob_start();
foreach ($suppliers as $supplier): ?>
    <tr>
        <td><?= $supplier['supplier_id'] ?></td>
        <td><?= $supplier['name'] ?></td>
        <td><?= $supplier['email'] ?></td>
        <td><?= $supplier['address'] ?></td>
        <td>
            <button class="btn btn-success mx-1 btn-edit-supplier"
                data-id="<?= $supplier['supplier_id'] ?>"
                data-name="<?= $supplier['name'] ?>"
                data-email="<?= $supplier['email'] ?>"
                data-address="<?= $supplier['address'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalSuaNCC">
                <i class="fa-regular fa-pen-to-square"></i>
                Sửa
            </button>
            <button class="btn btn-danger btn-delete-supplier mx-1"
                data-supplier-id="<?= $supplier['supplier_id'] ?>"
                data-name="<?= $supplier['name'] ?>"
                data-email="<?= $supplier['email'] ?>"
                data-address="<?= $supplier['address'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalXoaNCC">
                <i class="fas fa-trash"></i>
                Xóa
            </button>
        </td>
    </tr>
<?php endforeach;

$supplierHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'supplierHtml' => $supplierHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
