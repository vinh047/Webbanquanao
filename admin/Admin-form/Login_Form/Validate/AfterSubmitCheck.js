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
    console.log("Response:", responseData);

    // Reset lỗi trước đó
    e.target.querySelectorAll(".form-control").forEach(input => {
      input.classList.remove("is-invalid", "border-danger");
      input.classList.add("border-dark");
      const next = input.nextElementSibling;
      if (next && next.classList.contains("invalid-feedback")) {
        next.remove();
      }
    });

    // Xử lý kết quả đăng nhập
    if (responseData.status === "LOGIN_SUCCESS") {
      const role = responseData.role;
      console.log("Logged in with role:", role);

      if (role === 1) {
        // User thường → chặn đăng nhập admin
        addError(e.target.querySelector('[name="email"]'), "Tài khoản không có quyền truy cập trang quản trị.");
        return;
      }

      // Admin, nhân viên, v.v.
      if (role === 2 || role === 3 || role === 4) {
        window.location.replace("../../../admin/index.php");
        return;
      }

      // Nếu không khớp role nào
      addError(e.target.querySelector('[name="email"]'), "Không xác định được quyền truy cập.");
    } 
    else if (responseData.status === "INVALID_PASSWORD") {
      addError(e.target.querySelector('[name="pswd"]'), "Mật khẩu không hợp lệ.");
    } 
    else if (responseData.status === "NO_ACCOUNT") {
      addError(e.target.querySelector('[name="email"]'), "Tài khoản không tồn tại.");
    } 
    else if (responseData.status === "MISSING_FIELDS") {
      addError(e.target.querySelector('[name="email"]'), "Vui lòng điền đầy đủ thông tin.");
    } 
    else {
      addError(e.target.querySelector('[name="email"]'), "Đã xảy ra lỗi không xác định.");
    }
  } catch (err) {
    console.error("Lỗi fetch:", err);
    addError(e.target.querySelector('[name="email"]'), "Lỗi máy chủ hoặc kết nối.");
  }
}
