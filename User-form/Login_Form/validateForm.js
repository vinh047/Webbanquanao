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

  //focus vào trường đầu tiên không hợp lệ
  

  if (trangthai === "dangky") {
    const usernameField = form.querySelector('[name="username"]');
    const emailField = form.querySelector('[name="email"]');
    const passwordField = form.querySelector('[name="pswd"]');
    const phoneField = form.querySelector('[name="sdt"]');
    
    const username = usernameField.value.trim();
    const email = emailField.value.trim();
    const password = passwordField.value.trim();
    const sdt = phoneField.value.trim();
    

    if (!username) showError(usernameField, "Vui lòng nhập tên người dùng");
    else if (!usernameRegex.test(username)) showError(usernameField, "Tên người dùng có độ dài từ 4 đến 20 ký tự, không chứa ký tự đặc biệt");

    if (!email) showError(emailField, "Vui lòng nhập email");
    else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

    if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");
    else if (!passwordRegex.test(password)) showError(passwordField, "Mật khẩu tối thiểu 8 ký tự bao gồm số, chữ hoa, chữ thường, ký tự đặc biệt");

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
  }

  if (firstInvalidField) {
    firstInvalidField.focus();
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
