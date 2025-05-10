<?php
require_once __DIR__ . '/header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý đăng xuất...
// (giữ nguyên logic logout ở logout.php nếu bạn đã tách riêng)

require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

// Kiểm tra đã đăng nhập
if (empty($_SESSION['user_id'])) {
  header('Location: /User-form/Login_Form/Login_Form.php');
  exit;
}

// Lấy thông tin user
$userDetail = $db->selectOne(
  'SELECT name, email, phone FROM users WHERE user_id = ?',
  [$_SESSION['user_id']]
);
?>

<link rel="stylesheet" href="/assets/css/info_user.css">

<div class="container my-5">
  <div class="row">
    <!-- Sidebar -->
    <aside class="col-md-3 mb-4">
      <nav class="list-group">
        <a href="#"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="modal"
          data-bs-target="#modalEditProfile">
          <span><i class="fa-solid fa-user me-2"></i>Thông tin tài khoản</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=donhang"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-receipt me-2"></i> Đơn hàng của bạn</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=lichsumuahang"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử mua hàng</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=danhsachdiachi"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-location-dot me-2"></i>Danh sách địa chỉ</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/User-form/Login_Form/logout.php"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-power-off me-2"></i>Đăng xuất</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      </nav>
    </aside>

    <!-- Main content -->
    <section class="col-md-9">
      <h2>Thông tin tài khoản</h2>
      <dl class="row mb-4">
        <dt class="col-sm-4">Họ và tên</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['name'], ENT_QUOTES) ?></dd>

        <dt class="col-sm-4">Số điện thoại</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['phone'], ENT_QUOTES) ?></dd>
      </dl>
      <a href="#"
        class="btn btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalEditProfile">
        Cập nhật thông tin
      </a>

      <hr class="my-5">

      <h2>Thông tin đăng nhập</h2>
      <dl class="row mb-4">
        <dt class="col-sm-4">Email</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['email'], ENT_QUOTES) ?></dd>

        <dt class="col-sm-4">Mật khẩu</dt>
        <dd class="col-sm-8">*****************</dd>
      </dl>
      <a href="#"
        class="btn btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalChangePassword">
        Thay đổi mật khẩu
      </a>
    </section>
  </div>
</div>

<!-- Modal: Edit Profile -->
<div class="modal fade" id="modalEditProfile" tabindex="-1" aria-labelledby="modalEditProfileLabel" role="dialog" aria-modal="true">
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
              value="<?= htmlspecialchars($userDetail['name'], ENT_QUOTES) ?>">
            <div class="invalid-feedback"></div>
          </div>

          <div class="mb-3">
            <label for="editPhone" class="form-label">Số điện thoại của bạn</label>
            <input
              type="text"
              id="editPhone"
              name="phone"
              class="form-control"
              value="<?= htmlspecialchars($userDetail['phone'], ENT_QUOTES) ?>">
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
<div class="modal fade" id="modalChangePassword" tabindex="-1" aria-labelledby="modalChangePasswordLabel" role="dialog" aria-modal="true">
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
              <input
                type="password"
                id="oldPassword"
                name="old_password"
                class="form-control"
                aria-describedby="oldPasswordFeedback"
                required>
              <span class="input-group-text">
                <i class="fa-regular fa-eye-slash"></i>
              </span>
              <div id="oldPasswordFeedback" class="invalid-feedback"></div>
            </div>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">Mật khẩu mới</label>
            <div class="input-group has-validation">
              <input
                type="password"
                id="newPassword"
                name="new_password"
                class="form-control"
                aria-describedby="newPasswordFeedback"
                required>
              <span class="input-group-text">
                <i class="fa-regular fa-eye-slash"></i>
              </span>
              <div id="newPasswordFeedback" class="invalid-feedback"></div>
            </div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Nhập lại mật khẩu</label>
            <div class="input-group has-validation">
              <input
                type="password"
                id="confirmPassword"
                name="confirm_password"
                class="form-control"
                aria-describedby="confirmPasswordFeedback"
                required>
              <span class="input-group-text">
                <i class="fa-regular fa-eye-slash"></i>
              </span>
              <div id="confirmPasswordFeedback" class="invalid-feedback"></div>
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

<script src="/assets/js/addToCart.js" defer></script>
<script src="/assets/js/cart.js" defer></script>
<script src="/assets/js/header.js"    defer></script>
<script src="/assets/js/info_user.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  if (typeof syncCartAfterLogin === 'function') {
    syncCartAfterLogin();
  }
});
</script>