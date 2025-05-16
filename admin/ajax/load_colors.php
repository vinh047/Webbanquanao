<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';
require_once 'permission_helper.php';

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

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM colors $whereSql LIMIT $limit OFFSET $offset";
$colors = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalColor = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalColor / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalColor, $limit, $page);

ob_start();
foreach ($colors as $color): ?>
    <tr>
        <td><?= $color['color_id'] ?></td>
        <td><?= $color['name'] ?></td>
        <td><?= $color['hex_code'] ?></td>
        <td>
            <?php if (hasPermission('Quản lý thuộc tính', 'write')): ?>
                <button class="btn btn-success mx-1 btn-edit-color"
                    data-id="<?= $color['color_id'] ?>"
                    data-name="<?= $color['name'] ?>"
                    data-hex-code="<?= $color['hex_code'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalSuaMauSac">
                    <i class="fa-regular fa-pen-to-square"></i>
                    Sửa
                </button>
            <?php endif; ?>
            <?php if (hasPermission('Quản lý thuộc tính', 'delete')): ?>
                <button class="btn btn-danger btn-delete-color mx-1"
                    data-id="<?= $color['color_id'] ?>"
                    data-name="<?= $color['name'] ?>"
                    data-hex-code="<?= $color['hex_code'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalXoaMauSac">
                    <i class="fas fa-trash"></i>
                    Xóa
                </button>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach;

$colorHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'colorHtml' => $colorHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
