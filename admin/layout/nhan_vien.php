<?php
require_once '../database/DBConnection.php';

$db = DBConnect::getInstance();

$roles = $db->select('SELECT * FROM roles WHERE role_id != 1 AND is_deleted = 0', []);
?>

<style>
    .nav-tabs .nav-link {
        color: black;
    }

    .nav-tabs .nav-lin k.active {
        color: #0d6efd;
        /* màu xanh Bootstrap */
    }
</style>

<!-- Thanh tìm kiếm -->
<div class="d-flex align-items-center justify-content-between mt-3 mb-4">
    <?php if (hasPermission('Quản lý nhân viên', 'write')): ?>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalThemNV">
            <i class="fa-solid fa-plus"></i> Thêm
        </button>
    <?php endif; ?>


    <div class="mx-auto w-25">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Tìm kiếm nhân viên" id="searchStaff">
            <button class="btn btn-secondary" style="pointer-events: none;">
                <i class="fa-solid fa-search"></i>
            </button>
        </div>
    </div>
</div>

<!-- Bảng nhân viên -->
<div class="table-responsive">
    <table class="table table-bordered align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên nhân viên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Chức năng</th>
            </tr>
        </thead>
        <tbody class="staff-wrap"></tbody>
    </table>
    <div class="pagination-wrap"></div>
</div>

<!-- Modal Thêm Nhân Viên -->
<div class="modal fade" id="modalThemNV" tabindex="-1" aria-labelledby="modalThemNVLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formThemNV" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalThemNVLabel">Thêm Nhân Viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nameNV" class="form-label">Tên nhân viên</label>
                    <input type="text" class="form-control" id="nameNV" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="emailNV" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailNV" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="passwordNV" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="passwordNV" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="phoneNV" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phoneNV" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="roleNV" class="form-label">Vai trò</label>
                    <select class="form-select" id="roleNV" name="role_id" required>
                        <option value="" disabled selected>Chọn vai trò</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="statusNV" class="form-label">Trạng thái</label>
                    <select class="form-control" id="statusNV" name="status" required>
                        <option value="1">Hoạt động</option>
                        <option value="0">Khóa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="provinceNV" class="form-label">Tỉnh/Thành phố</label>
                    <select class="form-control" id="provinceNV" name="province" required>
                        <option value="" selected disabled>Chọn tỉnh/thành</option>
                    </select>
                    <input type="hidden" name="province_name" id="provinceNameNV">
                </div>
                <div class="mb-3">
                    <label for="districtNV" class="form-label">Quận/Huyện</label>
                    <select class="form-control" id="districtNV" name="district" required>
                        <option value="" selected disabled>Chọn quận/huyện</option>
                    </select>
                    <input type="hidden" name="district_name" id="districtNameNV">
                </div>
                <div class="mb-3">
                    <label for="wardNV" class="form-label">Phường/Xã</label>
                    <select class="form-control" id="wardNV" name="ward" required>
                        <option value="" selected disabled>Chọn phường/xã</option>
                    </select>
                    <input type="hidden" name="ward_name" id="wardNameNV">
                </div>
                <div class="mb-3">
                    <label for="addressDetailNV" class="form-label">Địa chỉ chi tiết</label>
                    <input type="text" class="form-control" id="addressDetailNV" name="address_detail" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal SỬA nhân viên -->
