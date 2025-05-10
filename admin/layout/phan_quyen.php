<?php
require_once '../database/DBConnection.php';

$db = DBConnect::getInstance();

$roles = $db->select('SELECT * FROM roles WHERE role_id != 1 AND is_deleted = 0', []);

?>

<style>
    /* Khi checkbox đang bị readonly (không chỉnh sửa được) */
    .readonly-permission input[type="checkbox"] {
        pointer-events: none;
        accent-color: #ccc !important;
        /* xám nhạt */
        opacity: 0.7;
    }

    /* Khi checkbox được mở để chỉnh sửa */
    input[type="checkbox"]:not(:disabled) {
        accent-color: #198754;
        /* xanh Bootstrap */
        cursor: pointer;
    }

    /* Làm mờ và disable nút Xóa khi chưa được chỉnh */
    .readonly-permission .btn-delete-permission {
        opacity: 0.5;
        pointer-events: none;
    }

    /* Khi đang chỉnh sửa → cho phép click */
    .btn-delete-permission {
        transition: opacity 0.2s ease;
    }
</style>


<div class="container my-4">
    <h4 class="fw-bold text-primary mb-3">Quản lý phân quyền</h4>

    <form id="formPermission" class="border rounded p-3 bg-light" onsubmit="return handlePermissionSubmit(event)">
        <div class="row mb-3 align-items-end">
            <div class="col-md-3">
                <label for="roleSelect" class="form-label">Chọn vai trò</label>
                <select id="roleSelect" name="role_id" class="form-select">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto" id="deleteRoleWrapper">
                <button type="button" id="btnDeleteRole" class="btn btn-outline-danger">
                    <i class="fa fa-trash me-1"></i> Xóa vai trò
                </button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-secondary">
                    <tr>
                        <th>Tên chức năng</th>
                        <th>Read</th>
                        <th>Write</th>
                        <th>Delete</th>
                        <th>Chức năng</th>
                    </tr>
                </thead>
                <tbody id="permissionTable">
                    <!-- Dữ liệu từ JS/AJAX -->
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3" id="actionButtons">
            <button type="button" id="btnEdit" class="btn btn-warning">Thay đổi</button>

            <button type="submit" id="btnSave" class="btn btn-primary d-none">Lưu phân quyền</button>
            <button type="button" id="btnCancel" class="btn btn-secondary d-none">Hủy</button>
        </div>
    </form>
</div>

<!-- Modal 1: Xác nhận bước đầu -->
<div class="modal fade" id="modalConfirmDelete1" tabindex="-1" aria-labelledby="modalConfirmDelete1Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalConfirmDelete1Label">Xác nhận xóa vai trò</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa vai trò "<span id="roleName1" class="fw-bold text-danger"></span>"?
            </div>
            <div class="modal-footer">
                <button type="button" id="btnNextConfirm" class="btn btn-danger">Tiếp tục</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Cảnh báo cuối cùng -->
<div class="modal fade" id="modalConfirmDelete2" tabindex="-1" aria-labelledby="modalConfirmDelete2Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmDelete2Label">Xác nhận xóa vĩnh viễn</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <p>
                    Hành động này sẽ xóa vai trò <strong>"<span id="roleName2" class="text-danger"></span>"</strong> và toàn bộ quyền liên quan. Bạn không thể hoàn tác.
                </p>
                <p class="mb-2">Vui lòng nhập lại tên vai trò để xác nhận:</p>
                <input type="text" id="confirmRoleInput" class="form-control" placeholder="Nhập chính xác tên vai trò">
                <div id="confirmError" class="text-danger mt-2 d-none">Tên vai trò không đúng!</div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnConfirmDeleteFinal" class="btn btn-danger">Xác nhận xóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>



