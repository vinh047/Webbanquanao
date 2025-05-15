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
<style>
.top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 1rem;
}

/* Khung chứa nhóm tìm kiếm + filter + xóa lọc */
.search-filter-group {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  width: 420px;  /* bạn chỉnh phù hợp */
  margin: 0 auto;
}

/* Input tìm kiếm chiếm hết chỗ còn lại */
#searchVoucher {
  flex: 1;
}

/* Để nút Thêm ở bên trái, nhóm tìm kiếm nằm giữa, bên phải giữ khoảng trống */
.left-group {
  flex-shrink: 0;
}
.right-group {
  flex-shrink: 0;
  width: 100px; /* khoảng trống bên phải nếu cần */
}
</style>

<div class="top-bar">
  <div class="left-group">
    <button class="btn btn-primary rounded-2" data-bs-toggle="modal" data-bs-target="#modalThemVoucher">
      <i class="fa-solid fa-plus me-1"></i> Thêm voucher
    </button>
  </div>

  <div class="search-filter-group">
    <input type="text" class="form-control" placeholder="Tìm kiếm mã voucher" id="searchVoucher" />
    <button class="btn btn-secondary" id="btnSearchVoucher" type="button" style="pointer-events:auto;">
      <i class="fa-solid fa-search"></i>
    </button>
    <button class="btn btn-outline-secondary rounded" id="btnToggleFilterVoucher">
      <i class="fa fa-filter"></i>
    </button>
    <button type="button" class="btn btn-outline-secondary" id="btnClearFilterVoucher">
      <i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc
    </button>
  </div>

  <div class="right-group">
    <!-- Bạn có thể đặt thêm nút hoặc giữ khoảng trống -->
  </div>
</div>


<!-- Form lọc nâng cao -->
<form method="GET" class="form-voucher-search d-none container my-3">
    <div class="row g-3 justify-content-center">
        <div class="col-md-2">
            <div class="form-floating">
                <input type="date" class="form-control" id="from_date_voucher" name="from_date" value="<?= $_GET['from'] ?? '' ?>">
                <label for="from_date_voucher">Từ ngày</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating">
            <input type="date" class="form-control" id="to_date_voucher" name="to_date" value="<?= isset($_GET['to']) ? htmlspecialchars($_GET['to']) : '' ?>">
                <label for="to_date_voucher">Đến ngày</label>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" name="status" id="status_voucher">
                    <option value="" <?= empty($_GET['status']) ? 'selected' : '' ?>>Tất cả</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Hiệu lực</option>
                    <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Hết hạn</option>
                </select>
                <label for="status_voucher">Trạng thái</label>
            </div>
        </div>
    </div>
