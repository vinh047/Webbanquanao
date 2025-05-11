<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';

$db = DBConnect::getInstance();


$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

$search_name = $_GET['search_name'] ?? '';
if (!empty($search_name)) {
    $whereClauses[] = 'name LIKE ?';
    $params[] = '%' . $search_name . '%';
}





// Gộp điều kiện WHERE
$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM orders $whereSql LIMIT $limit OFFSET $offset";
$orders = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalorder = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalorder / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalorder, $limit, $page);

ob_start();
foreach ($orders as $order): 
$user = $db->selectOne('SELECT * FROM users WHERE user_id = ?', [$order['user_id']]);
$payment_method = $db->selectOne('SELECT * FROM payment_method WHERE payment_method_id = ?', [$order['payment_method_id']]);
$staff = $db->selectOne('SELECT * FROM users WHERE user_id = ?', [$order['staff_id']]);
?>
    <tr>
        <td><?= $order['order_id'] ?></td>
        <td><?= $user['name'] ?></td>
        <td><?= $order['status'] ?></td>
        <td><?= $order['total_price'] ?></td>
        <td><?= $order['shipping_address'] ?></td>
        <td><?= $order['note'] ?></td>
        <td><?= $order['created_at'] ?></td>
        <td><?= $payment_method['name'] ?></td>
        <td><?= $staff['name'] ?></td>
        <td>
            <button class="btn btn-success mx-1 btn-edit-order"
                
                data-bs-toggle="modal"
                data-bs-target="#modalSuaNCC">
                <i class="fa-regular fa-pen-to-square"></i>
                Sửa
            </button>
            <button class="btn btn-danger btn-delete-order mx-1"
                
                data-bs-toggle="modal"
                data-bs-target="#modalXoaNCC">
                <i class="fas fa-trash"></i>
                Xóa
            </button>
        </td>
    </tr>
<?php endforeach;

$orderHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'orderHtml' => $orderHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
