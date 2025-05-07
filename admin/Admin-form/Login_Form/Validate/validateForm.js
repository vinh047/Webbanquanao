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

  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

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

  const emailField = form.querySelector('[name="email"]');
  const passwordField = form.querySelector('[name="pswd"]');

  const email = emailField.value.trim();
  const password = passwordField.value.trim();

  if (!email) showError(emailField, "Vui lòng nhập email");
  else if (!emailRegex.test(email)) showError(emailField, "Email không hợp lệ");

  if (!password) showError(passwordField, "Vui lòng nhập mật khẩu");

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
