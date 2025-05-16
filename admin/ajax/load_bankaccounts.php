<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';
require_once 'permission_helper.php';

$db = DBConnect::getInstance();

// 1. Thiết lập phân trang
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Build điều kiện WHERE & params
$whereClauses = [];
$params       = [];

$search = trim($_GET['search_name'] ?? '');
if ($search !== '') {
    $whereClauses[] = '(bank_code LIKE ? OR account_number LIKE ? OR account_name LIKE ?)';
    $like = "%{$search}%";
    $params = [$like, $like, $like];
}

$whereSql = count($whereClauses)
    ? 'WHERE ' . implode(' AND ', $whereClauses)
    : '';

// 3. Truy vấn chính
$sql = "
    SELECT SQL_CALC_FOUND_ROWS *
    FROM bank_account
    $whereSql
    ORDER BY account_id DESC
    LIMIT {$limit} OFFSET {$offset}
";
$accounts = $db->select($sql, $params);

// 4. Lấy tổng số dòng
$totalQuery   = "SELECT FOUND_ROWS() AS cnt";
$totalResult  = $db->select($totalQuery);
$totalItems   = (int)($totalResult[0]['cnt'] ?? 0);
$totalPages   = max(1, ceil($totalItems / $limit));

// 5. Tạo HTML bảng
ob_start();
foreach ($accounts as $a): ?>
    <tr>
        <td><?= $a['account_id'] ?></td>
        <td><?= htmlspecialchars($a['bank_code']) ?></td>
        <td><?= htmlspecialchars($a['account_number']) ?></td>
        <td><?= htmlspecialchars($a['account_name']) ?></td>
        <td><?= $a['is_active'] ? 'Hoạt động' : 'Ngừng hoạt động' ?></td>
        <td>
            <?php if ($a['is_default']): ?>
                <span class="badge bg-success">Mặc định</span>
            <?php else: ?>
                <span class="text-muted">-</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if (hasPermission('Quản lý tài khoản ngân hàng', 'write')): ?>
                <button class="btn btn-edit-bankaccount text-white me-1"
                    style="background-color: #198745; border: none; border-radius: 12px; padding: 6px 12px;"
                    data-id="<?= $a['account_id'] ?>"
                    data-bank-code="<?= htmlspecialchars($a['bank_code'], ENT_QUOTES) ?>"
                    data-account-number="<?= htmlspecialchars($a['account_number'], ENT_QUOTES) ?>"
                    data-account-name="<?= htmlspecialchars($a['account_name'], ENT_QUOTES) ?>"
                    data-is-active="<?= $a['is_active'] ?>"
                    data-is-default="<?= $a['is_default'] ?>">
                    <i class="fas fa-pen-to-square me-1"></i> Sửa
                </button>
            <?php endif; ?>

            <?php if (hasPermission('Quản lý tài khoản ngân hàng', 'delete')): ?>
                <button class="btn btn-delete-bankaccount text-white"
                    style="background-color: #dc3545; border: none; border-radius: 12px; padding: 6px 12px;"
                    data-id="<?= $a['account_id'] ?>"
                    data-bank-code="<?= htmlspecialchars($a['bank_code'], ENT_QUOTES) ?>"
                    data-account-number="<?= htmlspecialchars($a['account_number'], ENT_QUOTES) ?>"
                    data-account-name="<?= htmlspecialchars($a['account_name'], ENT_QUOTES) ?>">
                    <i class="fas fa-trash me-1"></i> Xóa
                </button>
            <?php endif; ?>

        </td>
    </tr>
<?php endforeach;
$bankaccountHtml = ob_get_clean();

// 6. Phân trang
$paginationHtml = '';
if ($totalPages > 1) {
    ob_start();
    $pagination = new Pagination($totalItems, $limit, $page);
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

// 7. Trả JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'bankaccountHtml' => $bankaccountHtml,
    'pagination'      => $paginationHtml
]);
