function loadForm() {
  const form = document.getElementById("mainformmainform");
  if (!form) return;

  if (trangthai === "dangky") {
    form.innerHTML = `
      <h2 class="text-center text-black">Đăng ký tài khoản</h2>
      <div class="mb-3">
        <label class="text-black">Username</label>
        <input type="text" name="username" class="form-control border-dark" placeholder="Nhập Username">
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
        <label class="text-black">Số điện thoại</label>
        <input type="tel" name="sdt" class="form-control border-dark" placeholder="Nhập số điện thoại">
      </div>
      <div class="mb-3">
        <label class="text-black">Địa chỉ</label>
        <input type="text" name="diachi" class="form-control border-dark" placeholder="Nhập địa chỉ">
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
      <div class="form-check mb-3">
        <label for="remember" class="form-check-label text-black">Ghi nhớ đăng nhập</label>
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
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
          <a href="#" class="btn btn-link text-danger fw-bold fs-6">Click here</a>
        </span>
      </div>
    `;
  }

  // ✅ THÊM input hidden để gửi trạng thái về PHP
  const hiddenInput = document.createElement("input");
  hiddenInput.type = "hidden";
  hiddenInput.name = "trangthai";
  hiddenInput.value = trangthai;
  form.appendChild(hiddenInput);

  // ✅ Gắn sự kiện submit sau khi form được render
  form.addEventListener("submit", function (e) {
    const checkvar = validateForm();
    if (!checkvar) {
      e.preventDefault();
    }
  });
}

document.addEventListener("DOMContentLoaded", loadForm);
