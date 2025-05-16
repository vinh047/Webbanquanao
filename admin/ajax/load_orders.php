<?php

require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';
require_once 'permission_helper.php';



$db = DBConnect::getInstance();

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

// Lọc theo tên đơn hàng
$search_name = $_GET['search_name'] ?? '';
if (!empty($search_name)) {
    $whereClauses[] = 'o.order_id LIKE ?';
    $params[] = '%' . $search_name . '%';
}

// Tổng giá từ
if (!empty($_GET['price_min'])) {
    $whereClauses[] = 'o.total_price >= ?';
    $params[] = (float)$_GET['price_min'];
}

// Tổng giá đến
if (!empty($_GET['price_max'])) {
    $whereClauses[] = 'o.total_price <= ?';
    $params[] = (float)$_GET['price_max'];
}

// Trạng thái đơn hàng
if (!empty($_GET['status'])) {
    $whereClauses[] = 'o.status = ?';
    $params[] = $_GET['status'];
}

// Từ ngày
if (!empty($_GET['from_date'])) {
    $whereClauses[] = 'DATE(o.created_at) >= ?';
    $params[] = $_GET['from_date'];
}

// Đến ngày
if (!empty($_GET['to_date'])) {
    $whereClauses[] = 'DATE(o.created_at) <= ?';
    $params[] = $_GET['to_date'];
}

// Phương thức thanh toán
if (!empty($_GET['payment_method'])) {
    $whereClauses[] = 'o.payment_method_id = ?';
    $params[] = $_GET['payment_method'];
}

// Lọc theo khách hàng
if (!empty($_GET['user'])) {
    $whereClauses[] = 'u1.name LIKE ?';
    $params[] =  '%' . $_GET['user'] . '%';
}

// Lọc theo nhân viên tạo đơn
if (!empty($_GET['staff'])) {
    $whereClauses[] = 'u2.name LIKE ?';
    $params[] =  '%' . $_GET['staff'] . '%';
}



// Gộp điều kiện WHERE
$whereSql = count($whereClauses) > 0 ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

$sql = "SELECT SQL_CALC_FOUND_ROWS o.*
        FROM orders o
        JOIN users u1 ON o.user_id = u1.user_id
        LEFT JOIN users u2 ON o.staff_id = u2.user_id
        $whereSql
        ORDER BY o.created_at DESC
        LIMIT $limit OFFSET $offset";
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
    $staff = null;
    if (!empty($order['staff_id'])) {
        $staff = $db->selectOne('SELECT * FROM users WHERE user_id = ?', [$order['staff_id']]);
    }
?>
    <tr>
        <td><?= $order['order_id'] ?></td>
        <td><?= $user['name'] ?></td>
        <td><?= $order['status'] ?></td>
        <td><?= number_format($order['total_price'], 0, ',', '.') ?> ₫</td>
        <td><?= $order['shipping_address'] ?></td>
        <td><?= $order['note'] ?></td>
        <td><?= $order['created_at'] ?></td>
        <td><?= $payment_method['name'] ?></td>
        <td><?= $staff ? htmlspecialchars($staff['name']) : '' ?></td>
        <td class="d-flex align-items-center justify-content-center">
            <button class="btn btn-info mx-1 btn-view-order d-flex align-items-center" style="white-space: nowrap;"
                data-order-id="<?= $order['order_id'] ?>"
                data-user-id="<?= $user['user_id'] ?>"
                data-user-name="<?= htmlspecialchars($user['name']) ?>"
                data-status="<?= htmlspecialchars($order['status']) ?>"
                data-total-price="<?= $order['total_price'] ?>"
                data-shipping-address="<?= htmlspecialchars($order['shipping_address']) ?>"
                data-note="<?= htmlspecialchars($order['note']) ?>"
                data-created-at="<?= $order['created_at'] ?>"
                data-payment-method-id="<?= $payment_method['payment_method_id'] ?>"
                data-payment-method-name="<?= htmlspecialchars($payment_method['name']) ?>"
                data-staff-id="<?= $staff ? htmlspecialchars($staff['user_id']) : '' ?>"
                data-staff-name=" <?= $staff ? htmlspecialchars($staff['name']) : '' ?>">
                <i class="fa-regular fa-eye me-1"></i>
                Chi tiết
            </button>
            <?php if (hasPermission('Quản lý đơn hàng', 'write')): ?>
                <button class="btn btn-success mx-1 btn-edit-order d-flex align-items-center"
                    style="<?= ($order['status'] == 'Đã hủy') ? 'display:none !important;' : '' ?>"
                    data-order-id="<?= $order['order_id'] ?>"
                    data-user-id="<?= $user['user_id'] ?>"
                    data-user-name="<?= htmlspecialchars($user['name']) ?>"
                    data-status="<?= htmlspecialchars($order['status']) ?>"
                    data-total-price="<?= $order['total_price'] ?>"
                    data-shipping-address="<?= htmlspecialchars($order['shipping_address']) ?>"
                    data-note="<?= htmlspecialchars($order['note']) ?>"
                    data-created-at="<?= $order['created_at'] ?>"
                    data-payment-method-id="<?= $payment_method['payment_method_id'] ?>"
                    data-payment-method-name="<?= htmlspecialchars($payment_method['name']) ?>"
                    data-staff-id="<?= $staff ? htmlspecialchars($staff['user_id']) : '' ?>"
                    data-staff-name=" <?= $staff ? htmlspecialchars($staff['name']) : '' ?>">
                    <i class="fa-regular fa-pen-to-square me-1"></i>
                    Sửa
                </button>
            <?php endif; ?>
            <?php if (hasPermission('Quản lý đơn hàng', 'delete')): ?>
                <button class="btn btn-danger btn-delete-order mx-1 d-flex align-items-center"
                    style="<?= ($order['status'] !== 'Chờ xác nhận') ? 'display:none !important;' : '' ?> white-space: nowrap;"
                    data-order-id="<?= $order['order_id'] ?>"
                    data-user-id="<?= $user['user_id'] ?>"
                    data-user-name="<?= htmlspecialchars($user['name']) ?>"
                    data-status="<?= htmlspecialchars($order['status']) ?>"
                    data-total-price="<?= $order['total_price'] ?>"
                    data-shipping-address="<?= htmlspecialchars($order['shipping_address']) ?>"
                    data-note="<?= htmlspecialchars($order['note']) ?>"
                    data-created-at="<?= $order['created_at'] ?>"
                    data-payment-method-id="<?= $payment_method['payment_method_id'] ?>"
                    data-payment-method-name="<?= htmlspecialchars($payment_method['name']) ?>"
                    data-staff-id="<?= $staff ? htmlspecialchars($staff['user_id']) : '' ?>"
                    data-staff-name=" <?= $staff ? htmlspecialchars($staff['name']) : '' ?>">
                    <i class="fas fa-trash me-1"></i>
                    Hủy đơn
                </button>
            <?php endif; ?>
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
