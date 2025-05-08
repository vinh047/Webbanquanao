function loadForm() {
  const form = document.getElementById("mainformmainform");
  if (!form) return;

  if (trangthai === "dangky") {
    form.innerHTML = `
      <h2 class="text-center text-black">Đăng ký tài khoản</h2>
      <div class="mb-3">
        <label class="text-black">Họ và tên</label>
        <input type="text" name="name" class="form-control border-dark" placeholder="Nhập họ và tên">
      </div>
      <div class="mb-3">
        <label class="text-black">Email</label>
        <input type="email" name="email" class="form-control border-dark" placeholder="Nhập email">
      </div>
      <div class="mb-3">
        <label class="text-black">Mật khẩu</label>
        <input type="password" name="pswd" class="form-control border-dark" placeholder="Nhập mật khẩu">
      </div>
      <div class="mb-3">
        <label for="pswd-confirm" class="form-label text-black">Xác nhận mật khẩu</label>
        <input type="password" class="form-control border-dark" id="pswd-confirm" name="pswd-confirm" placeholder="Nhập lại mật khẩu">
      </div>
      <div class="mb-3">
        <label class="text-black">Số điện thoại</label>
        <input type="tel" name="sdt" class="form-control border-dark" placeholder="Nhập số điện thoại">
      </div>
      <hr>
      <button type="submit" class="btn btn-success w-100">Đăng ký ngay</button>
      <div class="text-center mt-3">
        <span class="text-black fw-bold fs-5">
          Đã có tài khoản?
          <a href="?trangthai=dangnhap" class="btn btn-link fw-bold fs-6">Đăng nhập</a>
        </span>
      </div>
    `;
  } else if (trangthai === "quenmatkhau") {
    form.innerHTML = `
      <h2 class="text-center text-black">Quên mật khẩu</h2>
      <p class="text-black text-center mb-4">Vui lòng nhập email của Quý Khách hàng, chúng tôi sẽ gửi mã OTP để xác thực thông tin.</p>
      <div class="mb-3">
        <label for="email" class="form-label text-black">Email</label>
        <input type="email" class="form-control border-dark" id="email" name="email" placeholder="Nhập email vào đây">
      </div>
      <hr>
      <div class="text-center pb-2">
        <button type="submit" class="btn btn-warning w-75">Gửi mã OTP</button>
      </div>
      <div class="text-center">
        <a href="?trangthai=dangnhap" class="btn btn-link text-primary fw-bold fs-6">Đăng nhập / Đăng ký</a>
      </div>
    `;
  } else if (trangthai === "nhapotp") {
    form.innerHTML = `
      <h2 class="text-center text-black">Nhập mã OTP</h2>
      <p class="text-center text-black">Vui lòng nhập mã OTP đã gửi đến email của bạn và đặt lại mật khẩu.</p>
  
      <div class="mb-3">
        <label class="text-black">Mã OTP</label>
        <input type="text" name="otp" class="form-control border-dark" placeholder="Nhập mã OTP">
      </div>
      <div class="mb-3">
        <label class="text-black">Mật khẩu mới</label>
        <input type="password" name="new_password" class="form-control border-dark" placeholder="Nhập mật khẩu mới">
      </div>
      <div class="mb-3">
        <label class="text-black">Xác nhận mật khẩu</label>
        <input type="password" name="confirm_password" class="form-control border-dark" placeholder="Nhập lại mật khẩu">
      </div>
      <hr>
      <button type="submit" class="btn btn-success w-100">Xác nhận</button>
    `;
  
  } else {
    form.innerHTML = `
      <div class="avatar text-center mb-4">
        <img src="img/avatar.jpg" class="rounded-circle" width="100" height="100">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label text-black">Email</label>
        <input type="email" class="form-control border-dark" id="email" name="email" placeholder="Nhập email">
      </div>
      <div class="mb-3">
        <label for="pswd" class="form-label text-black">Mật khẩu</label>
        <input type="password" class="form-control border-dark" id="pswd" name="pswd" placeholder="Nhập mật khẩu">
      </div>
      <hr>
      <div class="text-center pb-2">
        <button type="submit" class="btn btn-primary w-75">Đăng nhập</button>
      </div>
      <div class="text-center">
        <span class="text-black fw-bold fs-6">
          Chưa có tài khoản?
          <a href="?trangthai=dangky" class="btn btn-link fw-bold fs-6">Đăng ký</a>
        </span>
      </div>
      <div class="text-center">
        <span class="text-black fw-bold fs-6">
          Quên mật khẩu?
          <a href="?trangthai=quenmatkhau" class="btn btn-link text-danger fw-bold fs-6">Click here</a>
        </span>
      </div>
    `;
  }

  const hiddenInput = document.createElement("input");
  hiddenInput.type = "hidden";
  hiddenInput.name = "trangthai";
  hiddenInput.value = trangthai;
  form.appendChild(hiddenInput);

  // Gọi logic xử lý submit form từ file khác (userdb_func.js)
  form.addEventListener("submit", submitForm);
}

document.addEventListener("DOMContentLoaded", loadForm);
