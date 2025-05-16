<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../database/DBConnection.php';
require_once '../../layout/phantrang.php';
require_once 'permission_helper.php';

$db = DBConnect::getInstance();

$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params       = [];

// Lọc nhân viên theo role_id 2 hoặc 4
$whereClauses[] = 'u.role_id != 1';

// Tìm kiếm theo tên (nếu có)
$search_name = trim($_GET['search_name'] ?? '');
if ($search_name !== '') {
    $whereClauses[] = 'u.name LIKE ?';
    $params[]       = "%{$search_name}%";
}

// Ghép WHERE
$whereSql = count($whereClauses)
    ? 'WHERE ' . implode(' AND ', $whereClauses)
    : '';

// Query chính, JOIN với user_addresses lấy địa chỉ mặc định
$sql = "
    SELECT SQL_CALC_FOUND_ROWS
           u.user_id, u.name, u.email, u.password, u.phone, u.status, u.role_id,
           ua.province, ua.district, ua.ward, ua.address_detail, r.name as role_name
      FROM users u
    LEFT JOIN user_addresses ua ON u.user_id = ua.user_id AND ua.is_default = 1
    JOIN roles r ON r.role_id = u.role_id
    {$whereSql}
     LIMIT {$limit}
    OFFSET {$offset}
";
$staffs = $db->select($sql, $params);

// Lấy tổng số dòng
$totalResult = $db->select("SELECT FOUND_ROWS()");
$totalStaff  = $totalResult[0]['FOUND_ROWS()'];
$totalPages  = ceil($totalStaff / $limit);
$pagination  = new Pagination($totalStaff, $limit, $page);

ob_start();
if (empty($staffs)) {
    echo '<tr><td colspan="7">Không có nhân viên nào được tìm thấy.</td></tr>';
} else {
    foreach ($staffs as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['user_id']) ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= htmlspecialchars($s['phone']) ?></td>
            <td><?= htmlspecialchars($s['role_name']) ?></td>
            <td><?= $s['status'] == 1 ? 'Hoạt động' : 'Khóa' ?></td>
            <td>
                <!-- nút Sửa -->
                <?php if (
                    hasPermission('Quản lý nhân viên', 'write') &&
                    (
                        $s['role_id'] != 2 || // Cho phép sửa nếu KHÔNG phải là admin
                        ($_SESSION['role_id'] == 2 && $s['role_id'] == 2) // Nếu là admin thì được sửa admin
                    )
                ): ?>

                    <button class="btn btn-success btn-edit-staff mx-1"
                        data-id="<?= $s['user_id'] ?>"
                        data-name="<?= htmlspecialchars($s['name']) ?>"
                        data-email="<?= htmlspecialchars($s['email']) ?>"
                        data-password="<?= htmlspecialchars($s['password']) ?>"
                        data-phone="<?= htmlspecialchars($s['phone']) ?>"
                        data-province="<?= htmlspecialchars($s['province'] ?? '') ?>"
                        data-district="<?= htmlspecialchars($s['district'] ?? '') ?>"
                        data-ward="<?= htmlspecialchars($s['ward'] ?? '') ?>"
                        data-address-detail="<?= htmlspecialchars($s['address_detail'] ?? '') ?>"
                        data-status="<?= $s['status'] ?>"
                        data-role="<?= htmlspecialchars($s['role_name']) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalSuaNV">
                        <i class="fa-regular fa-pen-to-square"></i> Sửa
                    </button>
                <?php endif; ?>

                <!-- nút Xóa -->
                <?php if (
                    hasPermission('Quản lý nhân viên', 'delete') &&
                    (
                        $s['role_id'] != 2 ||
                        ($_SESSION['role_id'] == 2 && $s['role_id'] == 2)
                    )
                    && $_SESSION['admin_id'] != $s['user_id']
                ): ?>
                    <button class="btn btn-danger btn-delete-staff mx-1"
                        data-id="<?= $s['user_id'] ?>"
                        data-name="<?= htmlspecialchars($s['name']) ?>"
                        data-email="<?= htmlspecialchars($s['email']) ?>"
                        data-password="<?= htmlspecialchars($s['password']) ?>"
                        data-phone="<?= htmlspecialchars($s['phone']) ?>"
                        data-status="<?= $s['status'] ?>"
                        data-role="<?= htmlspecialchars($s['role_name']) ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#modalXoaNV">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                <?php endif; ?>

                <!-- nút Chi tiết -->
                <button style="color: white;" class="btn btn-info btn-detail-staff mx-1"
                    data-id="<?= $s['user_id'] ?>"
                    data-name="<?= htmlspecialchars($s['name']) ?>"
                    data-email="<?= htmlspecialchars($s['email']) ?>"
                    data-phone="<?= htmlspecialchars($s['phone']) ?>"
                    data-role="<?= htmlspecialchars($s['role_name']) ?>"
                    data-status="<?= $s['status'] ?>"
                    data-password="<?= htmlspecialchars($s['password']) ?>"
                    data-province="<?= htmlspecialchars($s['province'] ?? '') ?>"
                    data-district="<?= htmlspecialchars($s['district'] ?? '') ?>"
                    data-ward="<?= htmlspecialchars($s['ward'] ?? '') ?>"
                    data-address-detail="<?= htmlspecialchars($s['address_detail'] ?? '') ?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modalChiTietNV">
                    <i class="fa-regular fa-eye"></i> Chi tiết
                </button>
            </td>
        </tr>
<?php endforeach;
}
$staffHtml = ob_get_clean();

$paginationHtml = '';
if ($totalPages > 1) {
    ob_start();
    $pagination->render([]);
    $paginationHtml = ob_get_clean();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'staffHtml'  => $staffHtml,
    'pagination' => $paginationHtml
]);
