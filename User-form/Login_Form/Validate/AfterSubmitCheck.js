async function submitForm(e) {
  e.preventDefault();

  const isValid = validateForm();
  if (!isValid) return;

  const formData = new FormData(e.target);
  const trangthai = new URLSearchParams(window.location.search).get("trangthai") || "";

  try {
    const response = await fetch("/User-form/Login_Form/userdb_func.php", {
      method: "POST",
      body: formData
    });

    if (!response.ok) {
      throw new Error("Lỗi HTTP: " + response.status);
    }

    const responseData = await response.json();
    console.log("TRANGTHAI = ", trangthai);
    console.log("RESPONSE = ", responseData);

    // Reset lỗi cũ
    e.target.querySelectorAll(".form-control").forEach(input => {
      input.classList.remove("is-invalid", "border-danger");
      input.classList.add("border-dark");

      const next = input.nextElementSibling;
      if (next && next.classList.contains("invalid-feedback")) {
        next.remove();
      }
    });

    // ✅ Đăng nhập hoặc đăng ký thành công
    if (responseData.status === "LOGIN_SUCCESS" || responseData.status === "REGISTER_SUCCESS") {
      const role = responseData.role;

      if (role === 1) {
        try {
          console.log("🟡 Trước khi syncCartAfterLogin()");
          if (typeof window.syncCartAfterLogin === "function") {
            await window.syncCartAfterLogin();
            renderMiniCart();
            updateCartCount();
          }
          console.log("✅ Sau khi syncCartAfterLogin()");
        } catch (err) {
          console.error("❌ syncCartAfterLogin failed:", err);
        }

        // Redirect sau khi đồng bộ
        setTimeout(() => {
          window.location.href = location.origin + "/index.php";
        }, 100);
        return;
      }

      if ([2, 3, 4].includes(role)) {
        alert("Tài khoản đã bị khóa");
        return;
      }

      return;
    }

    // ✅ Quên mật khẩu → gửi OTP
    if (trangthai === "quenmatkhau" && responseData.status === "FORGOT_SUCCESS") {
      alert("Đã gửi OTP đến email. Vui lòng nhập mã OTP.");
      const url = new URL(window.location.href);
      url.searchParams.set("trangthai", "nhapotp");
      window.location.href = url.href;
      return;
    }

    // ✅ Xử lý lỗi cụ thể
    switch (responseData.status) {
      case "NAME_EXISTS":
        addError(e.target.querySelector('[name="name"]'), "Tên đã tồn tại.");
        break;
      case "EMAIL_EXISTS":
        addError(e.target.querySelector('[name="email"]'), "Email đã tồn tại.");
        break;
      case "PHONE_EXISTS":
        addError(e.target.querySelector('[name="sdt"]'), "Số điện thoại đã tồn tại.");
        break;
      case "INVALID_PASSWORD":
        addError(e.target.querySelector('[name="pswd"]'), "Mật khẩu không hợp lệ.");
        break;
      case "NO_ACCOUNT":
        addError(e.target.querySelector('[name="email"]'), "Tài khoản không tồn tại.");
        break;
      case "MISSING_FIELDS":
        addError(e.target.querySelector('[name="name"]'), "Vui lòng điền đầy đủ thông tin.");
        break;
      case "MISSING_EMAIL":
        addError(e.target.querySelector('[name="email"]'), "Vui lòng nhập email.");
        break;
      default:
        addError(e.target.querySelector('[name="name"]'), "Đã xảy ra lỗi không xác định.");
        break;
    }
  } catch (err) {
    console.error("Lỗi kết nối:", err);
    addError(e.target.querySelector('[name="uname"]'), "Lỗi máy chủ hoặc kết nối.");
  }
}
