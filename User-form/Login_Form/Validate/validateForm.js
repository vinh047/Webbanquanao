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

  const nameRegex = /^[\p{L}]+(?: [\p{L}]+)+$/u;
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
    const nameField = form.querySelector('[name="name"]');
    const emailField = form.querySelector('[name="email"]');
    const passwordField = form.querySelector('[name="pswd"]');
    const passwordConfirmField = form.querySelector('[name="pswd-confirm"]');
    const phoneField = form.querySelector('[name="sdt"]');

    const name = nameField.value.trim();
    const email = emailField.value.trim();
    const password = passwordField.value.trim();
    const passwordConfirm = passwordConfirmField.value.trim();
    const sdt = phoneField.value.trim();

    if (!name) showError(nameField, "Vui lòng nhập họ và tên");
    else if (!nameRegex.test(name)) showError(nameField, "Vui lòng nhập thông tin hợp lệ (Họ và tên)");

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

    if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
    else if (!passwordRegex.test(password)) showError(passwordField, "Mật khẩu cần ≥8 ký tự, gồm số, chữ hoa, thường, ký tự đặc biệt");

    if (!passwordConfirm) showError(passwordConfirmField, "Vui lòng nhập lại mật khẩu");
    else if (password !== passwordConfirm) showError(passwordConfirmField, "Mật khẩu không khớp");

    if (!sdt) showError(phoneField, "Vui lòng nhập số điện thoại");
    else if (!phoneRegex.test(sdt)) showError(phoneField, "Số điện thoại không hợp lệ");
  } else if (trangthai === "dangnhap") {
    const emailField = form.querySelector('[name="email"]');
    const passwordField = form.querySelector('[name="pswd"]');

    const email = emailField.value.trim();
    const password = passwordField.value.trim();

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

    if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
  } else if (trangthai === "quenmatkhau") {
    const emailField = form.querySelector('[name="email"]');
    const email = emailField.value.trim();

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");
  } else if (trangthai === "nhapotp") {
    const otpField = form.querySelector('[name="otp"]');
    const otp = otpField.value.trim();

    if (!otp) showError(otpField, "Vui lòng nhập mã OTP");
  } else if (trangthai === "resetpswd") {
    const passField = form.querySelector('[name="new_password"]');
    const confirmField = form.querySelector('[name="confirm_password"]');

    const pass = passField.value.trim();
    const confirm = confirmField.value.trim();

    if (!pass) showError(passField, "Vui lòng nhập mật khẩu mới");
    if (!confirm) showError(confirmField, "Vui lòng nhập lại mật khẩu");
    else if (!passwordRegex.test(pass))
      showError(passField, "Mật khẩu cần ≥8 ký tự, gồm số, chữ hoa, thường, ký tự đặc biệt");
    else if (pass !== confirm)
      showError(confirmField, "Mật khẩu không khớp");
  }

  if (firstInvalidField) firstInvalidField.focus();
  return isValid;
}

function addError(input, message) {
  if (!input) return;
  input.classList.remove("border-dark");
  input.classList.add("is-invalid", "border-danger");

  const feedback = document.createElement("div");
  feedback.className = "invalid-feedback d-block text-danger";
  feedback.textContent = message;
  input.insertAdjacentElement("afterend", feedback);
}