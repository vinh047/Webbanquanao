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
  
      // Parse phản hồi từ server
      const responseData = await response.json();
      console.log(responseData.status);
  
      // Xóa các lỗi validation trước đó
      e.target.querySelectorAll(".form-control").forEach(input => {
        input.classList.remove("is-invalid", "border-danger");
        input.classList.add("border-dark");
        const next = input.nextElementSibling;
        if (next && next.classList.contains("invalid-feedback")) {
          next.remove();
        }
      });
  
      // Xử lý đăng nhập hoặc đăng ký thành công
      if (responseData.status === "LOGIN_SUCCESS" || responseData.status === "REGISTER_SUCCESS") {
        const role = responseData.role; 
        console.log(role);
        console.log("Type of role:", typeof role);
  
        if (role === 1) {
          console.log("Redirecting to the new page...");
          window.location.replace("../../index.php");
        } else if (role === 2 || role === 3 || role === 4) {
          console.log("Redirecting to the new page...");
          window.location.replace("../../admin/index.php");
        }
  
      } else if (responseData.status === "USERNAME_EXISTS") {
        addError(e.target.querySelector('[name="username"]'), "Username đã tồn tại.");
      } else if (responseData.status === "EMAIL_EXISTS") {
        addError(e.target.querySelector('[name="email"]'), "Email đã tồn tại.");
      } else if (responseData.status === "INVALID_PASSWORD") {
        addError(e.target.querySelector('[name="pswd"]'), "Mật khẩu không hợp lệ.");
      } else if (responseData.status === "NO_ACCOUNT") {
        addError(e.target.querySelector('[name="email"]'), "Tài khoản không tồn tại.");
      } else if (responseData.status === "MISSING_FIELDS") {
        addError(e.target.querySelector('[name="username"]'), "Vui lòng điền đầy đủ thông tin.");
      } else {
        addError(e.target.querySelector('[name="username"]'), "Đã xảy ra lỗi không xác định.");
      }
    } catch (err) {
      // Xử lý lỗi khi có vấn đề với máy chủ
      addError(e.target.querySelector('[name="username"]'), "Lỗi máy chủ hoặc kết nối.");
    }
  }
  