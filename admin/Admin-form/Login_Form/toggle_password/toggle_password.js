document.addEventListener("DOMContentLoaded", () => {
    document.body.addEventListener("click", function (e) {
      const toggle = e.target.closest(".toggle-password");
      if (!toggle) return;
  
      const icon = toggle.querySelector("i");
      const input = toggle.closest(".input-group").querySelector("input");
  
      if (input && icon) {
        const isHidden = input.type === "password";
        input.type = isHidden ? "text" : "password";
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
      }
    });
  });
  