<div class="modal fade" id="modalSuaNV" tabindex="-1" aria-labelledby="modalSuaNVLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formSuaNV" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Sửa thông tin nhân viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idNhanVienSua" name="user_id">
                <div class="mb-3">
                    <label for="nameNhanVienSua" class="form-label">Tên nhân viên</label>
                    <input type="text" class="form-control" id="nameNhanVienSua" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="emailNhanVienSua" class="form-label">Email</label>
                    <input type="email" class="form-control" id="emailNhanVienSua" name="email" readonly style="background-color:#f5f5f5; cursor:not-allowed;">
                </div>
                <div class="mb-3">
                    <label for="passwordNhanVienSua" class="form-label">Mật khẩu</label>
                    <input type="text" class="form-control" id="passwordNhanVienSua" name="password">
                    <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                </div>
                <div class="mb-3">
                    <label for="phoneNhanVienSua" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phoneNhanVienSua" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="roleNhanVienSua" class="form-label">Vai trò</label>
                    <select class="form-select" id="roleNhanVienSua" name="role_id" required>
                        <option value="" disabled selected>Chọn vai trò</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="statusNhanVienSua" class="form-label">Trạng thái</label>
                    <select class="form-control" id="statusNhanVienSua" name="status">
                        <option value="1">Hoạt động</option>
                        <option value="0">Khóa</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="provinceNVEdit" class="form-label">Tỉnh/Thành phố</label>
                    <select class="form-control" id="provinceNVEdit" name="province" required>
                        <option value="" selected disabled>Chọn tỉnh/thành</option>
                    </select>
                    <input type="hidden" name="province_name" id="provinceNameNVEdit">
                </div>
                <div class="mb-3">
                    <label for="districtNVEdit" class="form-label">Quận/Huyện</label>
                    <select class="form-control" id="districtNVEdit" name="district" required>
                        <option value="" selected disabled>Chọn quận/huyện</option>
                    </select>
                    <input type="hidden" name="district_name" id="districtNameNVEdit">
                </div>
                <div class="mb-3">
                    <label for="wardNVEdit" class="form-label">Phường/Xã</label>
                    <select class="form-control" id="wardNVEdit" name="ward" required>
                        <option value="" selected disabled>Chọn phường/xã</option>
                    </select>
                    <input type="hidden" name="ward_name" id="wardNameNVEdit">
                </div>
                <div class="mb-3">
                    <label for="addressDetailNVEdit" class="form-label">Địa chỉ chi tiết</label>
                    <input type="text" class="form-control" id="addressDetailNVEdit" name="address_detail" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal XÓA nhân viên -->
<div class="modal fade" id="modalXoaNV" tabindex="-1" aria-labelledby="modalXoaNVLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formXoaNV" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xóa nhân viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idNhanVienXoa" name="user_id">
                <p>Bạn có chắc chắn muốn khóa nhân viên này không?</p>
                <p><strong>Tên nhân viên:</strong> <span id="nameNhanVienXoa"></span></p>
                <p><strong>Email:</strong> <span id="emailNhanVienXoa"></span></p>
                <p><strong>Số điện thoại:</strong> <span id="phoneNhanVienXoa"></span></p>
                <p><strong>Trạng thái hiện tại:</strong> <span id="statusNhanVienXoa"></span></p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Xác nhận khóa</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal CHI TIẾT nhân viên -->