</form>

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
                    <td class="d-flex justify-content-center gap-2">
                        <button class="btn btn-success rounded-2 px-3" data-bs-toggle="modal" data-bs-target="#editModal<?= $voucher['voucher_id'] ?>">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Sửa
                        </button>
                        <button class="btn btn-danger rounded-2 px-3" onclick="deleteVoucher(<?= $voucher['voucher_id'] ?>)">
                            <i class="fa-solid fa-trash me-1"></i> Xoá
                        </button>
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
                        <input type="date" class="form-control" name="start_date" id="edit_start_date_<?= $voucher['voucher_id'] ?>" value="<?= $voucher['start_date'] ?>" required>
                        <label>Ngày bắt đầu</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" name="end_date" id="edit_end_date_<?= $voucher['voucher_id'] ?>" value="<?= $voucher['end_date'] ?>" required>
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
                        <input type="text" class="form-control" id="edit_status_text_<?= $voucher['voucher_id'] ?>" value="<?= $status_text ?>" readonly>
                        <label>Trạng thái</label>
                    </div>
                    <input type="hidden" name="status" id="edit_status_value_<?= $voucher['voucher_id'] ?>" value="<?= $status_value ?>">
                    <?php if ($voucher['end_date'] < $today): ?>
                        <div class="form-text text-danger">Voucher đã hết hạn, không thể sửa trạng thái.</div>
                    <?php endif; ?>
                </div>
                                                <div class="modal-footer d-flex justify-content-between">
                                    <button type="submit" class="btn btn-outline-warning rounded-2">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Lưu thay đổi
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary rounded-2" data-bs-dismiss="modal">
                                        <i class="fa-solid fa-xmark me-1"></i> Hủy
                                    </button>
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
                        <input type="date" class="form-control" name="start_date" id="add_start_date" required>
                        <label>Ngày bắt đầu</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" name="end_date" id="add_end_date" required>
                        <label>Ngày kết thúc</label>
                    </div>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="add_status_text" value="Hiệu lực" readonly>
                        <label>Trạng thái</label>
                    </div>
                        <input type="hidden" name="status" id="add_status_value" value="active">
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="submit" class="btn btn-outline-primary rounded-2">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Thêm
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-2" data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark me-1"></i> Hủy
                        </button>
                    </div>
            </form>
        </div>
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
function updateVoucherStatus(startInput, endInput, statusTextInput, statusValueInput) {
    const today = new Date();
    const startDate = new Date(startInput.value);
    const endDate = new Date(endInput.value);

    if (!startInput.value || !endInput.value) {
        statusTextInput.value = '';
        statusValueInput.value = '';
        return;
    }

    if (endDate < today) {
        statusTextInput.value = 'Hết hạn';
        statusValueInput.value = 'inactive';
    } else if (startDate > today) {
        statusTextInput.value = 'Chưa hiệu lực';
        statusValueInput.value = 'inactive';
    } else {
        statusTextInput.value = 'Hiệu lực';
        statusValueInput.value = 'active';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Cập nhật trạng thái tự động cho modal Thêm Voucher
    const addStartDate = document.getElementById('add_start_date');
    const addEndDate = document.getElementById('add_end_date');
    const addStatusText = document.getElementById('add_status_text');
    const addStatusValue = document.getElementById('add_status_value');

    if (addStartDate && addEndDate && addStatusText && addStatusValue) {
        addStartDate.addEventListener('change', () => updateVoucherStatus(addStartDate, addEndDate, addStatusText, addStatusValue));
        addEndDate.addEventListener('change', () => updateVoucherStatus(addStartDate, addEndDate, addStatusText, addStatusValue));
    }

    // Cập nhật trạng thái tự động cho tất cả modal Sửa Voucher
    document.querySelectorAll('div.modal[id^="editModal"]').forEach(modal => {
        const voucherId = modal.id.replace('editModal', '');

        const startInput = document.getElementById('edit_start_date_' + voucherId);
        const endInput = document.getElementById('edit_end_date_' + voucherId);
        const statusTextInput = document.getElementById('edit_status_text_' + voucherId);
        const statusValueInput = document.getElementById('edit_status_value_' + voucherId);

        if (startInput && endInput && statusTextInput && statusValueInput) {
            startInput.addEventListener('change', () => updateVoucherStatus(startInput, endInput, statusTextInput, statusValueInput));
            endInput.addEventListener('change', () => updateVoucherStatus(startInput, endInput, statusTextInput, statusValueInput));
        }
    });
    
});
function deleteVoucher(id) {
    if (!confirm("Xác nhận xoá voucher này?")) return;
    fetch(`ajax/handle_voucher.php?delete=${id}`)
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        })
        .catch(() => alert("Lỗi xóa voucher"));
}
let currentVoucherFilter = '';

function handleVoucherFilter() {
    const keyword = document.getElementById('searchVoucher').value.trim();
    const from = document.getElementById('from_date_voucher').value;
    const to = document.getElementById('to_date_voucher').value;
    const status = document.getElementById('status_voucher').value;

    const query = new URLSearchParams();
    if (keyword) query.append('search', keyword);
    if (from) query.append('from', from);
    if (to) query.append('to', to);
    if (status) query.append('status', status);

    window.location.href = `index.php?page=vouchers&${query.toString()}`;
}

// Sự kiện:
// document.getElementById('searchVoucher').addEventListener('input', handleVoucherFilter);
document.getElementById('btnSearchVoucher').addEventListener('click', handleVoucherFilter);
['from_date_voucher', 'to_date_voucher', 'status_voucher'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('change', handleVoucherFilter);
});

document.getElementById('btnClearFilterVoucher').addEventListener('click', () => {
    const form = document.querySelector('.form-voucher-search');
    form.reset();
    document.getElementById('searchVoucher').value = '';

    // Reset các select thủ công nếu cần (một số trình duyệt không reset mặc định)
    ['status_voucher'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    handleVoucherFilter();

    // Ẩn phần lọc nâng cao
    form.classList.add('d-none');
    localStorage.setItem('voucherFilterVisible', '0');
});


document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.form-voucher-search');

    // Lấy trạng thái từ localStorage
    const visible = localStorage.getItem('voucherFilterVisible') === '1';
    if (visible) form.classList.remove('d-none');

    document.getElementById('btnToggleFilterVoucher').addEventListener('click', () => {
        const isVisible = !form.classList.contains('d-none');
        if (isVisible) {
            form.classList.add('d-none');
            localStorage.setItem('voucherFilterVisible', '0');
        } else {
            form.classList.remove('d-none');
            localStorage.setItem('voucherFilterVisible', '1');
        }
    });
});


</script>
