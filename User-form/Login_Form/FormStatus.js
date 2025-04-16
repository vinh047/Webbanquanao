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

  const hiddenInput = document.createElement("input");
  hiddenInput.type = "hidden";
  hiddenInput.name = "trangthai";
  hiddenInput.value = trangthai;
  form.appendChild(hiddenInput);

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const isValid = validateForm();
    if (!isValid) return;

    const formData = new FormData(form);

    try {
      const response = await fetch("/User-form/Login_Form/userdb_func.php", {
        method: "POST",
        body: formData
      });

      const text = await response.text();

      form.querySelectorAll(".form-control").forEach(input => {
        input.classList.remove("is-invalid", "border-danger");
        input.classList.add("border-dark");
        const next = input.nextElementSibling;
        if (next && next.classList.contains("invalid-feedback")) {
          next.remove();
        }
      });

      if (text === "REGISTER_SUCCESS" || text === "LOGIN_SUCCESS") {
        window.location.href = "../../index.php";
      } else if (text === "EMAIL_EXISTS") {
        addError(form.querySelector('[name="email"]'), "Email đã tồn tại.");
      } else if (text === "INVALID_PASSWORD") {
        addError(form.querySelector('[name="pswd"]'), "Mật khẩu không hợp lệ.");
      } else if (text === "NO_ACCOUNT") {
        addError(form.querySelector('[name="email"]'), "Tài khoản không tồn tại.");
      } else if (text === "MISSING_FIELDS") {
        addError(form.querySelector('[name="username"]'), "Vui lòng điền đầy đủ thông tin.");
      } else {
        addError(form.querySelector('[name="username"]'), "Đã xảy ra lỗi không xác định.");
      }
    } catch (err) {
      addError(form.querySelector('[name="username"]'), "Lỗi máy chủ hoặc kết nối.");
    }
  });
}

document.addEventListener("DOMContentLoaded", loadForm);
