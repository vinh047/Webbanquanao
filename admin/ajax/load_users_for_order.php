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
$whereClauses[] = 'status = 1 AND role_id = 1';

$name = $_GET['name'] ?? '';
$email = $_GET['email'] ?? '';
$phone = $_GET['phone'] ?? '';

if (!empty($name)) {
    $whereClauses[] = 'name LIKE ?';
    $params[] = '%' . $name . '%';
}
if (!empty($email)) {
    $whereClauses[] = 'email LIKE ?';
    $params[] = '%' . $email . '%';
}
if (!empty($phone)) {
    $whereClauses[] = 'phone LIKE ?';
    $params[] = '%' . $phone . '%';
}



// Gộp điều kiện WHERE
$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users $whereSql LIMIT $limit OFFSET $offset";
$users = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalUser = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalUser / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalUser, $limit, $page);

ob_start();
foreach ($users as $user): ?>
    <tr>
        <td><?= $user['user_id'] ?></td>
        <td><?= $user['name'] ?></td>
        <td><?= $user['email'] ?></td>
        <td><?= $user['phone'] ?></td>
        <td>
            <button
                class="btn btn-success mx-1 btn-choose-user"
                data-user-id="<?= $user['user_id'] ?>"
                data-name="<?= htmlspecialchars($user['name']) ?>"
                data-email="<?= htmlspecialchars($user['email']) ?>"
                data-phone="<?= htmlspecialchars($user['phone']) ?>">
                <i class="fa-regular fa-pen-to-square"></i>
                Chọn
            </button>
        </td>
    </tr>
<?php endforeach;

$userHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'userHtml' => $userHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
