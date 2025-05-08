<?php
echo '<link rel="stylesheet" href="/assets/css/info_user.css">';
echo '<script src="/assets/js/info_user.js" defer></script>';
?>

<?php
// 1) Kiểm tra đã login chưa (header.php đã chạy session_start())
if (empty($_SESSION['user_id'])) {
    header('Location: /User-form/Login_Form/Login_Form.php');
    exit;
}

// 2) Lấy chi tiết thông tin user
$userDetail = $db->selectOne(
    'SELECT name, email, phone, address
     FROM users
     WHERE user_id = ?',
    [ $_SESSION['user_id'] ]
);
?>

<div class="container my-5">
  <div class="row">
    <!-- Sidebar trái -->
    <aside class="col-md-3 mb-4">
      <nav class="list-group">
      <a href="/index.php?page=taikhoan"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-user"></i></i> Thông tin tài khoản</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=donhang"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử đơn hàng</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?action=logout"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
          <span><i class="fa-solid fa-power-off me-2"></i> Đăng xuất</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      </nav>
    </aside>

    <!-- Nội dung chính bên phải -->
    <section class="col-md-9">
      <h2>Thông tin tài khoản</h2>
      <dl class="row mb-4">
        <dt class="col-sm-4">Họ và tên</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['name'], ENT_QUOTES) ?></dd>

        <dt class="col-sm-4">Email</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['email'], ENT_QUOTES) ?></dd>

        <dt class="col-sm-4">Số điện thoại</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['phone'], ENT_QUOTES) ?></dd>

        <dt class="col-sm-4">Địa chỉ</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($userDetail['address'], ENT_QUOTES) ?></dd>
      </dl>

      <a href="/index.php?page=edit_profile" class="btn btn-outline-primary">
        Cập nhật thông tin
      </a>
    </section>
  </div>
</div>