<script>
    function toggleDeleteRoleButton() {
        const selectedId = document.getElementById('roleSelect').value;
        const deleteWrapper = document.getElementById('deleteRoleWrapper');

        if (selectedId === "2") {
            deleteWrapper.classList.add('d-none');
        } else {
            deleteWrapper.classList.remove('d-none');
        }
    }

    function loadPermissions() {
        const roleId = document.getElementById('roleSelect').value;
        const permissionTable = document.querySelector('#permissionTable');
        fetch('ajax/load_permissions.php?role_id=' + roleId)
            .then(res => res.json())
            .then(data => {
                permissionTable.innerHTML = data.permissionHtml || '';
                disableAllCheckboxes(); // sẽ thêm class readonly
                // Giữ lại class nếu đang readonly
                if (!btnEdit.classList.contains('d-none')) {
                    formPermission.classList.add('readonly-permission');
                }
                toggleDeleteRoleButton();
            })
    }
    loadPermissions();


    document.getElementById('roleSelect').addEventListener('change', function() {
        loadPermissions();
    });


    document.querySelector('#permissionTable').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-permission');
        if (!btn) return;

        const permissionId = btn.dataset.permissionId;
        const permissionName = btn.dataset.name;

        const confirmMsg = `Bạn có chắc chắn muốn xóa chức năng "${permissionName}" cùng toàn bộ quyền liên quan không?`;
        if (!confirm(confirmMsg)) return;

        fetch('ajax/delete_permission.php', {
                method: 'POST',
                body: new URLSearchParams({
                    permission_id: permissionId
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadPermissions(); // Reload lại danh sách

            });
    });

    const formPermission = document.getElementById('formPermission');
    const btnEdit = document.getElementById('btnEdit');
    const btnSave = document.getElementById('btnSave');
    const btnCancel = document.getElementById('btnCancel');

    // ✅ Khi mới vào: thêm class readonly
    formPermission.classList.add('readonly-permission');

    function disableAllCheckboxes() {
        formPermission.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = true);
        formPermission.classList.add('readonly-permission');
    }

    function enableAllCheckboxes() {
        formPermission.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.disabled = false);
        formPermission.classList.remove('readonly-permission');
    }

    btnEdit.addEventListener('click', () => {
        enableAllCheckboxes();
        btnEdit.classList.add('d-none');
        btnSave.classList.remove('d-none');
        btnCancel.classList.remove('d-none');
    });

    btnCancel.addEventListener('click', () => {
        loadPermissions(); // Reload lại quyền gốc
        btnEdit.classList.remove('d-none');
        btnSave.classList.add('d-none');
        btnCancel.classList.add('d-none');
    });

    function handlePermissionSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const roleId = document.getElementById('roleSelect').value;
        const formData = new FormData();

        formData.append('role_id', roleId);

        // Duyệt qua các checkbox
        form.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            const permissionId = cb.dataset.permissionId;
            const action = cb.dataset.action;

            formData.append('permissions[' + permissionId + '][]', action);
        });

        fetch('ajax/update_permission.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Đã lưu phân quyền.');
                // Reset lại trạng thái ban đầu
                btnEdit.classList.remove('d-none');
                btnSave.classList.add('d-none');
                btnCancel.classList.add('d-none');
                loadPermissions();
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi khi lưu phân quyền.");
            });

        return false; // Ngăn reload form
    }

    let selectedRoleId = null;
    let selectedRoleName = '';

    document.getElementById('btnDeleteRole').addEventListener('click', () => {
        const roleSelect = document.getElementById('roleSelect');
        selectedRoleId = roleSelect.value;
        selectedRoleName = roleSelect.selectedOptions[0].text;

        // Gán tên vào modal đầu tiên
        document.getElementById('roleName1').textContent = selectedRoleName;

        // Hiện modal đầu
        const modal1 = new bootstrap.Modal(document.getElementById('modalConfirmDelete1'));
        modal1.show();
    });

    document.getElementById('btnNextConfirm').addEventListener('click', () => {
        // Ẩn modal đầu và mở modal cảnh báo cuối
        const modal1El = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDelete1'));
        modal1El.hide();

        document.getElementById('roleName2').textContent = selectedRoleName;

        const modal2 = new bootstrap.Modal(document.getElementById('modalConfirmDelete2'));
        modal2.show();
    });

    document.getElementById('btnConfirmDeleteFinal').addEventListener('click', () => {
        const input = document.getElementById('confirmRoleInput').value.trim();
        const errorEl = document.getElementById('confirmError');

        if (input !== selectedRoleName) {
            errorEl.classList.remove('d-none');
            return;
        }

        errorEl.classList.add('d-none');

        fetch('ajax/delete_role.php', {
                method: 'POST',
                body: new URLSearchParams({
                    role_id: selectedRoleId
                })
            })
            .then(res => res.json())
            .then(data => {
                const modal2 = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDelete2'));
                modal2.hide();

                alert(data.message);
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Xảy ra lỗi khi xóa vai trò.');
            });
    });
</script>