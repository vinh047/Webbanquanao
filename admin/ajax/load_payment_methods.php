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

$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM payment_method $whereSql LIMIT $limit OFFSET $offset";
$payment_methods = $db->select($sql, $params);

$totalQuery = "SELECT FOUND_ROWS()";
$totalResult = $db->select($totalQuery);
$totalPaymentMethod = $totalResult[0]['FOUND_ROWS()'];

// Tổng số trang
$totalPages = ceil($totalPaymentMethod / $limit);

// Khởi tạo phân trang
$pagination = new Pagination($totalPaymentMethod, $limit, $page);

ob_start();
foreach ($payment_methods as $payment_method): ?>
    <tr>
        <td><?= $payment_method['payment_method_id'] ?></td>
        <td><?= $payment_method['name'] ?></td>
        <td>
            <?php if (hasPermission('Quản lý thuộc tính', 'write')): ?>

                <button class="btn btn-success mx-1 btn-edit-payment-method"
                    data-id="<?= $payment_method['payment_method_id'] ?>"
                    data-name="<?= $payment_method['name'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalSuaPTTT">
                    <i class="fa-regular fa-pen-to-square"></i>
                    Sửa
                </button>
            <?php endif; ?>
            <?php if (hasPermission('Quản lý thuộc tính', 'delete')): ?>

                <button class="btn btn-danger btn-delete-payment-method mx-1"
                    data-id="<?= $payment_method['payment_method_id'] ?>"
                    data-name="<?= $payment_method['name'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalXoaPTTT">
                    <i class="fas fa-trash"></i>
                    Xóa
                </button>
            <?php endif; ?>

        </td>
    </tr>
<?php endforeach;

$paymentMethodHtml = ob_get_clean();

$paginationHtml = null;

if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

$data = [
    'paymentMethodHtml' => $paymentMethodHtml,
    'pagination' => $paginationHtml
];

header('Content-Type: application/json');
echo json_encode($data);
