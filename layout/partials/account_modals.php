<!-- 📁 partials/account_modals.php -->
<?php
// Biến $userDetail phải được truyền từ file cha
?>

<!-- Modal: Edit Profile -->
<div class="modal fade" id="modalEditProfile" tabindex="-1" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalEditProfileLabel">Chỉnh sửa thông tin tài khoản</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form id="formEditProfile" novalidate>
          <div class="mb-3">
            <label for="editName" class="form-label">Họ tên của bạn</label>
            <input
              type="text"
              id="editName"
              name="name"
              class="form-control"
              value="<?= htmlspecialchars($userDetail['name']) ?>">
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-3">
            <label for="editPhone" class="form-label">Số điện thoại của bạn</label>
            <input
              type="text"
              id="editPhone"
              name="phone"
              class="form-control"
              value="<?= htmlspecialchars($userDetail['phone']) ?>">
            <div class="invalid-feedback"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" form="formEditProfile" class="btn btn-primary">Lưu thay đổi</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Change Password -->
<div class="modal fade" id="modalChangePassword" tabindex="-1" aria-labelledby="modalChangePasswordLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalChangePasswordLabel">Thay đổi mật khẩu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form id="formChangePassword" novalidate>
          <div class="mb-3">
            <label for="oldPassword" class="form-label">Mật khẩu cũ</label>
            <div class="input-group has-validation">
              <input type="password" id="oldPassword" name="old_password" class="form-control" required>
              <span class="input-group-text"><i class="fa-solid fa-eye-slash"></i></span>
              <div class="invalid-feedback" id="oldPasswordFeedback"></div>
            </div>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">Mật khẩu mới</label>
            <div class="input-group has-validation">
              <input type="password" id="newPassword" name="new_password" class="form-control" required>
              <span class="input-group-text"><i class="fa-solid fa-eye-slash"></i></span>
              <div class="invalid-feedback" id="newPasswordFeedback"></div>
            </div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Nhập lại mật khẩu</label>
            <div class="input-group has-validation">
              <input type="password" id="confirmPassword" name="confirm_password" class="form-control" required>
              <span class="input-group-text"><i class="fa-solid fa-eye-slash"></i></span>
              <div class="invalid-feedback" id="confirmPasswordFeedback"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" form="formChangePassword" class="btn btn-primary">Lưu mật khẩu</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal: Thêm địa chỉ mới -->
<div class="modal fade" id="modalAddAddress" tabindex="-1" aria-labelledby="modalAddAddressLabel" role="dialog" aria-modal="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Thêm địa chỉ mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form id="formAddAddress" novalidate>
          <div class="mb-3">
            <label for="address_detail" class="form-label">Số nhà, tên đường</label>
            <input type="text" id="address_detail" name="address_detail" class="form-control" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-3">
            <label for="ward" class="form-label">Phường / Xã</label>
            <input type="text" id="ward" name="ward" class="form-control" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-3">
            <label for="district" class="form-label">Quận / Huyện</label>
            <input type="text" id="district" name="district" class="form-control" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="mb-3">
            <label for="province" class="form-label">Tỉnh / Thành phố</label>
            <input type="text" id="province" name="province" class="form-control" required>
            <div class="invalid-feedback"></div>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_default" name="is_default">
            <label class="form-check-label" for="is_default">Đặt làm địa chỉ mặc định</label>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" form="formAddAddress" class="btn btn-primary">Lưu địa chỉ</button>
      </div>
    </div>
  </div>
</div>