<div class="modal fade" id="modalChiTietNV" tabindex="-1" aria-labelledby="modalChiTietNVLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalChiTietNVLabel">Chi tiết nhân viên</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Tên:</strong> <span id="ct-name"></span></div>
                    <div class="col-md-6 mb-3"><strong>Email:</strong> <span id="ct-email"></span></div>
                    <div class="col-md-6 mb-3"><strong>SĐT:</strong> <span id="ct-phone"></span></div>
                    <div class="col-md-6 mb-3"><strong>Trạng thái:</strong> <span id="ct-status"></span></div>
                    <div class="col-md-6 mb-3"><strong>Vai trò:</strong> <span id="ct-role"></span></div>
                    <div class="col-md-12 mb-3"><strong>Mật khẩu (hash):</strong> <span id="ct-password"></span></div>
                    <div class="col-md-12 mb-3"><strong>Địa chỉ:</strong> <span id="ct-address-detail"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFilterParams = "";

    function loadStaff(page = 1, params = "") {
        const wrap = document.querySelector('.staff-wrap');
        const paginationWrap = document.querySelector('.pagination-wrap');
        fetch('ajax/load_staff.php?page=' + page + params)
            .then(res => res.json())
            .then(data => {
                wrap.innerHTML = data.staffHtml || '';
                paginationWrap.innerHTML = data.pagination || '';
                phantrang();
            });
    }

    loadStaff(1);

    function phantrang() {
        document.querySelectorAll(".page-link-custom").forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                loadStaff(page, currentFilterParams);
            });
        });
    }

    document.getElementById("searchStaff").addEventListener("input", function() {
        const keyword = this.value.trim();
        currentFilterParams = keyword ? `&search_name=${encodeURIComponent(keyword)}` : '';
        loadStaff(1, currentFilterParams);
    });

    document.getElementById("formThemNV").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value.trim();
        const phone = form.phone.value.trim();
        const status = form.status.value;
        const role_id = form.role_id.value;

        const province = form.province.value;
        const district = form.district.value;
        const ward = form.ward.value;
        const address_detail = form.address_detail.value.trim();

        // Lấy tên tỉnh, quận, phường từ các dropdown
        const province_name = form.province.options[form.province.selectedIndex].text;
        const district_name = form.district.options[form.district.selectedIndex].text;
        const ward_name = form.ward.options[form.ward.selectedIndex].text;

        if (!name || !email || !password || !phone || !province || !district || !ward || !address_detail || !role_id) {
            alert("Vui lòng nhập đầy đủ thông tin nhân viên.");
            return;
        }

        const formData = new FormData(form);
        formData.append('province_name', province_name);
        formData.append('district_name', district_name);
        formData.append('ward_name', ward_name);
        formData.append('role_id', role_id);

        fetch('ajax/add_staff.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalThemNV'));
                    modal.hide();
                    form.reset();
                    alert(data.message);
                    loadStaff(1);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi thêm nhân viên.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Gán dữ liệu khi nhấn XÓA
    document.querySelector('.staff-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-staff');
        if (!btn) return;

        document.getElementById("idNhanVienXoa").value = btn.dataset.id || "";
        document.getElementById("nameNhanVienXoa").textContent = btn.dataset.name || "";
        document.getElementById("emailNhanVienXoa").textContent = btn.dataset.email || "";
        document.getElementById("phoneNhanVienXoa").textContent = btn.dataset.phone || "";
        document.getElementById("statusNhanVienXoa").textContent = btn.dataset.status == "1" ? "Hoạt động" : "Khóa";
    });

    document.getElementById("formXoaNV").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("ajax/delete_staff.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById("modalXoaNV")).hide();
                    loadStaff(1, currentFilterParams);
                }
            });
    });

    // Gán dữ liệu khi nhấn SỬA
    document.querySelector('.staff-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-staff');
        if (!btn) return;

        document.getElementById("idNhanVienSua").value = btn.dataset.id || "";
        document.getElementById("nameNhanVienSua").value = btn.dataset.name || "";
        document.getElementById("emailNhanVienSua").value = btn.dataset.email || "";
        document.getElementById("passwordNhanVienSua").value = btn.dataset.password || "";
        document.getElementById("phoneNhanVienSua").value = btn.dataset.phone || "";
        document.getElementById("statusNhanVienSua").value = btn.dataset.status || "1";
        document.getElementById("addressDetailNVEdit").value = btn.dataset.addressDetail || "";
        document.getElementById("roleNhanVienSua").value = btn.dataset.roleId || "";

        const provinceCode = btn.dataset.province || "";
        const districtCode = btn.dataset.district || "";
        const wardCode = btn.dataset.ward || "";

        const provinceSelect = document.getElementById("provinceNVEdit");
        const districtSelect = document.getElementById("districtNVEdit");
        const wardSelect = document.getElementById("wardNVEdit");

        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(provinces => {
                provinceSelect.innerHTML = '<option disabled selected>Chọn tỉnh/thành</option>';
                provinces.forEach(p => {
                    const option = new Option(p.name.replace(/^Tỉnh |^Thành phố /, ''), p.code);
                    provinceSelect.add(option);
                });

                provinceSelect.value = provinceCode;

                if (provinceCode) {
                    return fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`);
                }
            })
            .then(res => {
                if (!res) return;
                return res.json();
            })
            .then(province => {
                if (!province) return;
                districtSelect.innerHTML = '<option disabled selected>Chọn quận/huyện</option>';
                province.districts.forEach(d => {
                    const option = new Option(d.name, d.code);
                    districtSelect.add(option);
                });

                districtSelect.value = districtCode;

                if (districtCode) {
                    return fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`);
                }
            })
            .then(res => {
                if (!res) return;
                return res.json();
            })
            .then(district => {
                if (!district) return;
                wardSelect.innerHTML = '<option disabled selected>Chọn phường/xã</option>';
                district.wards.forEach(w => {
                    const option = new Option(w.name, w.code);
                    wardSelect.add(option);
                });

                wardSelect.value = wardCode;
            })
            .catch(err => {
                console.error("Lỗi khi load địa chỉ:", err);
                alert("Không thể load địa chỉ. Vui lòng thử lại sau.");
            });
    });

    document.getElementById("formSuaNV").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = e.target;
        const user_id = form.user_id.value;
        const name = form.name.value.trim();
        const email = form.email.value.trim();
        const password = form.password.value.trim();
        const phone = form.phone.value.trim();
        const status = form.status.value;
        const role_id = form.role_id.value;
        if (!role_id) {
            alert("Vui lòng chọn vai trò.");
            return;
        }
        const province = form.province.value;
        const district = form.district.value;
        const ward = form.ward.value;
        const address_detail = form.address_detail.value.trim();

        // Lấy tên tỉnh, quận, phường từ các dropdown
        const province_name = form.province.options[form.province.selectedIndex].text;
        const district_name = form.district.options[form.district.selectedIndex].text;
        const ward_name = form.ward.options[form.ward.selectedIndex].text;

        if (!user_id || !name || !email || !phone || !province || !district || !ward || !address_detail) {
            alert("Vui lòng nhập đầy đủ thông tin nhân viên.");
            return;
        }

        const formData = new FormData(form);
        formData.append('province_name', province_name);
        formData.append('district_name', district_name);
        formData.append('ward_name', ward_name);
        formData.append('role_id', role_id);

        fetch('ajax/update_staff.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSuaNV'));
                    modal.hide();
                    alert(data.message);
                    loadStaff(1);
                } else {
                    alert(data.message || "Đã xảy ra lỗi khi sửa nhân viên.");
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Lỗi kết nối máy chủ.");
            });
    });

    // Gán dữ liệu khi nhấn Chi tiết
    document.querySelector('.staff-wrap').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-detail-staff');
        if (!btn) return;

        document.getElementById('ct-name').textContent = btn.dataset.name || '';
        document.getElementById('ct-email').textContent = btn.dataset.email || '';
        document.getElementById('ct-phone').textContent = btn.dataset.phone || '';
        document.getElementById('ct-status').textContent = btn.dataset.status == "1" ? "Hoạt động" : "Khóa";
        document.getElementById('ct-role').textContent = btn.dataset.role || '';
        document.getElementById('ct-password').textContent = btn.dataset.password || '';
        // Đã có nhãn "Địa chỉ: " trong HTML, chỉ cần gán giá trị address_detail
        document.getElementById('ct-address-detail').textContent = btn.dataset.addressDetail || 'Không xác định';
    });

    document.addEventListener('DOMContentLoaded', () => {
        // --- Load tỉnh/huyện/xã cho modal Thêm nhân viên ---
        const provinceAdd = document.getElementById('provinceNV');
        const districtAdd = document.getElementById('districtNV');
        const wardAdd = document.getElementById('wardNV');

        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(data => {
                provinceAdd.innerHTML = '<option selected disabled>Chọn tỉnh/thành</option>';
                data.forEach(p => {
                    const name = p.name.replace(/^Tỉnh |^Thành phố /, '');
                    provinceAdd.add(new Option(name, p.code));
                });
            });

        provinceAdd.addEventListener('change', () => {
            districtAdd.innerHTML = '<option selected disabled>Chọn quận/huyện</option>';
            wardAdd.innerHTML = '<option selected disabled>Chọn phường/xã</option>';
            fetch(`https://provinces.open-api.vn/api/p/${provinceAdd.value}?depth=2`)
                .then(res => res.json())
                .then(obj => {
                    obj.districts.forEach(d => districtAdd.add(new Option(d.name, d.code)));
                });
        });

        districtAdd.addEventListener('change', () => {
            wardAdd.innerHTML = '<option selected disabled>Chọn phường/xã</option>';
            fetch(`https://provinces.open-api.vn/api/d/${districtAdd.value}?depth=2`)
                .then(res => res.json())
                .then(obj => {
                    obj.wards.forEach(w => wardAdd.add(new Option(w.name, w.code)));
                });
        });

        // --- Load tỉnh/huyện/xã cho modal Sửa nhân viên ---
        const provinceEdit = document.getElementById('provinceNVEdit');
        const districtEdit = document.getElementById('districtNVEdit');
        const wardEdit = document.getElementById('wardNVEdit');

        fetch('https://provinces.open-api.vn/api/p/')
            .then(res => res.json())
            .then(data => {
                provinceEdit.innerHTML = '<option selected disabled>Chọn tỉnh/thành</option>';
                data.forEach(p => {
                    const name = p.name.replace(/^Tỉnh |^Thành phố /, '');
                    provinceEdit.add(new Option(name, p.code));
                });
            });

        provinceEdit.addEventListener('change', () => {
            districtEdit.innerHTML = '<option selected disabled>Chọn quận/huyện</option>';
            wardEdit.innerHTML = '<option selected disabled>Chọn phường/xã</option>';
            fetch(`https://provinces.open-api.vn/api/p/${provinceEdit.value}?depth=2`)
                .then(res => res.json())
                .then(obj => {
                    obj.districts.forEach(d => districtEdit.add(new Option(d.name, d.code)));
                });
        });

        districtEdit.addEventListener('change', () => {
            wardEdit.innerHTML = '<option selected disabled>Chọn phường/xã</option>';
            fetch(`https://provinces.open-api.vn/api/d/${districtEdit.value}?depth=2`)
                .then(res => res.json())
                .then(obj => {
                    obj.wards.forEach(w => wardEdit.add(new Option(w.name, w.code)));
                });
        });
    });
</script>