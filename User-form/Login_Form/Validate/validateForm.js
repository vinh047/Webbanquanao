function validateForm() {
  const form = document.getElementById("mainformmainform");
  let isValid = true;
  let firstInvalidField = null;

  form.querySelectorAll(".form-control").forEach(input => {
    input.classList.remove("is-invalid", "border-danger");
    input.classList.add("border-dark");

    const next = input.nextElementSibling;
    if (next && next.classList.contains("invalid-feedback")) {
      next.remove();
    }
  });

  const usernameRegex = /^[a-zA-Z0-9_]{4,20}$/;
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}$/;
  const phoneRegex = /^0\d{9}$/;

  function showError(field, message) {
    field.classList.remove("border-dark");
    field.classList.add("is-invalid", "border-danger");

    const feedback = document.createElement("div");
    feedback.className = "invalid-feedback d-block text-danger";
    feedback.textContent = message;
    field.insertAdjacentElement("afterend", feedback);
    isValid = false;

    if (!firstInvalidField) {
      firstInvalidField = field;
    }
  }

  if (trangthai === "dangky") {
    const usernameField = form.querySelector('[name="username"]');
    const emailField = form.querySelector('[name="email"]');
    const passwordField = form.querySelector('[name="pswd"]');
    const passwordConfirmField = form.querySelector('[name="pswd-confirm"]');
    const phoneField = form.querySelector('[name="sdt"]');

    const username = usernameField.value.trim();
    const email = emailField.value.trim();
    const password = passwordField.value.trim();
    const passwordConfirm = passwordConfirmField.value.trim();
    const sdt = phoneField.value.trim();

    if (!username) showError(usernameField, "Vui lòng nhập tên người dùng");
    else if (!usernameRegex.test(username)) showError(usernameField, "Tên người dùng phải từ 4–20 ký tự, không chứa ký tự đặc biệt");

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

    if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
    else if (!passwordRegex.test(password)) showError(passwordField, "Mật khẩu cần ≥8 ký tự, gồm số, chữ hoa, thường, ký tự đặc biệt");

    if (!passwordConfirm) showError(passwordConfirmField, "Vui lòng nhập lại mật khẩu");
    else if (password !== passwordConfirm) showError(passwordConfirmField, "Mật khẩu không khớp");

    if (!sdt) showError(phoneField, "Vui lòng nhập số điện thoại");
    else if (!phoneRegex.test(sdt)) showError(phoneField, "Số điện thoại không hợp lệ");
  } 
  else if (trangthai === "dangnhap") {
    const emailField = form.querySelector('[name="email"]');
    const passwordField = form.querySelector('[name="pswd"]');

    const email = emailField.value.trim();
    const password = passwordField.value.trim();

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

    if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
  } 
  else if (trangthai === "quenmatkhau") {
    const emailField = form.querySelector('[name="email"]');
    const email = emailField.value.trim();

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");
  } else if (trangthai === "nhapotp") {
    const otpField = form.querySelector('[name="otp"]');
    const passField = form.querySelector('[name="new_password"]');
    const confirmField = form.querySelector('[name="confirm_password"]');
  
    const otp = otpField.value.trim();
    const pass = passField.value.trim();
    const confirm = confirmField.value.trim();
  
    if (!otp) showError(otpField, "Vui lòng nhập mã OTP");
    if (!pass) showError(passField, "Vui lòng nhập mật khẩu mới");
    if (!confirm) showError(confirmField, "Vui lòng nhập lại mật khẩu");
    else if (pass !== confirm) showError(confirmField, "Mật khẩu không khớp");
  }
  

  if (firstInvalidField) firstInvalidField.focus();
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
