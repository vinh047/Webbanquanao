<?php
require_once '../database/DBConnection.php';
require_once '../layout/phantrang.php';
require_once '../utils/permission_helper.php';

$db = DBConnect::getInstance();

// cấu hình phân trang
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params       = [];

// Bộ lọc nâng cao
if (!empty($_GET['search'])) {
    $whereClauses[] = 'code LIKE ?';
    $params[]       = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['status'])) {
    $whereClauses[] = 'status = ?';
    $params[]       = $_GET['status'];
}
if (!empty($_GET['from'])) {
    $whereClauses[] = 'start_date >= ?';
    $params[]       = $_GET['from'];
}
if (!empty($_GET['to'])) {
    $whereClauses[] = 'end_date <= ?';
    $params[]       = $_GET['to'];
}

$whereSql = count($whereClauses)
    ? 'WHERE ' . implode(' AND ', $whereClauses)
    : '';

$sql = "
    SELECT SQL_CALC_FOUND_ROWS *
    FROM vouchers
    $whereSql
    ORDER BY voucher_id ASC
    LIMIT $limit OFFSET $offset
";
$vouchers = $db->select($sql, $params);
$totalResult = $db->select("SELECT FOUND_ROWS() as total");
$total       = $totalResult[0]['total'] ?? 0;
$pagination  = new Pagination($total, $limit, $page);

// render HTML rows
ob_start();
foreach ($vouchers as $v): ?>
<tr>
    <td><?= $v['voucher_id'] ?></td>
    <td><?= htmlspecialchars($v['code']) ?></td>
    <td><?= $v['discount'] ?></td>
    <td><?= $v['start_date'] ?></td>
    <td><?= $v['end_date'] ?></td>
    <td>
        <?php
        $today = date('Y-m-d');
        echo ($v['status'] === 'inactive' || $v['end_date'] < $today)
            ? '<span class="badge bg-danger">Hết hạn</span>'
            : '<span class="badge bg-success">Hiệu lực</span>';
        ?>
    </td>
    <td>
        <?php if (hasPermission('Quản lý thuộc tính', 'write')): ?>
            <button class="btn btn-sm btn-success" onclick="openEditVoucher(<?= $v['voucher_id'] ?>)"><i class="fa fa-pen"></i></button>
        <?php endif; ?>
        <?php if (hasPermission('Quản lý thuộc tính', 'delete')): ?>
            <button class="btn btn-sm btn-danger" onclick="deleteVoucher(<?= $v['voucher_id'] ?>)"><i class="fa fa-trash"></i></button>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach;
$tableHtml = ob_get_clean();

ob_start();
$pagination->render([]);
$paginationHtml = ob_get_clean();

header('Content-Type: application/json');
echo json_encode([
    'tableHtml' => $tableHtml,
    'pagination' => $paginationHtml
]);