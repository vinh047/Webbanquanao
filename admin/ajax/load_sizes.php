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

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM sizes $whereSql LIMIT $limit OFFSET $offset";
$sizes = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalSize = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalSize / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalSize, $limit, $page);

ob_start();
foreach ($sizes as $size): ?>
    <tr>
        <td><?= $size['size_id'] ?></td>
        <td><?= $size['name'] ?></td>
        <td>
            <button class="btn btn-success mx-1 btn-edit-size"
                data-id="<?= $size['size_id'] ?>"
                data-name="<?= $size['name'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalSuaSize">
                <i class="fa-regular fa-pen-to-square"></i>
                Sửa
            </button>
            <button class="btn btn-danger btn-delete-size mx-1"
                data-id="<?= $size['size_id'] ?>"
                data-name="<?= $size['name'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalXoaSize">
                <i class="fas fa-trash"></i>
                Xóa
            </button>
        </td>
    </tr>
<?php endforeach;

$sizeHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'sizeHtml' => $sizeHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
