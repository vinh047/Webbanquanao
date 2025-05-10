<?php
if (!isset($userDetail)) {
  echo '<div class="alert alert-danger">Không tìm thấy thông tin người dùng.</div>';
  return;
}
?>

<h2>Thông tin tài khoản</h2>
<dl class="row mb-4">
  <dt class="col-sm-4">Họ và tên</dt>
  <dd class="col-sm-8"><?= htmlspecialchars($userDetail['name']) ?></dd>

  <dt class="col-sm-4">Số điện thoại</dt>
  <dd class="col-sm-8"><?= htmlspecialchars($userDetail['phone']) ?></dd>
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
  <dd class="col-sm-8"><?= htmlspecialchars($userDetail['email']) ?></dd>

  <dt class="col-sm-4">Mật khẩu</dt>
  <dd class="col-sm-8">*****************</dd>
</dl>
<a href="#"
   class="btn btn-outline-primary"
   data-bs-toggle="modal"
   data-bs-target="#modalChangePassword">
  Thay đổi mật khẩu
</a>
