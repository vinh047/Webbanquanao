<?php
require_once '../database/DBConnection.php';
$db = DBConnect::getInstance();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->execute("DELETE FROM vouchers WHERE voucher_id = ?", [$_GET['delete']]);
    header("Location: index.php?page=vouchers&pageadmin=1");
    exit;
}

// Filters
$filter = '';
$params = [];


if (!empty($_GET['search'])) {
    $filter .= ' AND code LIKE ?';
    $params[] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['status'])) {
    $filter .= ' AND status = ?';
    $params[] = $_GET['status'];
}
if (!empty($_GET['from'])) {
    $filter .= ' AND start_date >= ?';
    $params[] = $_GET['from'];
}
if (!empty($_GET['to'])) {
    $filter .= ' AND end_date <= ?';
    $params[] = $_GET['to'];
}

$sql = "SELECT * FROM vouchers WHERE 1 $filter ORDER BY voucher_id ASC";
$vouchers = $db->select($sql, $params);
?>

<!-- UI Render -->
<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalThemVoucher">
        <i class="fa-solid fa-plus"></i> Thêm
    </button>
    <form method="GET" class="d-flex align-items-center gap-2">
    <input type="hidden" name="page" value="vouchers">
        <input type="text" class="form-control" name="search" placeholder="Tìm mã voucher" value="<?= $_GET['search'] ?? '' ?>" style="width: 200px">
        <select class="form-select" name="status" style="width: 150px">
            <option value="">Tất cả</option>
            <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hiệu lực</option>
            <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Hết hạn</option>
        </select>
        <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
        <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
        <button class="btn btn-secondary"><i class="fa fa-search"></i></button>
        <a href="index.php?page=vouchers&pageadmin=1" class="btn btn-outline-secondary">Xóa lọc</a>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Mã Voucher</th>
                <th>Giảm giá (%)</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vouchers as $voucher): ?>
                <tr>
                    <td><?= $voucher['voucher_id'] ?></td>
                    <td><?= htmlspecialchars($voucher['code']) ?></td>
                    <td><?= $voucher['discount'] ?></td>
                    <td><?= $voucher['start_date'] ?></td>
                    <td><?= $voucher['end_date'] ?></td>
                    <td>
                        <?php
                        $today = date('Y-m-d');
                        echo ($voucher['status'] === 'inactive' || $voucher['end_date'] < $today)
                            ? '<span class="badge bg-danger">Hết hạn</span>'
                            : '<span class="badge bg-success">Hiệu lực</span>';
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $voucher['voucher_id'] ?>">Sửa</button>
                        <a class="btn btn-sm btn-danger" href="ajax/handle_voucher.php?delete=<?= $voucher['voucher_id'] ?>" onclick="return confirm('Xác nhận xoá voucher này?')">Xoá</a>
                    </td>
                </tr>

                <!-- Modal Sửa Voucher -->
                <div class="modal fade" id="editModal<?= $voucher['voucher_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST" action="ajax/handle_voucher.php" class="voucher-form">
                            <input type="hidden" name="voucher_id" value="<?= $voucher['voucher_id'] ?>">
                            <input type="hidden" name="update_voucher" value="1">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Sửa Voucher</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="code" value="<?= htmlspecialchars($voucher['code']) ?>" required>
                                        <label>Mã voucher</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" name="discount" value="<?= $voucher['discount'] ?>" min="1" max="100" required>
                                        <label>Giảm giá (%)</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" name="start_date" value="<?= $voucher['start_date'] ?>" required>
                                        <label>Ngày bắt đầu</label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" name="end_date" value="<?= $voucher['end_date'] ?>" required>
                                        <label>Ngày kết thúc</label>
                                    </div>
                                    <?php
$today = date('Y-m-d');
if ($voucher['end_date'] < $today) {
    $status_text = 'Hết hạn';
    $status_value = 'inactive';
} elseif ($voucher['start_date'] > $today) {
    $status_text = 'Chưa hiệu lực';
    $status_value = 'inactive';
} else {
    $status_text = 'Hiệu lực';
    $status_value = 'active';
}
?>
<div class="form-floating">
    <input type="text" class="form-control" value="<?= $status_text ?>" readonly>
    <label>Trạng thái</label>
</div>
<input type="hidden" name="status" value="<?= $status_value ?>">
<?php if ($voucher['end_date'] < $today): ?>
    <div class="form-text text-danger">Voucher đã hết hạn, không thể sửa trạng thái.</div>
<?php endif; ?>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Voucher -->
<div class="modal fade" id="modalThemVoucher" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="ajax/handle_voucher.php" class="voucher-form">
            <input type="hidden" name="add_voucher" value="1">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Thêm Voucher Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="code" required>
                        <label>Mã voucher</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="discount" min="1" max="100" required>
                        <label>Giảm giá (%)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" name="start_date" required>
                        <label>Ngày bắt đầu</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" name="end_date" required>
                        <label>Ngày kết thúc</label>
                    </div>
                    <?php
$today = date('Y-m-d');

if ($voucher['end_date'] < $today) {
    $status_text = 'Hết hạn';
    $status_value = 'inactive';
    $is_disabled = true; // có thể dùng để disable các input khác nếu cần
} elseif ($voucher['start_date'] > $today) {
    $status_text = 'Chưa hiệu lực';
    $status_value = 'inactive';
    $is_disabled = true;
} else {
    $status_text = 'Hiệu lực';
    $status_value = 'active';
    $is_disabled = false;
}
?>

<div class="form-floating">
    <input type="text" class="form-control" value="<?= htmlspecialchars($status_text) ?>" readonly>
    <label>Trạng thái</label>
</div>
<input type="hidden" name="status" value="<?= htmlspecialchars($status_value) ?>">

<?php if ($voucher['end_date'] < $today): ?>
    <div class="form-text text-danger">Voucher đã hết hạn, không thể sửa trạng thái.</div>
<?php endif; ?>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelectorAll('.voucher-form').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault(); // chặn submit mặc định (reload trang)
        
        if (!form.action) {
            alert('Form chưa có action, vui lòng kiểm tra!');
            return;
        }

        let submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        let formData = new FormData(form);
        try {
            let res = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            if (!res.ok) {
                alert('Lỗi kết nối đến server!');
                return;
            }

            let data;
            try {
                data = await res.json();
            } catch {
                alert('Phản hồi từ server không hợp lệ!');
                return;
            }

            if (data.success) {
                // Ẩn modal
                let modalEl = form.closest('.modal');
                let modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                // Reload lại trang để cập nhật data mới
                location.reload();
            } else {
                alert(data.message || 'Lỗi xảy ra, vui lòng thử lại!');
            }
        } catch (err) {
            alert('Lỗi hệ thống, vui lòng thử lại!');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});

</script>
