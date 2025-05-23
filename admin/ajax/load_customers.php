<?php
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';
require_once 'permission_helper.php';

$db = DBConnect::getInstance();

// cấu hình phân trang
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params       = [];

// 1) Chỉ lấy user có role_id = 1 (khách hàng)
$whereClauses[] = 'role_id = 1';

// 2) Tìm theo tên (nếu có)
$search_name = trim($_GET['search_name'] ?? '');
if ($search_name !== '') {
    $whereClauses[] = 'name LIKE ?';
    $params[]      = "%{$search_name}%";
}

// ghép WHERE
$whereSql = count($whereClauses)
    ? 'WHERE ' . implode(' AND ', $whereClauses)
    : '';

// truy vấn chính với SQL_CALC_FOUND_ROWS
$sql = "
    SELECT SQL_CALC_FOUND_ROWS
           user_id, name, email, password, phone, status
      FROM users
    {$whereSql}
     LIMIT {$limit}
    OFFSET {$offset}
";
$customers = $db->select($sql, $params);

// lấy tổng số dòng không phân trang
$totalResult     = $db->select("SELECT FOUND_ROWS()");
$totalCustomers  = $totalResult[0]['FOUND_ROWS()'];
$totalPages      = ceil($totalCustomers / $limit);
$pagination      = new Pagination($totalCustomers, $limit, $page);

// render HTML rows
ob_start();
foreach ($customers as $c): ?>
    <tr>
        <td><?= htmlspecialchars($c['user_id']) ?></td>
        <td><?= htmlspecialchars($c['name'])    ?></td>
        <td><?= htmlspecialchars($c['email'])   ?></td>
        <td><?= htmlspecialchars($c['password']) ?></td>
        <td><?= htmlspecialchars($c['phone'])   ?></td>
        <td>
            <?= $c['status'] == 1 ? 'Hoạt động' : 'Khóa' ?>
        </td>
        <td>
            <!-- nút mở/khóa -->
            <?php if (hasPermission('Quản lý khách hàng', 'write')): ?>
                <button class="btn btn-warning btn-toggle-status-customer mx-1"
                    data-id="<?= $c['user_id'] ?>"
                    data-status="<?= $c['status'] ?>">
                    <?php if ($c['status'] == 1): ?>
                        <i class="fa-solid fa-lock"></i> Khóa tài khoản
                    <?php else: ?>
                        <i class="fa-solid fa-lock-open"></i> Mở khóa tài khoản
                    <?php endif; ?>
                </button>
            <?php endif; ?>

            <!-- nút Xóa -->

            <?php if (hasPermission('Quản lý khách hàng', 'delete')): ?>
                <button
                    class="btn btn-danger btn-delete-customer mx-1"
                    data-id="<?= $c['user_id']   ?>"
                    data-name="<?= htmlspecialchars($c['name'])  ?>"
                    data-email="<?= htmlspecialchars($c['email']) ?>"
                    data-password="<?= htmlspecialchars($c['password']) ?>"
                    data-phone="<?= htmlspecialchars($c['phone']) ?>"
                    data-status="<?= $c['status'] ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalXoaKH">
                    <i class="fas fa-trash"></i> Xóa
                </button>
            <?php endif; ?>


            <!-- nút chi tiết -->
            <button style="color: white;" class="btn btn-info btn-detail-customer mx-1"
                data-id="<?= $c['user_id']   ?>"
                data-name="<?= htmlspecialchars($c['name'])     ?>"
                data-email="<?= htmlspecialchars($c['email'])    ?>"
                data-password="<?= htmlspecialchars($c['password']) ?>"
                data-phone="<?= htmlspecialchars($c['phone'])    ?>"
                data-status="<?= $c['status'] ?>"
                data-bs-toggle="modal"
                data-bs-target="#modalChiTietKH">
                <i class="fa-regular fa-eye"></i> Chi tiết

            </button>
        </td>
    </tr>
<?php endforeach;
$customerHtml = ob_get_clean();

// render pagination nếu cần
$paginationHtml = '';
if ($totalPages > 1) {
    ob_start();
    // bạn có thể truyền các param cho Pagination nếu cần
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

// trả về JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'customerHtml' => $customerHtml,
    'pagination'   => $paginationHtml
]);
?>