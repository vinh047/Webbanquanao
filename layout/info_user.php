<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/DBConnection.php';
$db = DBConnect::getInstance();

if (empty($_SESSION['user_id'])) {
    header('Location: /User-form/Login_Form/Login_Form.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$userDetail = $db->selectOne(
    'SELECT name, email, phone FROM users WHERE user_id = ?',
    [$user_id]
);

// XÓA TOÀN BỘ BLOCK XỬ LÝ $_GET['cancel'] Ở ĐÂY

$currentPage = $_GET['page'] ?? 'taikhoan';
?>

<!-- styles for user info page -->
<link rel="stylesheet" href="/assets/css/info_user.css">

<div class="container my-5">
  <div class="row">
    <!-- Sidebar -->
    <aside class="col-md-3 mb-4">
      <nav class="list-group">
        <a href="/index.php?page=taikhoan"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center<?php if ($currentPage==='taikhoan') echo ' active'; ?>">
          <span><i class="fa-solid fa-user me-2"></i>Thông tin tài khoản</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=donhang"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center<?php if ($currentPage==='donhang') echo ' active'; ?>">
          <span><i class="fa-solid fa-receipt me-2"></i>Đơn hàng của bạn</span>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="/index.php?page=danhsachdiachi"
           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center<?php if ($currentPage==='danhsachdiachi') echo ' active'; ?>">
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
      <?php
        $pageMap = [
          'taikhoan'        => 'partials/account_section.php',
          'donhang'         => 'partials/order_section.php',
          'danhsachdiachi'  => 'partials/address_section.php',
        ];
        $includeFile = $pageMap[$currentPage] ?? 'partials/account_section.php';
        require_once __DIR__ . '/' . $includeFile;
      ?>
    </section>
  </div>
</div>

<!-- Include modals for user info pages -->
<?php
if ($currentPage === 'danhsachdiachi') {
    require_once __DIR__ . '/partials/address_modals.php';
} else {
    require_once __DIR__ . '/partials/account_modals.php';
}
?>

<script src="/assets/js/info_user.js" defer></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof syncCartAfterLogin === 'function') {
      syncCartAfterLogin();
    }
  });
</script>
