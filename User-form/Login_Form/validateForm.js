function validateForm() {
    const form = document.getElementById("mainformmainform");
    let isValid = true;
  
    form.querySelectorAll(".form-control").forEach(input => {
      input.classList.remove("is-invalid", "border-danger");
      input.classList.add("border-dark");
  
      const next = input.nextElementSibling;
      if (next && next.classList.contains("invalid-feedback")) {
        next.remove();
      }
    });
  
    const usernameRegex = /^[a-zA-Z0-9_]{4,20}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{6,}$/;
    const phoneRegex = /^0\d{9}$/;
  
    function showError(field, message) {
      field.classList.remove("border-dark");
      field.classList.add("is-invalid", "border-danger");
  
      const feedback = document.createElement("div");
      feedback.className = "invalid-feedback d-block text-danger";
      feedback.textContent = message;
      field.insertAdjacentElement("afterend", feedback);
      isValid = false;
    }
  
    if (trangthai === "dangky") {
      const usernameField = form.querySelector('[name="username"]');
      const emailField = form.querySelector('[name="email"]');
      const passwordField = form.querySelector('[name="pswd"]');
      const phoneField = form.querySelector('[name="sdt"]');
      const addressField = form.querySelector('[name="diachi"]');
  
      const username = usernameField.value.trim();
      const email = emailField.value.trim();
      const password = passwordField.value.trim();
      const sdt = phoneField.value.trim();
      const diachi = addressField.value.trim();
  
      if (!username) showError(usernameField, "Vui lòng nhập tên người dùng");
      else if (!usernameRegex.test(username)) showError(usernameField, "Tên người dùng không hợp lệ");
  
      if (!email) showError(emailField, "Vui lòng nhập email");
      else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");
  
      if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
      else if (!passwordRegex.test(password)) showError(passwordField, "Mật khẩu cần chữ hoa, số, ký tự đặc biệt");
  
      if (!sdt) showError(phoneField, "Vui lòng nhập số điện thoại");
      else if (!phoneRegex.test(sdt)) showError(phoneField, "Số điện thoại không hợp lệ");
  
      if (!diachi) showError(addressField, "Vui lòng nhập địa chỉ");
  
    } else if (trangthai === "dangnhap") {
      const emailField = form.querySelector('[name="email"]');
      const passwordField = form.querySelector('[name="pswd"]');
  
      const email = emailField.value.trim();
      const password = passwordField.value.trim();
  
      if (!email) showError(emailField, "Vui lòng nhập email");
      else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");
  
      if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
    }
  
    return isValid;
  }
  
  function addError(input, message) {
    input.classList.remove("border-dark");
    input.classList.add("is-invalid", "border-danger");
  
    const feedback = document.createElement("div");
    feedback.className = "invalid-feedback d-block text-danger";
    feedback.textContent = message;
    input.insertAdjacentElement("afterend", feedback);
  }
  