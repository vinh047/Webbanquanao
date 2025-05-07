function loadForm() {
  const form = document.getElementById("mainformmainform");
  if (!form) return;

  // Xử lý form đăng ký và đăng nhập
  if (trangthai === "dangnhap") {
    form.innerHTML = `
      <div class="text-center mb-4">
          <h2> Đăng nhập Admin </h2>
      </div>
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
