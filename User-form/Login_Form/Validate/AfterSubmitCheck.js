async function submitForm(e) {
  e.preventDefault();

  const isValid = validateForm();
  if (!isValid) return;

  const formData = new FormData(e.target);

  try {
    const response = await fetch("/User-form/Login_Form/userdb_func.php", {
      method: "POST",
      body: formData
    });

    const responseData = await response.json();
    console.log("TRANGTHAI = ", trangthai);
    console.log("RESPONSE = ", responseData);

    e.target.querySelectorAll(".form-control").forEach(input => {
      input.classList.remove("is-invalid", "border-danger");
      input.classList.add("border-dark");

      const next = input.nextElementSibling;
      if (next && next.classList.contains("invalid-feedback")) {
        next.remove();
      }
    });

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
      
        // ✅ Redirect duy nhất ở đây, sau khi mọi thứ xong
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

    // Các lỗi khác:
    if (responseData.status === "USERNAME_EXISTS") {
      addError(e.target.querySelector('[name="username"]'), "Username đã tồn tại.");
    } else if (responseData.status === "EMAIL_EXISTS") {
      addError(e.target.querySelector('[name="email"]'), "Email đã tồn tại.");
    } else if (responseData.status === "PHONE_EXISTS") {
      addError(e.target.querySelector('[name="sdt"]'), "Số điện thoại đã tồn tại.");
    } else if (responseData.status === "INVALID_PASSWORD") {
      addError(e.target.querySelector('[name="pswd"]'), "Mật khẩu không hợp lệ.");
    } else if (responseData.status === "NO_ACCOUNT") {
      addError(e.target.querySelector('[name="email"]'), "Tài khoản không tồn tại.");
    } else if (responseData.status === "MISSING_FIELDS") {
      addError(e.target.querySelector('[name="username"]'), "Vui lòng điền đầy đủ thông tin.");
    } else if (responseData.status === "MISSING_EMAIL") {
      addError(e.target.querySelector('[name="email"]'), "Vui lòng nhập email.");
    } else {
      addError(e.target.querySelector('[name="username"]'), "Đã xảy ra lỗi không xác định.");
    }
  } catch (err) {
    addError(e.target.querySelector('[name="username"]'), "Lỗi máy chủ hoặc kết nối.");
  }
}
