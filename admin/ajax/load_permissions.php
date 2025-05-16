<?php
require_once '../../database/DBConnection.php';
require_once 'permission_helper.php';
$db = DBConnect::getInstance();

$role_id = $_GET['role_id'] ?? 2;

// Lấy toàn bộ permissions
$allPermissions = $db->select("SELECT * FROM permissions WHERE is_deleted = 0");

// Lấy các quyền hiện có của vai trò
$existingPermissions = $db->select("
    SELECT permission_id, action 
    FROM role_permission_details 
    WHERE role_id = ?
", [$role_id]);

// Tổ chức lại dữ liệu để truy cập nhanh
$permissionMap = [];
foreach ($existingPermissions as $ep) {
    $permissionMap[$ep['permission_id']][] = $ep['action'];
}

// Render bảng
ob_start();
foreach ($allPermissions as $perm):
    $pid = $perm['permission_id'];
    $actions = $permissionMap[$pid] ?? [];
?>
    <tr>
        <td><?= htmlspecialchars($perm['name']) ?></td>
        <?php foreach (['read', 'write', 'delete'] as $action): ?>
            <td class="text-center">
                <input type="checkbox"
                    name="permissions[<?= $pid ?>][]"
                    value="<?= $action ?>"
                    data-permission-id="<?= $pid ?>"
                    data-action="<?= $action ?>"
                    style="width: 18px; height: 18px;"
                    <?= in_array($action, $actions) ? 'checked' : '' ?>>
            </td>
        <?php endforeach; ?>
        <?php if (hasPermission('Quản lý quyền', 'delete')): ?>
            <td class="text-center">
                <button type="button"
                    class="btn btn-danger btn-sm btn-delete-permission d-inline-flex align-items-center justify-content-center"
                    data-role-id="<?= $role_id ?>"
                    data-permission-id="<?= $perm['permission_id'] ?>"
                    data-name="<?= htmlspecialchars($perm['name']) ?>">
                    <i class="fa fa-trash me-1"></i> Xóa
                </button>
            </td>
        <?php endif; ?>


    </tr>
<?php endforeach;

$permissionHtml = ob_get_clean();
echo json_encode(['success' => true, 'permissionHtml' => $permissionHtml]);
